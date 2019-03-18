<?php
    class App_Db_Table extends Zend_Db_Table {
        public function delete ($where) {
            if (isset ($this->_dependentTables) && is_array ($this->_dependentTables) && ! empty ($this->_dependentTables)) {
                $count = 0;
                $rows = $this->fetchAll ($where);
                foreach ($rows as $row) {
                    $row->delete ();
                    $count++;
                }
                return $count;
            } else return parent::delete ($where);
        }
        
        public function getCount ($select = null) {
            if (! ($select instanceof Zend_Db_Select)) {
                $select = $this->select ();
            }
            $select->from ($this)
                ->reset ('columns')
                ->columns (new Zend_Db_Expr ('COUNT(*)'));
            $count = $this->getAdapter ()->fetchOne ($select);
            return $count;
        }
    }