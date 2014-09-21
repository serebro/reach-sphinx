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
     *      'title'         => ['type' => 'string', 'weight' => 100],
     *      'description'   => ['type' => 'string', 'weight' => 50],
     *      'views'         => ['type' => 'int', 'bits' => 32, 'default' => 0, 'weight' => 70]
     * ]
     * @return array
     */
    public static function attributes()
    {
        return [];
    }

    /**
     * @param string $attribute
     * @param mixed $item
     * @param array $attribute_params
     * @return mixed
     */
    public static function xmlPipePrepareAttribute($attribute, $item, $attribute_params)
    {
        return null;
    }

    /**
     * @param $items
     * @param $output_method
     * @return string
     */
    protected function getXmlPipe($items, $output_method, $build_schema = true) {
        if ($output_method !== self::XML_OUTPUT_MEMORY && $output_method !== self::XML_OUTPUT_STDOUT) {
            throw new InvalidArgumentException('Invalid argument "output_method"');
        }

        if (!is_array($items) || !($items instanceof \Traversable)) {
            throw new InvalidArgumentException('Invalid argument "items"');
        }

        $attributes = self::attributes();
        $xml = new XMLWriter();

        if ($output_method === self::XML_OUTPUT_MEMORY) {
            $xml->openMemory();
        } else if ($output_method === self::XML_OUTPUT_STDOUT) {
            $xml->openURI('php://output');
        }

        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('sphinx:docset');
        {
            if ($build_schema) {
                $xml->startElement('sphinx:schema');
                {
                    foreach($attributes as $attribute => $params) {
                        if (empty($params['type'])) {
                            continue;
                        }
                        $xml->startElement('sphinx:attr');
                        $xml->writeAttribute('name', $attribute);
                        $xml->writeAttribute('type', $params['type']);
                        if (!empty($params['bits'])) {
                            $xml->writeAttribute('bits', $params['bits']);
                        }
                        if (!empty($params['default'])) {
                            $xml->writeAttribute('default', $params['default']);
                        }
                        $xml->endElement(); // sphinx:attr
                    }
                }
                $xml->endElement(); // sphinx:schema
            }

            foreach ($items as $item) {
                $xml->startElement('sphinx:document');
                {
                    foreach($attributes as $attribute => $params) {
                        $id = self::xmlPipePrepareAttribute('id', $item, []);
                        $xml->writeAttribute('id', $id);

                        if ($attribute == 'id') {
                            continue;
                        }

                        $value = self::xmlPipePrepareAttribute($attribute, $item, $params);
                        if (is_null($value)) {
                            continue;
                        }
                        if (is_string($value)) {
                            $xml->startElement($attribute);
                            $xml->writeCData($value);
                            $xml->endElement();
                        } else {
                            $xml->writeElement($attribute, $value);
                        }
                    }

                    //$id = Crc64::format((string)$item['_id'], '%u');
                    //$xml->writeAttribute('id', $id);
                    ////$xml->writeAttribute('id', $fake_id++);
                    ////$xml->writeElement('title', $video['snippet']['title']);
                    //
                    //$xml->startElement('title');
                    //$xml->writeCData($item['snippet']['title']);
                    //$xml->endElement(); // title
                    //
                    //$xml->startElement('descr');
                    //$xml->writeCData(str_replace(["\n", "\r", "\t"], ' ', $item['snippet']['description']));
                    //$xml->endElement(); // descr
                    //
                    //$xml->writeElement('storage_id', $item['_id']);
                    //
                    //$xml->writeElement('viewCount', $item['stats']['viewCount']);
                    ////$xml->writeElement('published', $video['snippet']['publishedAt']->sec);
                }
                $xml->endElement(); // sphinx:document
            }
        }

        $xml->endElement(); // sphinx:docset
        $xml->endDocument();

        if ($output_method === self::XML_OUTPUT_MEMORY) {
            return $xml->outputMemory();
        } else if ($output_method === self::XML_OUTPUT_STDOUT) {
            $xml->flush();
        }
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
