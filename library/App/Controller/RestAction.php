<?php
    abstract class App_Controller_RestAction extends Zend_Controller_Action {
        abstract public function indexAction ();
        abstract public function getAction ();
        abstract public function postAction ();
        abstract public function putAction ();
        abstract public function deleteAction ();

        public function headAction () {
            $this->_forward ('get');
        }

        public function optionsAction () {
            $actions = array ();
            $class = new ReflectionObject ($this);
            $methods = $class->getMethods (ReflectionMethod::IS_PUBLIC);
            foreach ($methods as &$method) {
                $name = strtoupper ($method->name);

                if (substr ($name, -6) == 'ACTION' && $name != 'INDEXACTION') {
                    $actions [$name] = str_replace ('ACTION', null, $name);
                }
            }
            $this->_response->setBody (null);
            $this->_response->setHeader ('Allow', implode (', ', $actions));
            $this->_response->ok ();
        }

        public function ieHack () {
            /* Fucking bad hack to prevent fucking non supported multipart json forms with IE ! */
            if (! $this->_request->isXmlHttpRequest ()) {
                echo json_encode (array ('data' => $this->view->data));
                die ();
            }
        }
    }
