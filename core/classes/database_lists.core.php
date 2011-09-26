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
     
    class Lists {
    
        static private $lists = array();
        
        
        /**
         * getItems
         *
         * gets all items from the list
         */
        static public function getItems($list_name) {
        
            if (!$list = self::getList($list_name)) return false;
            
            return $list->getAll($where = null, $order = array('text'=>'ASC'));
            
        }
        
        /**
         * getItem
         *
         * gets one item by id
         */
        static public function getItem($list_name, $item) {
            
            if (!$list = self::getList($list_name)) return false;
            
            $list->get($item);
            return $list->text;
            
        }
        /**
         * isListItem()
         *
         * returns true if $id is element in list table $list
         */
        static public function isListItem($list_name, $item) {
        
            if (!$list = self::getList($list_name)) return false;
            
            $list->get($item);
            return $list->loaded();
            
        }
        
        
        /**
         * getList
         *
         * gets a lists entity
         */
        static public function getList($list_name) {
        
            if (isset(self::$lists[$list_name])) $list = self::$lists[$list_name];
            else {
                $list = new List_Entity($list_name);
                if ($list) self::$lists[$list_name] = $list;
            }
            
            return $list;
            
        }
    
    
    }
    
    
    class List_Entity extends Entity {
    
        public function __construct($name) {
        
            $datamodel = new List_Datamodel();
            if (!$datamodel->initList($name)) parent::__construct($datamodel);
           
        }
        
    }
    
    class List_Datamodel extends Datamodel {
    
        protected $_tableName = 'list__';
        protected $_primaryKey = 'id';
        protected $_frameworkValid = false;
    
        protected function configure() {

            $this->setFields(array(
                'id'   => array('type'=>'int','readonly'=>true),
                'text' => array('type'=>'text')
            ));
            
        }
        
        public function initList($name='') {
        
            if (!isText($name)) return false;
            $this->_tableName .= $name;
        
        }
        
    }
    