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
    class FUF_Form_SubForm extends FUF_Formulaire {
        protected $_isArray = true;

        public function __construct ($options = null, $racine = './') {
            parent::__construct ($options, $racine);
            $this->removeDecorator ('Form');
            $this->removeAttrib ('accept-charset');
            $this->addDecorator ('Fieldset');
            $this->setAttrib ('class', 'inlineLabels');
        }
    }
