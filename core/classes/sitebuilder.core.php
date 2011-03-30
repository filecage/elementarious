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
    
    
    class Sitebuilder {
    
        protected $pathFromRoot;
        private $output;
        private $vars = array();
    
        /*
         * ::__construct()
         * creates the variable $pathFromRoot, which is neccessary if the framework is hosted in a different directory than DOCUMENT_ROOT
         */
        public function __construct() {
        
            $this->pathFromRoot = dirname($_SERVER['PHP_SELF']);
            return true;
        
        }
        
        /**
         * ::create()
         * the actual without-me-works-nothing
         * called to process the whole request, instancing the templateparser and the controller
         */
        public function create() {
        
            // parse query
            $content     = new Templateparser();
            $queryparser = new Queryparser();
            $queryparser->parse();
            
            // build controller
            
            $controller_name = 'Controller_' . strtolower(str_replace('/', '_', trim($queryparser->getOpt('file'),'/')));

            if (Classloader::getFileName($controller_name,true,true)) {

                $controller = new $controller_name;
                $reflection = new ReflectionClass($controller_name);
                
                // if the controller is no child of the class Controller, throw exception
                if (!$reflection->isSubclassOf('Controller')) throw new Exception ('Sitecontroller has to be a child of class Controller to work with elementarious.');
                
            }
            
            if (!Option::val('paramsInUri') && $queryparser->getOpt('url_param')) throw new HttpError(404);
            
            // parse template (finally)
            $this->output = $content->get($queryparser->getOpt('page'));

        }
        
        /**
         * ::get()
         * returns the site content
         */
        public function get() {
        
            return $this->output;
            
        }

    }
    
?>