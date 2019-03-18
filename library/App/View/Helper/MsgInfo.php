<?php
    class App_View_Helper_MsgInfo extends Zend_View_Helper_Abstract {
        public function msgInfo ($message) {
            $txt = '<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">' . "\n";
            $txt .= "<p>\n";
            $txt .= '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>' . "\n";
            $txt .= "$message\n";
            $txt .= "</p>\n";
            $txt .= "</div>\n";
            return $txt;
        }
    }