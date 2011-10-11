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
     
     
    class Wordpress_Options_Datamodel extends Datamodel {
    
        protected $_frameworkValid = false;
        protected $_tableName = 'wp_options';
        protected $_primaryKey = 'option_id';

        protected function configure() {
        
            $this->setFields(array(
                'option_id' => array('type'=>'int', 'readonly'=>true),
                'option_name' => array('type'=>'varchar', 'length'=>64),
                'option_value' => 'text'
            ));
        
        }
    
    
    }
    
?>