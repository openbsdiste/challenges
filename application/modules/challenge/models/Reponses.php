<?php
    /**
     * @property integer $id
     * @property integer $challenge
     * @property integer $club
     * @property boolean $crypte
     * @property boolean $moderation
     * @property string $texte
     * @property string $notesinternes
     * @property float $note
     */
    class Challenge_Model_Reponses extends App_Model_Entity {
        protected $id;
        protected $challenge;
        protected $club;
        protected $crypte = 1;
        protected $moderation = 0;
        protected $texte;
        protected $notesinternes;
        protected $note = 0;

        protected $_cles = array ('id', 'challenge', 'club');
        
        protected $_autoincrement = false;
    }