<?php
    /**
     * @property integer $id
     * @property string $login
     * @property string $password
     * @property string $memoire
     * @property string $role
     * @property string $email
     * @property string $club
     * @property integer $actif
     */
    class Authentification_Model_Utilisateurs extends App_Model_Entity {
        protected $id;
        protected $login;
        protected $password;
        protected $memoire = '';
        protected $role;
        protected $email;
        protected $club;
        protected $actif = 1;

        protected $_cles = array ('id');

    }