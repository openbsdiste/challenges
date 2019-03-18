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

    class FUF_Form_Decorator_CkEditor extends Zend_Form_Decorator_Abstract {

        public function render ($content) {
            $xhtml = '<script type="text/javascript">CKEDITOR.replace("' . $this->_element->getId () . '" ';
            if (! is_null ($this->_options) && is_array ($this->_options) && sizeof ($this->_options)) {
            	$xhtml .= ', {';
            	foreach ($this->_options as $k => $v) $xhtml .= "'$k' : '$v',";
            	$xhtml .= '}';
            }
            $xhtml .= ');</script>';
            return $content . $xhtml;
        }
    }