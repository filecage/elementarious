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
     
     
    class Wordpress_Comments_Datamodel extends Datamodel {
    
        protected $_frameworkValid = false;
        protected $_tableName = 'wp_comments';
        protected $_primaryKey = 'comment_ID';
        protected $_globalWhere = array('comment_approved' => 1);
    
    
        public function configure() {
        
            $this->setFields(array(
                'comment_ID' => array('type'=>'int', 'readonly'=>true),
                'comment_post_ID' => array('type'=>'int'),
                'comment_author' => array('type'=>'text'),
                'comment_author_email' => array('type'=>'varchar', 'length'=>100),
                'comment_author_url' => array('type'=>'varchar', 'length'=>200),
                'comment_author_IP' => array('type'=>'varchar', 'length'=>100, 'default' => $_SERVER['REMOTE_ADDR']),
                'comment_date' => array('type'=>'datetime'),
                'comment_date_gmt' => array('type'=>'datetime'),
                'comment_content' => array('type'=>'text'),
                'comment_karma' => array('type'=>'int'),
                'comment_approved' => array('type'=>'varchar', 'length'=>20),
                'comment_agent' => array('type'=>'varchar', 'length'=>255, 'default' => $_SERVER['HTTP_USER_AGENT']),
                'comment_parent' => array('type'=>'int', 'default' => 0),
                'user_id' => array('type'=>'int','default'=>0)
            ));
        
        }
        
    }
    
 ?>