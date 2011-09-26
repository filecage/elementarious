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
     
     
     
    class Templatevars {
    
        static private $vars = array();
        static private $init = false;
        
        static private function init() {
        
            self::$vars = array(
                'path' => dirname($_SERVER['PHP_SELF']),
                'page' => Option::val('page'),
            );
            
            self::$init = true;
        
        }
        
        static public function set($name, $val=null) {
        
            if (!self::$init) self::init();
        
            if (is_array($name)) {
            
                foreach ($name as $var_name => $var_val) {
                
                    self::$vars[$var_name] = $var_val;
                
                }
            
            }
            else {
        
                self::$vars[$name] = $val;
            
            }
            
        }
        
        static public function get() {
        
            if (!self::$init) self::init();
            return self::$vars;
        
        }
        
        static public function clear() {

            self::init();
        
        }
    
    
    }
    
    
?>