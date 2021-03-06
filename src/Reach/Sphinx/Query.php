<?php

namespace Reach\Sphinx;

use InvalidArgumentException;
use SphinxClient;

class Query extends Criteria
{

    private $_connection_name = 'sphinx';

    protected $queries = [];


    public function __construct($criteria = null, $hydrate_class, $connection_name = null)
    {
        if (empty($hydrate_class)) {
            throw new InvalidArgumentException('Invalid argument');
        }

        $this->_hydrate_class = $hydrate_class;
        if ($connection_name) {
            $this->_connection_name = $connection_name;
        }

        parent::__construct($criteria);
    }

    public function setConnectionName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new InvalidArgumentException('Invalid argument');
        }

        $this->_connection_name = $name;
    }

    public function getConnectionName()
    {
        return $this->_connection_name;
    }

    public function one($connection_name = null)
    {
        return $this->limit(1)->all($connection_name)->first();
    }

    public function all($connection_name = null)
    {
        if ($connection_name) {
            $this->_connection_name = $connection_name;
        }

        if (!isset($this->criteria['text'])) {
            $this->criteria['text'] = '';
        }

        if (!isset($this->criteria['comment'])) {
            $this->criteria['comment'] = '';
        }

        if (isset($this->criteria['fields']) && count($this->criteria['fields'])) {
            $fields = join(',', $this->criteria['fields']);
            $fields = " @($fields) ";
        } else {
            $fields = '';
        }

        $class = $this->_hydrate_class;

        /** @var \Reach\Sphinx\Connection $connection */
        $connection = $class::getConnection($this->_connection_name);

        /** @var SphinxClient $sphinx */
        $sphinx = $connection->getSphinxClient();
        $sphinx = $this->build($class, $sphinx);
        $text = $sphinx->escapeString($this->criteria['text']);
        $sphinx->addQuery($fields . $text, $class::getIndexName(), $this->criteria['comment']);
        $results = $sphinx->runQueries();
        $sphinx->resetFilters();

        if (!is_array($results)) {
            $results[0]['error'] = $sphinx->getLastError();
            $results[0]['warning'] = $sphinx->getLastWarning();
        }

        return new ResultSet($results[0]);
    }

    protected function build($class, SphinxClient $sphinx)
    {
        if (!isset($this->criteria['setSelect'])) {
            $this->select('*');
        }

        if (!isset($this->criteria['setMatchMode'])) {
            if (empty($this->criteria['text'])) {
                $this->matchMode(SPH_MATCH_FULLSCAN);
                //$this->matchMode(SPH_MATCH_ANY);
            } else {
                $this->matchMode(SPH_MATCH_EXTENDED2);
            }
        }

        if (!isset($this->criteria['setSortMode'])) {
            if (empty($this->criteria['text'])) {
                $this->sortMode(SPH_SORT_EXTENDED);
            } else {
                $this->sortMode(SPH_SORT_RELEVANCE);
            }
        }

        if (!isset($this->criteria['setArrayResult'])) {
            $this->criteria['setArrayResult'] = true;
        }

        if (!isset($this->criteria['offset'])) {
            $this->offset(0);
        }

        if (!isset($this->criteria['max_matches'])) {
            $this->maxMatches(1000);
        }

        if (!isset($this->criteria['cut_off'])) {
            $this->cutOff(0);
        }

        // setLimits
        if (isset($this->criteria['limit'])) {
            $this->criteria['setLimits'] = [
                $this->criteria['offset'],
                $this->criteria['limit'],
                $this->criteria['max_matches'],
                $this->criteria['cut_off']
            ];
        }

        // setFieldWeights
        $weights = [];
        foreach ($class::attributes() as $attribute => $params) {
            if (isset($params['weight'])) {
                $weights[$attribute] = $params['weight'];
            }
        }
        if (count($weights)) {
            $sphinx->SetFieldWeights($weights);
        }

        $fn = ['setSelect', 'setFilter', 'setFilterRange', 'setFilterFloatRange', 'setFilterString'];
        $methods = get_class_methods('\SphinxClient');
        $exec = function ($sphinx, $method, $params) {
            if (!is_array($params)) {
                $params = [$params];
            }
            call_user_func_array([$sphinx, $method], $params);
        };

        foreach ($this->criteria as $method => $params) {
            if (in_array($method, $fn)) {
                foreach ($params as $_params) {
                    $exec($sphinx, $method, $_params);
                }
            } else if (in_array($method, $methods)) {
                $exec($sphinx, $method, $params);
            }
        }

        return $sphinx;
    }
}
