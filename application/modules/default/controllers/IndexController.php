<?php
    class IndexController extends App_Controller_Action {
        public function indexAction () {
            $metierChallenges = new Model_Metier_Challenges ();
            $this->view->liste = $metierChallenges->getListeChallenges ();
        }
        
        public function pingAction () {
            
        }
    }