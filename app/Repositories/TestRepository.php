<?php

namespace App\Repositories;

use App\Interfaces\TestInterface;
use App\Test;

class TestRepository implements TestInterface
{

    /**
     * Returns a simplified index of tests
     * @return mixed[]
     */
    public function index(array $ids = null) {

        // Validation
        if(!$ids){
            return false;
        }

        // Get tests from the database
        $tests = Test::whereIn('id', $ids)->orderBy('created_at', 'desc')->get(['id','created_at','url','nu_blocked','nu_allowed','is_processing','is_done','is_error']);

        if(!$tests){
            return false;
        }

        // Return objefct
        return $tests;
    }

    /**
     * Returns a list of tests
     * @return mixed[]
     */
    public function get(array $ids = null) {

        // Validation
        if(!$ids){
            return false;
        }

        // Get tests from the database
        $tests = Test::whereIn('id', $ids)->orderBy('created_at', 'desc')->get();

        if(!$tests){
            return false;
        }

        // Return objefct
        return $tests;
    }

    /**
     * Create a new test
     * @return Test object
     */
    public function create($params) {

        // Validation
        if(!isset($params['url']) || !isset($params['ip'])){
            return false;
        }

        // Create test entry on the database
        // with bare data
        $test = new Test;
        $test->url = $params['url'];
        $test->ip = $params['ip'];
        $test->is_processing = true;

        // Save new test
        $test->save();

        if(!$test){
            return false;
        }

        // Return new Test object
        return $test;

    }

}