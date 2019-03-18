<?php
    class Restnotation_ImportController extends App_Controller_RestAction {
        public function init () {
            parent::init();
        }

        public function indexAction () {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }

        public function deleteAction () {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }

        public function getAction () {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }

        public function postAction () {
            $zsn = new Zend_Session_Namespace ('challenge');
            if (isset ($zsn->chalId) && is_array ($_FILES) && isset ($_FILES ['fichierExcel'])) {
                file_put_contents ('/tmp/' . $_FILES ['fichierExcel']['name'], file_get_contents ($_FILES ['fichierExcel']['tmp_name']));
                $metierChallenge = new Model_Metier_Challenges ();
                $chalInfos = $metierChallenge->getChallenge ($zsn->chalId);
                $reader = new Challenge_Model_Metier_Excelreader ($chalInfos);
                if ($reader->load ('/tmp/' . $_FILES ['fichierExcel']['name'])) {
                    $clubInfos = $reader->getClubInformations ()->getDonnees (true);
                    $data = array (
                        'infos' => array (
                            'club' => $clubInfos ['club'],
                            'id' => $clubInfos ['id'],
                            'chalid' => $zsn->chalId
                        ),
                        'parse' => $reader->parseChallenge ()
                    );
                } else {
                    $data = $_FILES ['fichierExcel'];
                }
                $this->_response->Ok ();
            } else {
                $data = array ();
                $this->_response->notAcceptable ();
            }
            $this->view->data = $data;
            $this->ieHack ();
        }

        public function putAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
    }