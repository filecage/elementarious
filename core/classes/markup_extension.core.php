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
     
    class Markup_Cachebuilder extends Filewriter {
    
        protected $_targetDirectory = CACHE_PATH;
        protected $_targetFile = 'markupcache.json';
        protected $_writeMode = 'w';
    
    }
    
    interface Markup_Extension_Interface {
    
        public function __construct();
        public function get($args=null);
        
    }

    abstract class Markup_Extension {
    
        final public function __construct() {
        
            if (!isset($this->_endTag)) $this->_endTag = '';
            $this->init();
        
        }
        
        public function init() {}

    
        public function get($args=null) {
        
            $return = $this->_pattern;
            
            if (isset($args)) {
            
                foreach ($args as $name => $val) {
                
                    $return = str_replace('%' . $name . '%', $val, $return);
                    
                }
            
            }
            
            return $return;
        
        }
        
        public function endTag() {
        
            return $this->_endTag;
            
        }
    
    }
    
?>