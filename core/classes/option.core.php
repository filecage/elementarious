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
     
    
    class Option {
    
        static private $config;
        static private $options = array();
        static private $loaded = false;
        
        
        /**
         * ::load()
         * loads the configfile and reads into an array if not done
         * also creates an array if the config is empty
         */
        static public function load() {
        
            if (!self::$loaded) {
            
                include_once(CONFIG_PATH . '/config.php');
                self::$config = $config;
                self::$config['configHash'] = md5(implode(';', $config));
                self::$loaded = true;
                
                if (!is_array(self::$config)) self::$config = array();
            
            }
        
        }
        
        /**
         * ::val()
         * reads an option from the array
         */
        static public function val($name) {
            
            if (!self::$loaded) self::load();
            return ((isset(self::$config[$name])) ? self::$config[$name] : ((isset(self::$options[$name])) ? self::$options[$name] : false));
        
        }
        
        /**
         * ::set()
         * sets a value for an option
         * if the setting already appears in the config, it throws an exception (as long as $ignoreConfigExistance is false)
         */
        static public function set($name,$val,$ignoreConfigExistance=false) {
        
            if (!self::$loaded) self::load();
            
            if (isset(self::$config[$name]) && !$ignoreConfigExistance) {
                throw new Exception('Can not redeclare config option "' . $name . '"');
                return false;
            }

            self::$options[$name] = $val;
            return $val;
            
        }
    
    
    }
    
 ?>