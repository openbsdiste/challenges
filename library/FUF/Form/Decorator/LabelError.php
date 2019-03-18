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

    class FUF_Form_Decorator_LabelError extends Zend_Form_Decorator_Label {

        public function getClass () {
            $class   = '';
            $decoratorClass = $this->getOption ('class');
            if (!empty ($decoratorClass)) {
                $class .= ' ' . $decoratorClass;
            }
            return $class;
        }

        public function render ($content) {

            $element = $this->getElement ();
            $view    = $element->getView ();
            $errors = $element->getMessages ();
            $avant = '';
            $apres = '';
            if ((null !== $view) && !empty ($errors)) {
                $avant = '<p id="err' . $element->getName () . '" class="errorField"><strong>' . implode ('<br />', $errors) . '</strong></p>';
//                $apres = '<p class="formHint">' . implode ('<br />', $errors) . '</p>';
            }
            $contenu = parent::render ($content);
            return $avant . $contenu . $apres;
        }
    }