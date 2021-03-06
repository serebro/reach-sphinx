<?php

namespace Sphinx;

use PhactoryTestCase;

class CriteriaTest extends PhactoryTestCase
{

    public function testCreate()
    {
        $criteria = new \Reach\Sphinx\Criteria();
        $this->assertEquals([], $criteria->asArray());

        $criteria = ['select' => '*'];
        $criteria = new \Reach\Sphinx\Criteria($criteria);
        $this->assertEquals(['select' => '*'], $criteria->asArray());

        $criteria2 = new \Reach\Sphinx\Criteria($criteria);
        $this->assertEquals(['select' => '*'], $criteria2->asArray());
    }

    public function testWhere()
    {
        $criteria = new \Reach\Sphinx\Criteria(['limit' => 2]);
        $query = \Model\Sphinx\TestIndex::query($criteria);
        $this->assertInstanceOf('\Reach\Sphinx\Query', $query);
        $resultSet = $query
            ->comment('test')
            ->search('game')
            ->addBetween('viewCount', 1, 1000)
            ->all();
        $this->assertInstanceOf('\Reach\Sphinx\ResultSet', $resultSet);
        $this->assertEquals(2, $resultSet->count());
    }
}
