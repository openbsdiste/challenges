<?php
    /**
     * @property integer $id
     * @property integer $challenge
     * @property boolean $information
     * @property boolean $crypte
     * @property string $valeur
     * @property string $auteur
     * @property string $texte
     * @property string $image
     * @property integer $clubreponse
     * @property string $statut
     */
    class Challenge_Model_Questions extends App_Model_Entity {
        protected $id;
        protected $challenge;
        protected $information = 0;
        protected $crypte = 1;
        protected $valeur = null;
        protected $auteur = '';
        protected $texte;
        protected $image = '';
        protected $clubreponse = 0;
        protected $statut = '';

        protected $_cles = array ('id', 'challenge');
        
        protected $_autoincrement = false;
    }