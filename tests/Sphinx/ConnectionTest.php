<?php

class ConnectionTest extends PhactoryTestCase
{

    public function testConnection()
    {
        $connection = \Model\Sphinx\TestIndex::getConnection();
        $this->assertInstanceOf('\Reach\Sphinx\Connection', $connection);
    }
}