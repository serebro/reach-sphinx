<?php

namespace Reach\Sphinx;

use InvalidArgumentException;
use Traversable;
use XMLWriter;

class XmlPipe
{

    const OUTPUT_MEMORY = 1;
    const OUTPUT_STDOUT = 2;

    /** @var XMLWriter */
    protected $xml;

    /** @var array */
    protected $attributes = [];

    protected $documents;

    protected $kill_list;


    public function __construct(array $attributes, $output_method = self::OUTPUT_MEMORY)
    {
        if (!count($attributes)) {
            throw new InvalidArgumentException('Invalid argument "attributes"');
        }

        if ($output_method !== self::OUTPUT_MEMORY && $output_method !== self::OUTPUT_STDOUT) {
            throw new InvalidArgumentException('Invalid argument "output_method"');
        }

        $this->xml = new XMLWriter();
        $this->attributes = $attributes;

        $this->_output_method = $output_method;
        if ($this->_output_method === self::OUTPUT_MEMORY) {
            $this->xml->openMemory();
        } else if ($this->_output_method === self::OUTPUT_STDOUT) {
            $this->xml->openURI(STDOUT);
        }
    }

    public function setDocuments($documents)
    {
        if (!is_array($documents) && !($documents instanceof Traversable)) {
            throw new InvalidArgumentException('Invalid argument "items"');
        }

        $this->documents = $documents;
        return $this;
    }

    public function setKillList($kill_list)
    {
        if (!is_array($kill_list) && !($kill_list instanceof Traversable)) {
            throw new InvalidArgumentException('Invalid argument "items"');
        }

        $this->kill_list = $kill_list;
        return $this;
    }

    /**
     * @param callable $fn_prepare_value(string $attribute, mixed $item, array $attr_params = null)
     * @return null|string or STDOUT
     */
    public function build($fn_prepare_value)
    {
        if (!is_callable($fn_prepare_value, true)) {
            throw new InvalidArgumentException('Invalid argument');
        }

        $this->xml->startDocument('1.0', 'UTF-8');
        $this->xml->startElement('sphinx:docset');
        {
            $this->xml->startElement('sphinx:schema');
            {
                foreach ($this->attributes as $attribute => $params) {
                    if (empty($params['type']) && empty($params['field'])) {
                        continue;
                    }

                    $element_type = $params['field'] ? 'field' : 'attr';

                    $this->xml->startElement("sphinx:$element_type");
                    $this->xml->writeAttribute('name', $attribute);
                    $this->xml->writeAttribute('type', $params['type']);
                    if (!empty($params['bits'])) {
                        $this->xml->writeAttribute('bits', $params['bits']);
                    }
                    if (!empty($params['default'])) {
                        $this->xml->writeAttribute('default', $params['default']);
                    }
                    $this->xml->endElement(); // sphinx:attr
                }
            }
            $this->xml->endElement(); // sphinx:schema

            if (count($this->documents)) {
                foreach ($this->documents as $item) {
                    $this->xml->startElement('sphinx:document');
                    {
                        $id = call_user_func_array($fn_prepare_value, ['id', $item]);
                        $this->xml->writeAttribute('id', $id);

                        foreach ($this->attributes as $attribute => $params) {
                            if ($attribute == 'id') {
                                continue;
                            }

                            $value = call_user_func_array($fn_prepare_value, [$attribute, $item, $params]);
                            if (is_null($value)) {
                                continue;
                            }
                            if (is_string($value)) {
                                $this->xml->startElement($attribute);
                                $this->xml->writeCData($value);
                                $this->xml->endElement();
                            } else {
                                $this->xml->writeElement($attribute, $value);
                            }
                        }
                    }
                    $this->xml->endElement(); // sphinx:document
                }
            }

            if (count($this->kill_list)) {
                $this->xml->startElement('sphinx:killlist');
                foreach ($this->kill_list as $item) {
                    $value = call_user_func_array($fn_prepare_value, ['id', $item]);
                    $this->xml->writeElement('id', $value);
                }
                $this->xml->endElement(); // sphinx:document
            }
        }

        $this->xml->endElement(); // sphinx:docset
        $this->xml->endDocument();

        return $this->close();
    }

    protected function close()
    {
        if ($this->_output_method === self::OUTPUT_MEMORY) {
            return $this->xml->outputMemory();
        } else if ($this->_output_method === self::OUTPUT_STDOUT) {
            $this->xml->flush();
        }

        return null;
    }
}
