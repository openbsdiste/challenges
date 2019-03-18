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

    class FUF_Helper_FormCkfinderImage extends Zend_View_Helper_FormElement {
        protected static $_instance = false;
        
        public $_ckpath = '/scripts/ckfinder/';
        public $_ckscript = 'ckfinder.js';
        public $_width = '60 px';
        public $_height = '60 px';
        
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

        public function setWidth ($width) {
        	$this->_width = $width;
        	return $this;
        }
        
        public function setHeight ($height) {
        	$this->_height = $height;
        	return $this;
        }
        
        public function formCkfinderImage ($name, $value = null, $attribs = null) {
            $info = $this->_getInfo ($name, $value, $attribs);
            extract ($info);
            (self::$_instance) || $this->_addHeadScript ();

            $endTag = ' />';
	        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
	            $endTag= '>';
	        }

	        $onclick = '';
	        if (! $disable) {
	        	$onclick = ' onclick="javascript:runCkFinder_' . $this->view->escape ($id) . '();"';
	        }

	        if (empty ($attribs ['width'])) $attribs ['width'] = (int) $this->_width;
	        if (empty ($attribs['height'])) $attribs ['height'] = (int) $this->_height;
	        
            $xhtml = '<input type="hidden" name="' . $this->view->escape ($name) . '"'
                . ' id="' . $this->view->escape ($id) . '"'
                . ' value="' . $this->view->escape ($value) . '" ' . $endTag
                . ' <img name="img_' . $this->view->escape ($name) . '"'
                . ' id="img_' . $this->view->escape ($id) . '"'
                . ' src="' . $this->view->escape ($value) . '"'
                . ' alt="' . $this->view->escape ($value) . '"'
                . $onclick
                . $this->_htmlAttribs($attribs) . $endTag;
                
            return $xhtml;
        }
    }