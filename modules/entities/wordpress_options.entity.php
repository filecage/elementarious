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
     
    class Wordpress_Options extends Entity {
    
        public function __construct() {
            parent::__construct(new Wordpress_Options_Datamodel());
        }
        
        /**
         * getOption
         *
         * searches for an option and loads it into the entity cache
         */
        public function getOption($option_name) {
        
            $options = $this->getAll(array('option_name'=>$option_name));
            
            // nothing found, return false
            if (count($options)<1)
                return false;
                
            // else, load into cache and return array
            return $this->get($options[0]['option_id']);
            
        }
        
        
    }
    
?>