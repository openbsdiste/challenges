<?php
    class App_Controller_Plugin_RestHandler extends Zend_Controller_Plugin_Abstract {
        private $dispatcher;

        private $defaultFormat = 'html';

        private $acceptableFormats = array (
            'html',
            'xml',
            'php',
            'json'
        );

        private $responseTypes = array (
            'text/html'                         => 'html',
            'application/xhtml+xml'             => 'html',
            'text/xml'                          => 'xml',
            'application/xml'                   => 'xml',
            'application/xhtml+xml'             => 'xml',
            'text/php'                          => 'php',
            'application/php'                   => 'php',
            'application/x-httpd-php'           => 'php',
            'application/x-httpd-php-source'    => 'php',
            'text/javascript'                   => 'json',
            'application/json'                  => 'json',
            'application/javascript'            => 'json'
        );

        private $requestTypes = array (
            'multipart/form-data',
            'application/x-www-form-urlencoded',
            'text/xml',
            'application/xml',
            'text/php',
            'application/php',
            'application/x-httpd-php',
            'application/x-httpd-php-source',
            'text/javascript',
            'application/json',
            'application/javascript',
            false
        );

        public function __construct (Zend_Controller_Front $frontController) {
            $this->dispatcher = $frontController->getDispatcher ();
        }

        public function dispatchLoopStartup (Zend_Controller_Request_Abstract $request) {
            $this->_response->setHeader ('Vary', 'Accept');
            $this->_response->setHeader ('Access-Control-Max-Age', '86400');
            $this->_response->setHeader ('Access-Control-Allow-Origin', '*');
            $this->_response->setHeader ('Access-Control-Allow-Credentials', 'true');
            $this->_response->setHeader ('Access-Control-Allow-Headers', 'Authorization, X-Authorization, Origin, Accept, Content-Type, X-Requested-With, X-HTTP-Method-Override');
            $this->setConfig ();
            $this->setResponseFormat ($request);
            $this->handleActions ($request);
            $this->handleRequestBody ($request);
        }

        private function setConfig () {
            $frontController = Zend_Controller_Front::getInstance ();
            $options = new Zend_Config ($frontController->getParam ('bootstrap')->getOptions (), true);
            $rest = $options->get ('rest', false);
            if ($rest) {
                $this->defaultFormat = $rest->default;
                $this->acceptableFormats = $rest->formats->toArray ();
            }
        }

        private function setResponseFormat (Zend_Controller_Request_Abstract $request) {
            $format = false;
            if (in_array ($request->getParam ('format', 'none'), $this->responseTypes)) {
                $format = $request->getParam ('format');
            } else {
                $bestMimeType = $this->negotiateContentType ($request);
                if (!$bestMimeType || $bestMimeType == '*/*') {
                    $bestMimeType = 'application/xml';
                }
                $format = $this->responseTypes [$bestMimeType];
            }
            if ($format == false or !in_array ($format, $this->acceptableFormats)) {
                $request->setParam ('format', $this->defaultFormat);
                if ($request->isOptions () === false) {
                    $request->dispatchError (App_Controller_Response_Rest::UNSUPPORTED_TYPE, 'Unsupported Media/Format Type');
                }
            } else {
                $request->setParam('format', $format);
            }
        }

        private function handleActions (Zend_Controller_Request_Abstract $request) {
            $controller = $this->dispatcher->getControllerClass ($request);
            $className  = $this->dispatcher->loadClass ($controller);
            $class = new ReflectionClass ($className);
            if ($this->isRestClass ($class)) {
                $methods = $class->getMethods (ReflectionMethod::IS_PUBLIC);
                $actions = array ();
                foreach ($methods as &$method) {
                    $name = strtoupper ($method->name);
                    if ($name == '__CALL' and $method->class != 'Zend_Controller_Action') {
                        $actions[] = $request->getMethod ();
                    } elseif (substr ($name, -6) == 'ACTION' and $name != 'INDEXACTION') {
                        $actions [] = str_replace ('ACTION', null, $name);
                    }
                }
                $this->_response->setHeader ('Access-Control-Allow-Methods', implode (', ', $actions));

                if (!in_array (strtoupper ($request->getMethod ()), $actions)) {
                    $request->dispatchError (App_Controller_Response_Rest::NOT_ALLOWED, 'Method Not Allowed');
                    $this->_response->setHeader ('Allow', implode (', ', $actions));
                }
            }
        }

        private function handleRequestBody (Zend_Controller_Request_Abstract $request) {
            $header = current (explode (';', strtolower ($request->getHeader ('Content-Type'))));
            foreach ($this->requestTypes as $contentType) {
                if ($header == $contentType) {
                    break;
                }
            }
            $rawBody = $request->getRawBody ();
            if (in_array ($contentType, array ('multipart/form-data', 'application/x-www-form-urlencoded'))) {
                if ($request->isPost () && $contentType == 'multipart/form-data') {
                    foreach ($_FILES as &$file) {
                        if (array_key_exists ('tmp_name', $file) && is_file ($file ['tmp_name'])) {
                            $data = file_get_contents ($file ['tmp_name']);
                            $file ['content'] = base64_encode ($data);
                        }
                    }
                    unset ($file);
                } else {
                    switch ($contentType) {
                        case 'application/x-www-form-urlencoded':
                            parse_str ($rawBody, $_POST);
                            break;
                        case 'multipart/form-data':
                            $boundary = false;
                            $expl = explode (';', $request->getHeader ('Content-Type'));
                            parse_str (end ($expl));
                            if ($boundary) {
                                $regs = array ();
                                if (preg_match (sprintf ('/--%s(.+)--%s--/s', $boundary, $boundary), $rawBody, $regs)) {
                                    $chunks = explode ('--' . $boundary, trim ($regs [1]));
                                    foreach ($chunks as $chunk) {
                                        if (preg_match ('/Content-Disposition: form-data; name="(?P<name>.+?)"(?:; filename="(?P<filename>.+?)")?(?P<headers>(?:\\r|\\n)+?.+?(?:\\r|\\n)+?)?(?P<data>.+)/si', $chunk, $regs)) {
                                            if (!empty ($regs ['filename'])) {
                                                $data = $regs ['data'];
                                                $headers = $this->parseHeaders ($regs ['headers']);
                                                $_FILES [$regs ['name']] = array (
                                                    'name' => $regs ['filename'],
                                                    'type' => $headers ['Content-Type'],
                                                    'size' => mb_strlen ($data),
                                                    'content' => base64_encode ($data)
                                                );
                                            } else {
                                                $_POST [$regs ['name']] = trim ($regs ['data']);
                                            }
                                        }
                                    }
                                }
                            }
                            break;
                    }
                }
                $request->setParams ($_POST + $_FILES);
            } elseif (!empty ($rawBody)) {
                try {
                    switch ($contentType) {
                        case 'text/javascript':
                        case 'application/json':
                        case 'application/javascript':
                            $_POST = (array) Zend_Json::decode ($rawBody, Zend_Json::TYPE_OBJECT);
                            break;
                        case 'text/xml':
                        case 'application/xml':
                            $json = @Zend_Json::fromXml ($rawBody);
                            $_POST = (array) Zend_Json::decode ($json, Zend_Json::TYPE_OBJECT)->request;
                            break;
                        case 'text/php':
                        case 'application/x-httpd-php':
                        case 'application/x-httpd-php-source':
                            $_POST = (array) unserialize ($rawBody);
                            break;
                        default:
                            $_POST = (array) $rawBody;
                            break;
                    }
                    $request->setParams ($_POST);
                } catch (Exception $e) {
                    $request->dispatchError (App_Controller_Response_Rest::BAD_REQUEST, 'Invalid Payload Format');
                    return;
                }
            }
        }

        private function isRestClass ($class) {
            if ($class === false) {
                return false;
            } elseif (in_array ($class->name, array ('Zend_Rest_Controller', 'App_Controller_RestAction'))) {
                return true;
            } else {
                return $this->isRestClass ($class->getParentClass ());
            }
        }

        private function parseHeaders ($header) {
            if (function_exists ('http_parse_headers')) {
                return http_parse_headers ($header);
            }

            $retVal = array ();
            $fields = explode ("\r\n", preg_replace ('/\x0D\x0A[\x09\x20]+/', ' ', $header));
            $match = array ();
            foreach ($fields as $field) {
                if (preg_match ('/([^:]+): (.+)/m', $field, $match)) {
                    $match [1] = preg_replace ('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower (trim ($match [1])));
                    if (isset ($retVal [$match [1]])) {
                        $retVal [$match [1]] = array ($retVal [$match [1]], $match [2]);
                    } else {
                        $retVal [$match [1]] = trim ($match [2]);
                    }
                }
            }
            return $retVal;
        }

        private function negotiateContentType ($request) {
            if (function_exists ('http_negotiate_content_type')) {
                return http_negotiate_content_type( array_keys ($this->responseTypes));
            }
            $string = strtolower (str_replace (' ', '', $request->getHeader ('Accept')));
            $mimeTypes = array ();
            $types = explode (',', $string);
            foreach ($types as $type) {
                $quality = 1;
                if (strpos ($type, ';q=')) {
                    list ($type, $quality) = explode (';q=', $type);
                } elseif (strpos ($type, ';')) {
                    list ($type, ) = explode (';', $type);
                }
                if (array_key_exists ($type, $this->responseTypes) and !array_key_exists ($quality, $mimeTypes)) {
                    $mimeTypes [$quality] = $type;
                }
            }
            krsort ($mimeTypes);

            return current (array_values ($mimeTypes));
        }
    }
