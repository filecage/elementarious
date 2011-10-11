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
     
    class Wordpress_Data extends Entity {
    
        private $postmeta;
    
        public function __construct() {
            parent::__construct(new Wordpress_Posts_Datamodel());
            $this->postmeta = new Wordpress_Postmeta_Entity();
        }
        
        /**
         * getMenuItems
         *
         * gets all published menu items and orders them
         * does not return post_content
         */
        public function getMenuItems() {
        
            return $this->getAll($where = array('post_type'=>'page'), $order_by = array('menu_order' => 'ASC', 'post_title' => 'ASC'));
        
        }
        
        /**
         * getSite
         *
         * returns the site data specified by $site
         * if $site is false or another invalid value (e.g. null), it returns the index page
         */
        public function getSite($site = null) {
        
            if ((is_null($site)||is_bool($site))&&!isText($site))
                $where = array('post_type'=>'page');
            else
                $where = array('post_type'=>'page', 'post_name'=>$site);
                
            $sites = $this->getAll($where, $order_by = array('menu_order' => 'ASC', 'post_title' => 'ASC'));
            $this->get($sites[0]['ID']);
            
            if (count($sites) < 1)
                throw new HttpError(404);
            
            
        }
        
        /**
         * getPostmeta
         *
         * returns all options of $post_id keyed by $option_name
         */
        public function getPostmeta($post_id) {
        
            $postmeta = $this->postmeta->getAll(array('post_id'=>$post_id));
            $meta     = array();
            
            foreach ($postmeta as $option) {
                $meta[$option['meta_key']] = $option['meta_value'];
            }
            
            return $meta;
            
        }
    
    }
    
    class Wordpress_Postmeta_Entity extends Entity {
    
        public function __construct() {
            parent::__construct(new Wordpress_Postmeta_Datamodel());
        }
        
    }
    
    
?>