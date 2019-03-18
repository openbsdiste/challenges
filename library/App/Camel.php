<?php
    class App_Camel {
        static public function camelize ($input, $ucfirst = false) {
            $value = preg_replace ("/([_-\s]?([a-z0-9]+))/e", "ucwords('\\2')", $input);
            return ($ucfirst ? strtoupper ($value [0]) : strtolower ($value [0])) + substr ($value, 1);
        }
        
        static public function uncamelize ($input) {
            $matches = array ();
            preg_match_all ('/([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)/', $input, $matches);
            $ret = $matches [0];
            foreach ($ret as &$match) {
                $match = ($match == strtoupper($match)) ? strtolower ($match) : lcfirst ($match);
            }
            return implode ('_', $ret);
        }
    }