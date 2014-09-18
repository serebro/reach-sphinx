<?php

namespace Models\Sphinx;

class Video extends Base
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