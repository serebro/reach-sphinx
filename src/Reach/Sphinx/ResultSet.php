<?php

namespace Reach\Sphinx;

use Reach\ResultSet as BaseResultSet;

class ResultSet extends BaseResultSet
{

    protected $result;

    public function __construct(array $result)
    {
        if (!isset($result['matches'])) {
            $result['matches'] = [];
        }

        $this->result = $result;
        parent::__construct($result['matches']);
    }

    public function getError()
    {
        return $this->result['error'];
    }

    public function getWarning()
    {
        return $this->result['warning'];
    }

    public function getStatus()
    {
        return $this->result['status'];
    }

    public function getFields()
    {
        return $this->result['fields'];
    }

    public function getAttrs()
    {
        return $this->result['attrs'];
    }

    public function getTotal()
    {
        return $this->result['total'];
    }

    public function getTotalFound()
    {
        return $this->result['total_found'];
    }

    public function getTime()
    {
        return $this->result['time'];
    }

    public function getWords()
    {
        return $this->result['words'];
    }
}
 