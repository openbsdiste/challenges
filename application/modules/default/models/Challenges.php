<?php
    /**
     * @property integer $id
     * @property string $annee
     * @property integer $organisateur
     * @property integer $statut
     * @property string $datelimite
     */
    class Model_Challenges extends App_Model_Entity {
        protected $id;
        protected $annee;
        protected $organisateur;
        protected $statut = 0;
        protected $datelimite;

        protected $_cles = array ('id');
    }