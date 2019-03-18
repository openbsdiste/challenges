<?php
    class App_View_Helper_MsgErreur extends Zend_View_Helper_Abstract {
        public function msgErreur ($message) {
            $txt = '<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">' . "\n";
            $txt .= "<p>\n";
            $txt .= '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>' . "\n";
            $txt .= "$message\n";
            $txt .= "</p>\n";
            $txt .= "</div>\n";
            return $txt;
        }
    }