<?php
    /**
     * @property integer $id
     * @property integer $noeud
     * @property string $challenge
     * @property string $left
     * @property string $right
     * @property string $level
     * @property string $title
     * @property boolean modifie
     */
    class Challenge_Model_Arbre extends App_Model_Entity {
        protected $id;
        protected $noeud;
        protected $challenge;
        protected $left;
        protected $right;
        protected $level;
        protected $title;
        protected $modifie = 0;

        protected $_cles = array ('id');
    }