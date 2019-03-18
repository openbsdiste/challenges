<?php
    /**
     * This file is part of FUF.
     *
     * FUF is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.

     * FUF is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with FUF.  If not, see <http://www.gnu.org/licenses/>.
     *
     * @author François Lecluse
     * @version 0.7.2
     * @license GPL v3 or later
     * @copyright (c)2008, FL
     *
     */

    class FUF_Helper_FormCkeditor extends Zend_View_Helper_FormTextarea {
        protected static $_instance = false;
        
        public $_ckpath = '/scripts/ckeditor/';
        public $_ckscript = 'ckeditor.js';

        protected function _addHeadScript () {
            self::$_instance = true;
            $this->view->headScript ()->appendFile ($this->_ckpath . $this->_ckscript);
        }
        
        public function setPath ($path) {
            $this->_ckpath = implode ('/', explode ('/', $path)) . '/';
            return $this;
        }
        
        public function setScript ($script) {
            $this->_ckscript = $script;
            return $this;
        }
        
        public function FormCkeditor ($name, $value = null, $attribs = null) {
            $info = $this->_getInfo ($name, $value, $attribs);
            extract ($info);
            (self::$_instance) || $this->_addHeadScript ();
            return $this->formTextarea ($name, $value, $attribs);
        }
    }