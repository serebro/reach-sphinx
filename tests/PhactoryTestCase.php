<?php


/**
 * Test Case Index Class for using Phactory *
 */
abstract class PhactoryTestCase extends \PHPUnit_Framework_TestCase
{

    /** @var  \Reach\Sphinx\Connection */
    protected static $connection;

    /** @var  array */
    protected static $config;


    public static function setUpBeforeClass()
    {
        self::$config = [
            'class' => '\Reach\Sphinx\Connection',
            'host'  => 'localhost',
            'port'  => 9312,
        ];
        \Reach\Service\Container::register('sphinx', self::$config);
        self::$connection = \Reach\Service\Container::get('sphinx');
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    }

    protected function tearDown()
    {
        self::$connection->close();
    }
}