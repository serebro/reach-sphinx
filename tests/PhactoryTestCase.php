<?php

use Phactory\Mongo\Phactory;
use Reach\Mongo\Connection;
use Reach\Mongo\ConnectionManager;

/**
 * Test Case Base Class for using Phactory *
 */
abstract class PhactoryTestCase extends \PHPUnit_Framework_TestCase
{

    protected static $connection;

    protected static $phactory;

    /** @var  array */
    protected static $config;


    public static function setUpBeforeClass()
    {
        self::$config = [
        ];

    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }
}