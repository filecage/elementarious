<?php

    /**
     * elementarious framework
     *
     *
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with this program.  If not, see <http://www.gnu.org/licenses/>.
     * 
     */
    
    
    class Queryparser extends Sitebuilder {
        
        private $query;
        protected $pathFromRoot;
    
        /*
         * ::__construct()
         */
        public function __construct() {
            parent::__construct();
        }
        
        /**
         * ::parse()
         * parses the optional parameters of GET and POST and sets the request type
         * also sets the right location of the requested file and may perform user-agent checks
         */
        public function parse() {
        
            // set request type
            Option::set('request_type', $this->getRequestType());
        
            // parse query
            $url_param  = false;
            $dir        = VIEW_PATH . Option::val('request_type') . '/';
            $page       = '/';
            $file       = '';
            $params     = array();
            $query      = array();
            $query_full = explode('?', $_SERVER['REQUEST_URI']);
            
            
            foreach (explode('/', trim(str_replace($this->pathFromRoot,'',$query_full[0]),'/')) as $level => $call) {

                if (file_exists($dir . $call . '.xml')) {
                
                    $file    = $call;
                    $page   .= $call;
                    $query[] = array($call,'file');
                
                }
                elseif (is_dir($dir . $call.'/')) {
                    
                    $file     = 'index';
                    $page    .= $call.'/';
                    $dir     .= $call.'/';
                    $query[]  = array($call,'dir');
                
                }
                else {
                
                    $url_param = true;
                    $this->addUrlParameter($call);
                    $params[] = $call;
                
                }
            
            }
            
            $this->query = array(
                'url_param' => $url_param,
                'dir'       => $dir,
                'file'      => $file,
                'page'      => $page,
                'query_prs' => $query_full,
                'query'     => $query,
                'params'    => $params
            );
            
            var_dump($this->query);die();
            
            // set constant page
            #$file = '/' . trim(str_replace($this->pathFromRoot, '', $query[0]),'/');
            #if ($this->file == '/') $this->file = '/index';
            Option::set('page', $file);
            
            
            // parse parameters
            $this->parseParameters();

        }
        
        /**
         * ::addUrlParameter()
         * adds a new parsed GET-parameter from the url
         */
        private function addUrlParameter($param) {
        
            $_GET[] = $param;
        
        }
        
        /**
         * ::setRequestType()
         * returns the request type by the given variables
         */
        private function getRequestType() {
        
            if (isset($_POST['__ajax'])) {
                return 'ajax';
            }
            elseif (isset($_POST['__plain'])) {
                return 'text';
            }
            elseif (isset($_POST['__mail'])) {
                throw new Exception('Requesting mail parser by HTTP is not valid');
            }
            else {
                return 'html';
            }
        
        }
        
        /**
         * ::parseParameters()
         * parses GET and POST parameters and sets the request type
         */
        private function parseParameters() {
        
            global $_VAR;
        
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                Option::set('REQUEST_IS_GET', true);
                $_VAR = $_GET;
            }
            
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
                Option::set('REQUEST_IS_POST', true);
                $_VAR = $_POST;
            }
            
            foreach ($_VAR as $name => $val) {
                $_VAR[$name] = trim($val);
                if (empty($_VAR[$name])) $_VAR[$name] = (Option::val('header_enable_shortget')) ? true : null;
            }

            return true;
        
        }
        
        public function getQuery() {
        
            return $this->query;
            
        }
    
    }