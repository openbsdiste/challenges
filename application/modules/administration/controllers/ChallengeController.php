<?php
    class Administration_ChallengeController extends App_Controller_Action {
        public function indexAction () {
            $form = new Administration_Form_Challenge ();
            
            if ($this->_request->isPost ()) {
                if ($form->isValid ($this->_getAllParams ())) {
                    $metier = new Model_Metier_Challenges ();
                    $metier->creeChallenge ($form->getValues ());
                    $this->view->message = __('form.challenge.ok');
                }
            }
            
            $this->view->form = $form;
        }
    }