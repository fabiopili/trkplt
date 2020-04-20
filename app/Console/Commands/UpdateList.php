<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\BlockList;
use App\Hostname;
use App\BlocklistHostname;

class UpdateList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateList {blocklist_id} {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a list based on a flat txt file with hostnames.';

    /**
     * Chunk size for batch processing
     *
     * @var int
     */
    protected $chunkSize = 1000;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

        // Init PHP Domain Parser
        // https://github.com/jeremykendall/php-domain-parser
        $this->DomainParser = new \Pdp\Manager(new \Pdp\Cache(), new \Pdp\CurlHttpClient());
        $this->DomainParserRules = $this->DomainParser->getRules();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $addedHostsNumber = 0;
        $deletedHostsNumber = 0;

        // Get and validate parameter
        $blocklist_id = (int)$this->argument('blocklist_id');

        if(!$blocklist_id){
            $this->error('List ID not specified');
            return;
        }

        $file = $this->argument('file');

        if(!$file){
            $this->error('File not specified');
            return;
        }

        // All good, let's process the list
        $blocklist = BlockList::where('id', '=', $blocklist_id)->first();

        // Did I find the list?
        if(!$blocklist){
            $this->error('List not found');
            return;
        }

        // Does the file exist?
        if(!file_exists($file)){
            $this->error('File not found');
            return;
        }

        $this->info('Updating list "' . $blocklist->name . '" with all entries on file ' . $file);

        // Fetching existing hosts for that list
        $existingHosts = $blocklist->hostnames->pluck('hostname');

        // Explode file
        // Keep both arrays as collections so we have access to a
        // pretty handy chunking method Laravel offers
        $newHosts = collect(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        // Cleanup
        // Ignore empty lines and comments
        $newHosts = $newHosts->map(function ($line, $key) {

            // Ignore whole commented lines
            if(substr($line,0,1)=='#'){
                return false;
            }

            // Strip comments after the hostname
            // (?:(^[\w|\d|\-|.]*))([\s\w]*#.*)
            if(strpos($line, '#')){
                return preg_replace('/(?:(^[\w|\d|\-|.]*))([\s\w]*#.*)/', "$1", $line);
            }

            // Ignore empty lines
            if(empty(trim($line))){
                return false;
            }

            return $line;

        // Remove false and empty
        })->filter();

        // We need a strategy here to avoid running a lot of write operations in sequence
        // Idea:
        // Get the whole list from the DB and compare both arrays.
        // Work only on the differences
        // Keep an eye on memory usage to make sure we won't have problems

        // Compare new and deleted by intersecting both arrays
        $addedHosts = $newHosts->diff($existingHosts);
        $deletedHosts = $existingHosts->diff($newHosts);

        // Remove deleted hosts
        Hostname::whereIn('hostname', $deletedHosts)->delete();

        // Count deleted
        $deletedHostsNumber = count($deletedHosts);

        // Return info
        $this->info('Deleted ' . $deletedHostsNumber . ' hostnames');

        // Insert new hosts
        // Chunk results to optimize insert performance
        foreach ($addedHosts->chunk($this->chunkSize) as $chunk){

            // Add blocklist id and format each result according to the DB schema
            $hostsToInsert = $chunk->map(function ($item, $key) use ($blocklist_id) {

                // Let's make sure we found a hostname to work
                if($item){

                    // Parse hostname
                    $domainData = $this->DomainParserRules->resolve($item);

                    return [
                        'hostname' => $item,
                        'domain' => $domainData->getRegistrableDomain() ?? $item,
                    ];

                } else {
                    return false;
                }

            });


            // Do I have any to insert?
            if(count($hostsToInsert->toArray()) > 0){

                // Insert chunk into DB
                $hostnameCount = Hostname::insertOrIgnore($hostsToInsert->toArray());

                // Get ID for all hostnames inserted on this chunk
                $hostnameIds = Hostname::whereIn('hostname', $hostsToInsert->pluck('hostname')->unique())->get()->pluck('id');

                // Create relationship between hostname and blocklist
                $hostnameBlocklistRelationship = [];
                foreach ($hostnameIds as $key => $hostname_id) {

                    $hostnameBlocklistRelationship[] = [
                        'hostname_id' => $hostname_id,
                        'block_list_id' => $blocklist_id
                    ];

                }

            }

            // Save hostname and blocklist relationships
            BlocklistHostname::insertOrIgnore($hostnameBlocklistRelationship);

            // Count added
            $addedHostsNumber += count($hostsToInsert);

        }

        // Return info
        $this->info('Added ' . $addedHostsNumber . ' hostnames');
        $this->info('Processing complete');

    }

}
