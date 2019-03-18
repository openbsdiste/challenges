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
    class FUF_Form extends Zend_Form {
        public static $_racine = "./configuration/";

        protected $_lesElements = array (
            'standards' => array (
                'noms' => array ('Button', 'Captcha', 'Checkbox', 'Hash', 'Multiselect', 'Password', 'Select', 'Text', 'Textarea', 'Xhtml'),
                'deco' => array (
                    'ViewHelper',
                    array ('Description', array ('placement' => 'APPEND', 'class' => 'formHint')),
                    array ('LabelError', array ('escape' => false, 'requiredPrefix' => '<em>*</em>')),
                    array (
                        'decorator' => array ('Holder' => 'HtmlTagPerso'),
                        'options' => array ('tag' => 'div', 'class' => 'ctrlHolder')
                    )
                ),
                'attr' => array (
                )
            ),
            'ckeditor' => array (
                'noms' => array ('Ckeditor'),
                'deco' => array (
                    'ViewHelper',
                    array ('Description', array ('placement' => 'APPEND', 'class' => 'formHint')),
                    array ('LabelError', array ('escape' => false, 'requiredPrefix' => '<em>*</em>')),
                    array (
                        'decorator' => array ('Holder' => 'HtmlTagPerso'),
                        'options' => array ('tag' => 'div', 'class' => 'ctrlHolder')
                    ),
                    array ('CkEditor', array ())
                ),
                'attr' => array (
                )
            ),
            'ckfinderimage' => array (
                'noms' => array ('Ckfinderimage'),
                'deco' => array (
                    'ViewHelper',
                    array ('Description', array ('placement' => 'APPEND', 'class' => 'formHint')),
                    array ('LabelError', array ('escape' => false, 'requiredPrefix' => '<em>*</em>')),
                    array (
                        'decorator' => array ('Holder' => 'HtmlTagPerso'),
                        'options' => array ('tag' => 'div', 'class' => 'ctrlHolder')
                    ),
                    array ('CkFinderImage', array ())
                ),
                'attr' => array (
                )
            ),
            'fichiers' => array (
                'noms' => array ('File'),
                'deco' => array (
                    'FileHelper',
                    array ('Description', array ('placement' => 'APPEND', 'class' => 'formHint')),
                    array ('LabelError', array ('escape' => false, 'requiredPrefix' => '<em>*</em>')),
                    array (
                        'decorator' => array ('Holder' => 'HtmlTagPerso'),
                        'options' => array ('tag' => 'div', 'class' => 'ctrlHolder')
                    )
                ),
                'attr' => array (
                )
            ),
            'caches' => array (
                'noms' => array ('Hidden'),
                'deco' => array (
                    'ViewHelper',
                    array ('Description', array('placement' => 'APPEND', 'class' => 'formHint')),
                    array (
                        'decorator' => array ('Holder' => 'HtmlTag'),
                        'options' => array ('tag' => 'div', 'class' => 'hiddenHolder')
                    )
                ),
            ),
            'actions' => array (
                'noms' => array ('Image', 'Reset', 'Submit'),
                'deco' => array (
                    'ViewHelper',
                    array ('Description', array('placement' => 'APPEND', 'class' => 'formHint')),
                    array (
                        'decorator' => array ('Holder' => 'HtmlTag'),
                        'options' => array ('tag' => 'div', 'class' => 'actionHolder')
                    )
                ),
            ),
            'multiples' => array (
                'noms' => array ('Multi', 'MultiCheckbox', 'Radio'),
                'deco' => array (
                    'ViewHelper',
                    'MultiSelect',
                    array ('Description', array ('placement' => 'APPEND', 'class' => 'formHint')),
                    array ('LabelError', array ('escape' => false, 'requiredPrefix' => '<em>*</em>')),
                    array (
                        'decorator' => array ('Holder' => 'HtmlTag'),
                        'options' => array ('tag' => 'div', 'class' => 'ctrlHolder')
                    )
                ),
                'attr' => array (
                    'label_class' => 'inlineLabel'
                ),
                'sep' => ''
            )
        );

        protected function _cherche ($quoi, $nomClasse) {
            $zfe = substr ($nomClasse, 0, 17);
            $classe = substr ($nomClasse, 18);
            if ($zfe == 'FUF_Form_Element_') {
                $zfe = substr ($nomClasse, 0, 16);
                $classe = substr ($nomClasse, 17);
            }
            $retour = false;
            if (($zfe == 'Zend_Form_Element') | ($zfe == 'FUF_Form_Element')) {
                foreach ($this->_lesElements as $v) {
                    if (in_array ($classe, $v ['noms'])) {
                        if (isset ($v [$quoi])) $retour = $v [$quoi];
                    }
                }
            }
            return $retour;
        }


        protected function _chercheDecorateurs ($nomClasse) {
            return $this->_cherche ('deco', $nomClasse);
        }

        protected function _chercheAttributs ($nomClasse) {
            return $this->_cherche ('attr', $nomClasse);
        }

        protected function _chercheSeparateur ($nomClasse) {
            return $this->_cherche ('sep', $nomClasse);
        }

        public function __construct ($options = null) {
            $this->addElementPrefixPath ('FUF_Form_Decorator', 'FUF/Form/Decorator/', 'decorator');
            $this->addPrefixPath ('FUF_Form_Decorator', 'FUF/Form/Decorator/', 'decorator');
            $this->setAttrib ('accept-charset', 'UTF-8');
            $this->setDisplayGroupDecorators (array ('FormElements', 'Fieldset'));
            $this->setDecorators (array (
                array ('Description', array ('placement' => 'PREPEND', 'class' => 'formTitle')),
                'FormElements',
                'TitleError',
                'Form'
            ));
            $this->_setupTranslation ();
            parent::__construct ($options);
            $this->setMethod ('post');
            $this->setAction ('');
            $this->setAttrib ('class', 'uniForm');
        }

        public function addElement ($element, $name = null, $options = null) {
            if (is_string ($element)) try {
                $element = $this->createElement ($element, $name, $options);
            } catch (Exception $e) {
                throw $e;
            }

            $sep = false;
            if (!$options || !array_key_exists ('decorators', $options)) {
                $deco = $this->_chercheDecorateurs (get_class ($element));
                $attr = $this->_chercheAttributs (get_class ($element));
                $sep = $this->_chercheSeparateur (get_class ($element));
                if ($deco !== false) $element->setDecorators ($deco);
                if ($attr !== false) $element->setAttribs ($attr);
                if ($sep !== false) $element->setSeparator ($sep);
            }
            $element->addFilter (new FUF_Filter_StripSlashes ());
            $retour =  parent::addElement ($element, $name, $options) ;
            //if ($sep !== false) $retour->setSeparator ($sep);
            return $retour;
        }

        public function addDisplayGroup (array $elements, $name, $options = null) {
            if ($options === null) $options = array ();
            if (! isset ($options ['class'])) $options ['class'] = 'inlineLabels';
            $options ['disableLoadDefaultDecorators'] = true;
            parent::addDisplayGroup ($elements, $name, $options);
            $groupe = $this->getDisplayGroup ($name);
            $groupe->addDecorator ('FormElements')
                 ->addDecorator ('Fieldset');
            return $this;
        }

        protected function _setupTranslation () {
            if (! self::getDefaultTranslator ()) {
                $translate = new Zend_Translate ('array', self::$_racine . 'translate/formulaires.php', 'fr');
                Zend_Form::setDefaultTranslator ($translate);
            }
        }
    }