<?php

namespace Reach\Sphinx;

use Reach\ConnectionInterface;

class Connection implements ConnectionInterface
{

    /** @var \SphinxClient */
    private $_sphinx;


    public function __construct(array $config)
    {
        $this->_sphinx = new \SphinxClient();
        if (isset($config['host'])) {
            if (!isset($config['host'])) {
                $config['port'] = 9312;
            }
            $this->_sphinx->SetServer($config['host'], $config['port']);
        }

        if (isset($config['retries'])) {
            if (!isset($config['delay'])) {
                $config['delay'] = 0;
            }
            $this->_sphinx->SetRetries($config['retries'], $config['delay']);
        }

        if (isset($config['timeout'])) {
            $this->_sphinx->SetConnectTimeout($config['timeout']);
        }
    }

    public function isConnectError()
    {
        return $this->_sphinx->IsConnectError();
    }

    public function getSphinxClient()
    {
        return $this->_sphinx;
    }

    public function close()
    {
        $this->_sphinx->Close();
    }
}
