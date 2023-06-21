<?php
    namespace abstraction;

    abstract class BaseController
    {
        protected function __construct()
        {
            $this->setCors();
        }

        public function HandleRequest()
        {
            switch($_SERVER['REQUEST_METHOD'])
            {
                case 'POST':
                    $this->onPost();
                    break;
                case 'GET':
                    $this->onGet();
                    break;
                case 'PUT':
                    $this->onPut();
                    break;
                case 'DELETE':
                    $this->onDelete();
                    break;
                default:
                    http_response_code(405);                   
            }
        }

        public static function Create() : static
        {
            return new static();
        }

        protected function onGet()
        {
            http_response_code(404);
        }

        protected function onPost()
        {
            http_response_code(404);
        }

        protected function onPut()
        {
            http_response_code(404);
        }

        protected function onDelete()
        {
            http_response_code(404);
        }

        protected function setCors()
        {
            header('Access-Control-Allow-Origin: *');
        }
    }
?>