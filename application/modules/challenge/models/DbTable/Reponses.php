<?php
    class Challenge_Model_DbTable_Reponses extends App_Db_Table {
        protected $_name = 'reponses';
        protected $_primary = array ('id', 'challenge', 'club');
        protected $_sequence = false;
    }