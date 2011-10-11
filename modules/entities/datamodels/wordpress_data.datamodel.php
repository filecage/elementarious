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
     
    class Wordpress_Posts_Datamodel extends Datamodel {
    
        protected $_frameworkValid = false;
        protected $_tableName = 'wp_posts';
        protected $_primaryKey = 'ID';
        protected $_globalWhere = array('post_status' => 'publish');
    
        protected function configure() {
        
            $this->setFields(array(
                'ID' => array('type'=>'int', 'readonly' => true),
                'post_date' => array('type'=>'datetime'),
                'post_content' => array('type'=>'text', 'allow_html' => true),
                'post_title' => array('type'=>'text'),
                'post_status' => array('type'=>'varchar', 'length'=> 20),
                'post_name' => array('type'=>'varchar', 'length' => 200),
                'post_modified' => array('type'=>'datetime'),
                'post_content_filtered' => array('type'=>'text'),
                'guid' => 'text',
                'menu_order' => array('type'=>'int'),
                'post_type' => array('type'=>'varchar', 'length'=>20),
                'post_mime_type' => array('type'=>'varchar','length'=>100)
            ));
        
        }
    
    
    }
    
    class Wordpress_Postmeta_Datamodel extends Datamodel {
    
        protected $_frameworkValid = false;
        protected $_tableName = 'wp_postmeta';
        protected $_primaryKey = 'meta_id';
        
        protected function configure() {
        
            $this->setFields(array(
                'meta_id' => array('type'=>'int', 'readonly'=>true),
                'post_id' => 'int',
                'meta_key' => array('type'=>'varchar', 'length'=>255),
                'meta_value' => 'text'
            ));
        
        }

    
    }
    
    
?>