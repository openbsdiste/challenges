<?php
    class Challenge_Model_DbTable_Participants extends App_Db_Table {
        protected $_name = 'participants';
        protected $_primary = array ('challenge', 'club');
        protected $_sequence = false;
    }