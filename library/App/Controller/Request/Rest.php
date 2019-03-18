<?php
    class App_Controller_Request_Rest extends Zend_Controller_Request_Http {
        private $_error = false;
        
        public function setError ($code, $message) {
            $this->_error = new stdClass ();
            $this->_error->code = $code;
            $this->_error->message = $message;
            return $this;
        }
        
        public function getError () {
            return $this->_error;
        }
        
        public function hasError () {
            return ($this->_error !== false);
        }
        
        public function getMethod () {
            $pMethod = $this->getParam ('_method', false);
            $pHeader = $this->getHeader ('X-HTTP-Method-Override');
            $pRequest = $this->getServer ('REQUEST_METHOD');
            return ($pMethod) ? $pMethod : ($pHeader) ? $pHeader : $pRequest;
        }
        
        public function dispatchError ($code, $message) {
            $this->setError ($code, $message);
            $this->setControllerName ('error');
            $this->setActionName ('error');
            $this->setDispatched (true);
        }
    }
