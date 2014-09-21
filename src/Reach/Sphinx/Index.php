<?php

namespace Reach\Sphinx;

use InvalidArgumentException;
use Reach\Service\Container as ServiceContainer;
use XMLWriter;

class Index {

    const XML_OUTPUT_MEMORY = 1;
    const XML_OUTPUT_STDOUT = 2;

    public static $default_connection_name = 'sphinx';


    public static function getIndexName()
    {
        return str_replace(['\\', '/', '-'], '_', strtolower(get_called_class()));
    }

    /**
     * return [
     *      'title'         => ['field' => true, 'weight' => 100],
     *      'description'   => ['field' => true, 'weight' => 50],
     *      'code'          => ['type' => 'string', 'weight' => 20]
     *      'views'         => ['type' => 'int', 'bits' => 32, 'default' => 0, 'weight' => 70]
     * ]
     * @return array
     */
    public static function attributes()
    {
        return [];
    }

    /**
     * @param string $connection_name
     * @return mixed
     */
    public static function getConnection($connection_name = null)
    {
        if (empty($connection_name)) {
            $connection_name = self::$default_connection_name;
        }
        if (!ServiceContainer::has($connection_name)) {
            throw new InvalidArgumentException("Reach service \"$connection_name\" is not defined.");
        }

        return ServiceContainer::get($connection_name);
    }

    public static function query($criteria = null)
    {
        return new Query($criteria, get_called_class());
    }

    public function hydrate(array $data)
    {

    }
}
