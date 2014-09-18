<?php

namespace Models\Sphinx;

use XMLWriter;

class Base {

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

    public static function query($criteria = null)
    {
        return new Query($criteria);
    }
}
