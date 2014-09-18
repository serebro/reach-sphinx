<?php

namespace Models\Sphinx;

class Query extends Criteria
{

    public function all()
    {
        return new ResultSet();
    }
}