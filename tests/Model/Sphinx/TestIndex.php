<?php

namespace Models\Sphinx;

use Reach\Sphinx\Index;

class TestIndex extends Index
{

    public static function getIndexName()
    {
        return 'video';
    }

    public function attributes()
    {
        return [
            'title'      => 'string',
            'descr'      => 'string',
            'storage_id' => 'string',
            'viewCount'  => 'int',
            'updated'    => 'timestamp',
            'published'  => 'timestamp',
        ];
    }
}