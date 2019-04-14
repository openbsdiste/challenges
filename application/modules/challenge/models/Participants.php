<?php
    /**
     * @property integer $challenge
     * @property integer $club
     * @property boolean $valide
     * @property float $calculee
     * @property float $attribuee
     * @property string $password
     */
    class Challenge_Model_Participants extends App_Model_Entity {
        protected $challenge;
        protected $club;
        protected $valide = 0;
        protected $calculee = -1;
        protected $attribuee = -1;
        protected $password = '';

        protected $_cles = array ('challenge', 'club');
        
        protected $_autoincrement = false;
    }