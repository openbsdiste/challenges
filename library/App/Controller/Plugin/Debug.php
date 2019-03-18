<?php
    class App_Controller_Plugin_Debug extends ZFDebug_Controller_Plugin_Debug {
        protected function _output ($html) {
            $response = $this->getResponse ();
            $response->setBody (str_ireplace ('</head>', $this->_headerOutput () . '</head>', $response->getBody ()));
            $response->setBody (str_ireplace ('</body>', '<div id="ZFDebug_debug">' . $html . '</div></body>', $response->getBody ()));
        }
    }