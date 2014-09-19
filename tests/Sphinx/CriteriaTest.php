<?php

namespace Sphinx;

class CriteriaTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $criteria = [
            'select' => '*'
        ];

        $criteria = new \Reach\Sphinx\Criteria($criteria);
        $this->assertEquals(['select' => '*'], $criteria->asArray());
    }
}