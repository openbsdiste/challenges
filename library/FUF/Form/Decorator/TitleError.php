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

    class FUF_Form_Decorator_TitleError extends Zend_Form_Decorator_Abstract {

        protected function _recurseForm (Zend_Form $form) {
            $content = '';
            $errors  = $form->getMessages ();
            if ($form instanceof Zend_Form_SubForm) {
                $name = $form->getName ();
                if ((1 == count ($errors)) && array_key_exists ($name, $errors)) {
                    $errors = $errors [$name];
                }
            }
            if (! empty ($errors)) {
                foreach (array_keys ($errors) as $name) {
                    $element = $form->$name;
                    if ($element instanceof Zend_Form_Element) {
                        $label = $element->getLabel ();
                        if (empty ($label)) $label = $element->getName ();
                        $content = '<li><a href="#err'
                            . $element->getName ()
                            . '" title="Aller vers erreur">'
                            . $label . '</a></li>'
                            . $content;
                    } elseif ($element instanceof Zend_Form) {
                        $content .= $this->_recurseForm ($element);
                    }
                }
            }
            return $content;
        }

        public function render ($content) {
            $contenu = $content;
            $form = $this->getElement ();
            if ($form instanceof Zend_Form) {
                $view = $form->getView ();
                if (null !== $view) {
                    $liste = $this->_recurseForm ($form);
                    if (! empty ($liste)) {
                        $contenu = "\n" . '<div id="errorMsg">';
                        $contenu .= '<h3>Erreur(s) de saisie</h3>';
                        $contenu .= "\n<ol>" . $liste . "</ol></div>\n" . $content;
                    }
                }
            }
            return $contenu;
        }
    }