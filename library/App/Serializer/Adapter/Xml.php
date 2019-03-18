<?php
    class App_Serializer_Adapter_Xml extends Zend_Serializer_Adapter_AdapterAbstract {
        protected $_options = array (
            'rootNode' => 'response',
        );

        public function serialize ($value, array $opts = array ()) {
            $opts = $opts + $this->_options;
            try {
                $dom = new DOMDocument;
                $root = $dom->appendChild ($dom->createElement ($opts ['rootNode']));
                $this->createNodes ($dom, $value, $root, false);
                return $dom->saveXml ();
            } catch (Exception $e) {
                require_once 'Zend/Serializer/Exception.php';
                throw new Zend_Serializer_Exception ('Serialization failed', 0, $e);
            }
        }

        public function unserialize ($xml, array $opts = array ()) {
            try {
                $json = Zend_Json::fromXml ($xml);
                return (array) Zend_Json::decode ($json, Zend_Json::TYPE_OBJECT);
            } catch (Exception $e) {
                require_once 'Zend/Serializer/Exception.php';
                throw new Zend_Serializer_Exception ('Unserialization failed by previous error', 0, $e);
            }
        }

        private function createNodes ($dom, $data, &$parent) {
            switch (gettype ($data)) {
                case 'string':
                case 'integer':
                case 'double':
                    $parent->appendChild ($dom->createTextNode ($data));
                    break;
                case 'boolean':
                    switch ($data) {
                        case true:
                            $value = 'true';
                            break;
                        case false:
                            $value = 'false';
                            break;
                    }
                    $parent->appendChild ($dom->createTextNode ($value));
                    break;
                case 'object':
                case 'array':
                    foreach ($data as $key => $value) {
                        if (is_object ($value) and $value instanceOf DOMDocument and !empty ($value->firstChild)) {
                            $node = $dom->importNode ($value->firstChild, true);
                            $parent->appendChild ($node);
                        } else {
                            $attributes = null;
                            if (is_object ($value) and $value instanceOf SimpleXMLElement) {
                                $attributes = $value->attributes (); 
                                $value = (array) $value;
                            }
                            if ($key[0] !== '@') {
                                if (gettype ($value) == 'array' and !is_numeric ($key)) {
                                    $child = $parent->appendChild ($dom->createElement ($key));
                                    if ($attributes) {
                                        foreach ($attributes as $attrKey => $attrValue) {
                                            $child->setAttribute ($attrKey, $attrValue);
                                        }
                                    }
                                    $this->createNodes ($dom, $value, $child);
                                } else {
                                    if (is_numeric ($key)) {
                                        $key = sprintf ('%s', $this->depluralize ($parent->tagName));
                                    }
                                    $child = $parent->appendChild ($dom->createElement ($key));
                                    if ($attributes) {
                                        foreach ($attributes as $attrKey => $attrValue) {
                                            $child->setAttribute ($attrKey, $attrValue);
                                        }
                                    }
                                    $this->createNodes ($dom, $value, $child);
                                }
                            }
                        }
                    }
                    break;
            }
        }

        private function depluralize ($word) {
            $rules = array (
                'ss' => false,
                'os' => 'o',
                'ies' => 'y',
                'xes' => 'x',
                'oes' => 'o',
                'ies' => 'y',
                'ves' => 'f',
                's' => null
            );
            foreach (array_keys ($rules) as $key) {
                if (substr ($word, (strlen ($key) * -1)) != $key) {
                    continue;
                }
               if ($key === false) {
                    return $word;
                }
                return substr ($word, 0, strlen ($word) - strlen ($key)) . $rules [$key];
            }
            return $word;
        }
    }
