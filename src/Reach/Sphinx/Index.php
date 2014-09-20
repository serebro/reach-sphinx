<?php

namespace Reach\Sphinx;

use Reach\Service\Container as ServiceContainer;
use XMLWriter;

class Index {

    public static $default_connection_name = 'sphinx';


    public static function getIndexName()
    {
        return str_replace(['\\', '/', '-'], '_', strtolower(get_called_class()));
    }

    /**
     * return [
     *      'title' => ['type' => 'string', 'weight' => 100],
     *      'description' => ['type' => 'string', 'weight' => 50],
     *      'views' => ['type' => 'int', 'weight' => 70]
     * ]
     * @return array
     */
    public static function attributes()
    {
        return [];
    }

    protected function getXmlSchema(XMLWriter $xmlWriter)
    {

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
            throw new \InvalidArgumentException('Invalid argument');
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