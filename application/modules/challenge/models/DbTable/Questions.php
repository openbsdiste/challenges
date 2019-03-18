<?php
    class Challenge_Model_DbTable_Questions extends App_Db_Table {
        protected $_name = 'questions';
        protected $_primary = array ('id', 'challenge');
        protected $_sequence = false;
    }