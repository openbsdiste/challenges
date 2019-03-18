<?php
    class App_Db_View extends App_Db_Table {
        protected function _setupPrimaryKey () {
            $this->_primary = $this->_getCols ();
            parent::_setupPrimaryKey ();
        }
        
        protected function _modifie () {
            throw new Zend_Db_Exception ("Toute modification est interdite sur une vue.");
        }
        
        public function insert (array $data) {
            $this->_modifie ();
        }

        public function update (array $data, $where) {
            $this->_modifie ();
        }
        
        public function delete ($where) {
            $this->_modifie ();
        }
        
        public function getCount ($select = null) {
            if (! ($select instanceof Zend_Db_Select)) {
                $select = $this->select ();
            }
            $select->from ($this, array ('count(*) as amount'));
            $rows = $this->fetchAll ($select);
            
            return $rows [0]->amount;
        }
    }