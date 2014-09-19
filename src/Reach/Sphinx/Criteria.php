<?php

namespace Reach\Sphinx;

use Exception;
use InvalidArgumentException;

class Criteria
{

    protected $select = [];

    protected $max_query_time = 0;

    protected $limit = null;

    protected $offset = 0;

    protected $max_matches = 1000;

    protected $cut_off = 0;

    protected $match_mode = SPH_MATCH_ANY;

    private static $_match_modes = [
        SPH_MATCH_ALL,
        SPH_MATCH_ANY,
        SPH_MATCH_PHRASE,
        SPH_MATCH_BOOLEAN,
        SPH_MATCH_EXTENDED,
        SPH_MATCH_FULLSCAN,
        SPH_MATCH_EXTENDED2,
    ];

    private static $_sort_modes = [
        SPH_SORT_RELEVANCE,
        SPH_SORT_ATTR_DESC,
        SPH_SORT_ATTR_ASC,
        SPH_SORT_TIME_SEGMENTS,
        SPH_SORT_EXTENDED,
        SPH_SORT_EXPR,
    ];

    private static $_group_funcs = [
        SPH_GROUPBY_DAY,
        SPH_GROUPBY_WEEK,
        SPH_GROUPBY_MONTH,
        SPH_GROUPBY_YEAR,
        SPH_GROUPBY_ATTR,
        SPH_GROUPBY_ATTRPAIR,
    ];

    protected $criteria = [];

    //protected $ranker;
    //
    //protected $rank_expr;
    //
    //protected $sort_mode;
    //
    //protected $sort_by;
    //
    //protected $field_weights = [];
    //
    //protected $index_weights = [];
    //
    //protected $id_range = [];
    //
    //protected $filter = [];
    //
    //protected $filter_range = [];
    //
    //protected $filter_float_range = [];
    //
    //protected $geo_anchor = [];
    //
    //protected $filter_string = [];
    //
    //protected $group_by = [];
    //
    //protected $group_distinct;

    public function __construct($criteria = null)
    {
        if (empty($criteria)) {
            return;
        }

        if (!$criteria instanceof CriteriaInterface && !is_array($criteria)) {
            throw new Exception('Invalid parameter type.');
        }

        if ($criteria instanceof CriteriaInterface) {
            $criteria = $criteria->asArray();
        }

        $this->criteria = $criteria;
    }

    /**
     * @param $clause
     * @return $this
     */
    public function select($clause)
    {
        if (!is_array($clause) && !is_string($clause)) {
            throw new InvalidArgumentException('Invalid argument');
        }

        if (is_array($clause)) {
            $this->criteria['select'][] = current($clause) . ' as ' . key($clause);
        } else {
            $this->criteria['select'][] = $clause;
        }

        return $this;
    }

    public function add()
    {

        return $this;
    }

    public function addOr()
    {

        return $this;
    }

    /**
     * @param array $sort
     * @return $this
     */
    public function sort(array $sort)
    {

        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        if (!is_int($offset)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['offset'] = $offset;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        if (!is_int($limit)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['limit'] = $limit;
        return $this;
    }

    /**
     * @param int $millis
     * @return $this
     */
    public function maxQueryTime($millis)
    {
        if (!is_int($millis)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['max_query_time'] = $millis;
        return $this;
    }

    /**
     * @param int $max_matches
     * @return $this
     */
    public function maxMatches($max_matches)
    {
        if (!is_int($max_matches)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['max_matches'] = $max_matches;
        return $this;
    }

    /**
     * @param int $cut_off
     * @return $this
     */
    public function cutOff($cut_off)
    {
        if (!is_int($cut_off)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['cut_off'] = $cut_off;
        return $this;
    }

    /**
     * @param int $mode
     * @return $this
     */
    public function matchMode($mode)
    {
        if (!in_array($mode, self::$_match_modes)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['match_mode'] = $mode;
        return $this;
    }

    /**
     * @param        $ranker
     * @param string $rank_expr
     * @return $this
     */
    public function rankingMode($ranker, $rank_expr = '')
    {
        $this->criteria['ranker'] = $ranker;
        $this->criteria['rank_expr'] = $rank_expr;
        return $this;
    }

    /**
     * @param        $mode
     * @param string $sort_by
     * @return $this
     */
    public function sortMode($mode, $sort_by = '')
    {
        if (!in_array($mode, self::$_sort_modes)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['sort_mode'] = $mode;
        $this->criteria['sort_by'] = $sort_by;
        return $this;
    }

    /**
     * @param array $weights
     * @return $this
     */
    public function fieldWeights(array $weights)
    {
        $this->criteria['field_weights'] = $weights;
        return $this;
    }

    /**
     * @param array $weights
     * @return $this
     */
    public function indexWeights(array $weights)
    {
        $this->criteria['index_weights'] = $weights;
        return $this;
    }

    /**
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function idRange($min, $max)
    {
        if (!is_int($min) || !is_int($max)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['id_range'] = [$min, $max];
        return $this;
    }

    /**
     * @param string $attribute
     * @param int[]  $values
     * @param bool   $exclude
     * @return $this
     */
    public function filter($attribute, $values, $exclude = false)
    {
        if (!is_string($attribute) || !is_array($values) || !count($values)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['filter'][] = [$attribute, $values, $exclude];
        return $this;
    }

    /**
     * @param string $attribute
     * @param int    $min
     * @param int    $max
     * @param bool   $exclude
     * @return $this
     */
    public function filterRange($attribute, $min, $max, $exclude = false)
    {
        if (!is_string($attribute) || !is_int($min) || !is_int($max)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['filter_range'][] = [$attribute, $min, $max, $exclude];
        return $this;
    }

    /**
     * @param string $attribute
     * @param float  $min
     * @param float  $max
     * @param bool   $exclude
     * @return $this
     */
    public function filterFloatRange($attribute, $min, $max, $exclude = false)
    {
        if (!is_string($attribute) || !is_int($min) || !is_int($max)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['filter_float_range'][] = [$attribute, $min, $max, $exclude];
        return $this;
    }

    public function filterString($attribute, $value, $exclude = false)
    {
        if (!is_string($attribute) || !is_string($value)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['filter_string'][] = [$attribute, $value, $exclude];
        return $this;
    }

    /**
     * @param string $attr_lat
     * @param string $attr_long
     * @param float  $lat
     * @param float  $long
     * @return $this
     */
    public function geoAnchor($attr_lat, $attr_long, $lat, $long)
    {
        if (!is_string($attr_lat) || !is_string($attr_long) || !is_float($lat) || !is_float($long)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['geo_anchor'] = [$attr_lat, $attr_long, $lat, $long];
        return $this;
    }

    /**
     * @param string $attribute
     * @param int    $func
     * @param string $group_sort
     * @return $this
     */
    public function groupBy($attribute, $func, $group_sort = '@group desc')
    {
        if (!is_string($attribute) || !is_string($group_sort)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        if (!in_array($func, self::$_group_funcs)) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->criteria['group_by'] = [$attribute, $func, $group_sort];
        return $this;
    }

    public function groupDistinct($attribute)
    {
        $this->criteria['group_distinct'] = $attribute;
        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->criteria;
    }
}
