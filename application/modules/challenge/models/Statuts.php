<?php
    /**
     * @property integer $statut
     * @property integer $bloquant
     */
    class Challenge_Model_Statuts extends App_Model_Entity {
        protected $statut;
        protected $bloquant;

        protected $_cles = array ('statut');
        
        protected $_autoincrement = false;
    }