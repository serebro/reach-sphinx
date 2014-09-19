<?php

namespace Reach\Sphinx;

use Reach\Service\Container as ServiceContainer;
use XMLWriter;

class Index {

    public static function getIndexName()
    {
        return str_replace(['\\', '/', '-'], '_', strtolower(get_called_class()));
    }

    public function attributes()
    {
        return [];
    }

    protected function getXmlSchema(XMLWriter $xmlWriter)
    {

    }

    public static function getConnection($connection_name = 'sphinx')
    {
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
