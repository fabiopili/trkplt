<?php

namespace App\Interfaces;

interface TestInterface
{

    /**
     * Returns a simplified index of tests
     * @return Test object
     */
    public function index(array $ids);

    /**
     * Returns a list of tests
     * @return Test object
     */
    public function get(array $ids);

    /**
     * Create a new test
     * @return Test object
     */
    public function create($params);

}