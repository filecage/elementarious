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
     
     
    abstract class Entity {
        
        protected $_datamodel;
        protected $_db;
        protected $loadedEntry;
        
        public function __construct($datamodel) {
        
            if (!Option::val('mysql_allow')) throw new Exception('Entity::__construct() called while MySQL is disabled by configuration; enable it or delete database call.');
            if ($datamodel instanceof Datamodel) $this->_datamodel = $datamodel;
            else throw new Exception('Entity::__construct() called, but given argument is no child of the Datamodel class.');
            
            $this->_db = Database::getResource();
            $this->clearLoadedEntry();
        
        }
        
        /**
         * getAll()
         *
         * gets all entries from the database which are not set to be deleted or match the possible configured globalWhere-statement
         */
        public function getAll($where = '', $order = '', $limit = false, $extra_fields = array()) {
        
            $this->clearLoadedEntry();
            $fields = $this->_datamodel->getFieldNames();
            $fieldnames = '';
            
            foreach ($extra_fields as $name => $as) {
            
                $fields[] = array($as, $name);
            
            }
            
            foreach ($fields as $num => $name) {
            
                if (isText($name[1])) $fieldnames .= $name[1] . ' as '.$name[0];
                else $fieldnames .= $name[0];
                if ($num < count($fields)-1) $fieldnames .= ',';
                
            }
            
            $query = 
                'SELECT
                    ' . $fieldnames . '
                FROM
                    ' . $this->_datamodel->getTableName();
                    
            $where_str = '';
            
            if ($this->_datamodel->isFrameworkValid()||$where!='') $where_str .= ' WHERE';
            if ($this->_datamodel->isFrameworkValid()) $where_str .= ' deleted = 0';
            if ($this->_datamodel->isFrameworkValid()&&$where!='') $where_str .= ' AND';
            
            if (isText($where)) $where_str .= ' ' . $where;
            elseif (is_array($where)) {
                $num = 0;
                foreach ($where as $field => $value) {
                
                    if (is_array($value)) {
                        if (isset($value['IN'])&&is_array($value['IN'])) {
                            $method   = 'IN';
                            $unquoted = true;
                            $query_value = '(';
                            foreach ($value['IN'] as $key => $val) {
                                $query_value .= '\'' . $this->escape($val) . '\'';
                                $query_value .= ($key+1<count($value['IN'])) ? ',' : ')';
                            }
                            $value = $query_value;
                        }
                        else {
                            $method   = isset($value[1]) ? $value[1] : '=';
                            $unquoted = (isset($value['unquoted'])&&$value['unquoted']) ? true : false;
                            $value    = $this->escape($value[0]);
                        }
                    }
                    else {
                        $method   = '=';
                        $unquoted = false;
                    }
                    
                    if ($num > 0) $where_str .= ' AND';
                    $where_str .= ' ' . $this->escape($field) . ' ' . $method;
                    $where_str .= ($unquoted) ? ' ' . $value : ' \'' . $this->escape($value) . '\'';
                    
                                    
                    $num++;

                }
            }
            
            if ($this->_datamodel->hasGlobalWhere()) {
                if (isText($where_str)) $where_str .= ' AND';
                $where_str .= $this->formGlobalWhere();
            }
            
                        
            $query = $query . $where_str;
            
            if (is_array($order)) {
            
                if (count($order) == 2) {
                    if (!is_array($order[0])&&!is_array($order[1]))
                        $order = array($order[0] => $order[1]);
                }
            
                $query .= ' ORDER BY';
                $num    = 0;
                
                foreach ($order as $field => $mode) {
                    if ($num>0) $query .= ' ,';
                    $query .= ' ' . $field . ' ' . $this->escape($mode);
                
                    $num++;
                    
                }
            
            }
            
            if ($limit !== false) {
            
                if (is_array($limit))
                    $query .= ' LIMIT ' . $this->escape($limit[0]) . ' , ' . $this->escape($limit[1]);
                    
                else
                    $query .= ' LIMIT 0 , ' . $this->escape($limit);
                
            }
            
            return $this->query($query);
        
        }
        
        /**
         * get()
         *
         * gets an entry by it's primary key
         */
        public function get($primary_key_orig, $order = '', $ignore_global_where = false) {
        
            $this->clearLoadedEntry();
            
            $primary_key = $this->escape($primary_key_orig);
            $fields      = $this->_datamodel->getFieldNames();
            $fieldnames  = '';
            
            foreach ($fields as $num => $name) {
            
                $fieldnames .= $name[0];
                if (isText($name[1])) $fieldnames .= ' as '.$name[1];
                if ($num < count($fields)-1) $fieldnames .= ',';
                
            }
            
            $query = 
                'SELECT
                    ' . $fieldnames . '
                FROM
                    ' . $this->_datamodel->getTableName() . '
                WHERE
                    ' . $this->_datamodel->getPrimaryKeyField() . ' = \'' . $primary_key . '\'
                ';
                    
            if ($this->_datamodel->isFrameworkValid()) $query .= ' AND deleted = 0';
            if ($this->_datamodel->hasGlobalWhere()&&!$ignore_global_where) $query .= ' AND ' . $this->formGlobalWhere();
            
            if (is_array($order)) {
            
                $query .= ' ORDER BY';
                $num    = 0;
                
                foreach ($order as $field => $mode) {
                    if ($num>0) $query .= ' ,';
                    $query .= ' ' . $this->escape($field) . ' ' . $this->escape($mode);
                
                    $num++;
                    
                }
            
            }
            
            if ($result = $this->query($query)) {

                foreach ($result[0] as $name => $value) {
                    $this->$name = $value;
                }
                
                $this->loadedEntry = $primary_key_orig;
                
                return $result[0];
                
            }
            
            return false;
        
        }
        
        /**
         * search()
         *
         * searches for $term in previously defined search fields
         */
        public function search($term) {
        
            $where_cond = '';
            $term = $this->escape($term);
            $order = array();
        
            foreach ($this->_datamodel->getSearchFields() as $num => $field) {
                
                if ($num>0)
                    $where_cond .= ' OR';
                
                $where_cond .= ' ' . $field['name'] . ' LIKE \'';
                $where_cond .= $field['method'] == 'complete' ? $term . '%' : '%' . $term . '%';
                $where_cond .= '\'';
                
                $order[$field['name']] = 'ASC';
            
            }

            $return = $this->getAll($where_cond,$order);
            
            if (count($return)==1)
                $this->get($return[0][$this->_datamodel->getPrimaryKeyField()]);
                
            return $return;
        
        }
        
        /**
         * getLoadedData()
         *
         * returns a data-array which has been loaded previously by get()
         */
        public function getLoadedData() {
        
            if (!$this->loadedEntry) return false;
            
            $return = array();
            foreach ($this->_datamodel->getFields() as $name => $value) {
                $return[$name] = $this->$name;
            }
            
            return $return;
            
        }
            
        
        /**
         * clearLoadedEntry()
         *
         * clears the loaded entry and resets all fields to enable a new insert()
         */
        public function clearLoadedEntry() {
        
            $this->loadedEntry = false;
            
            foreach ($this->_datamodel->getFields() as $name => $value) {
                $this->$name = $value;
            }
            
        }
        
        /**
         * save()
         *
         * updates the current loaded entry or creates a new one
         */
        public function save($data = false, $update_id = false, $insert = false) {
        
            $fields = array();
        
            if (!is_array($data)) {

                $fieldnames = $this->_datamodel->getFieldNames($write=true);
                
                foreach ($fieldnames as $num => $field) {
                    $fieldname = isText($field[1]) ? $field[1] : $field[0];
                    $fields[]  = array($fieldname,$this->escape($this->_datamodel->convertForDb($field[0],$this->$field[0])));
                }

            }
            else {

                foreach ($data as $field => $value) {
                    if (!$this->_datamodel->isField($field)) continue;
                    $value = $this->_datamodel->convertForDb($field,$value);
                    if ($value===false) return false;
                    $fields[] = array($this->escape($field),$this->escape($value));
                }
                
            }

            if ($update_id===false && $insert===false) {            
                $update_id = $this->loadedEntry;
            }
            
            if (count($fields) < 1) return false;

            /* we're doing an update of an existing, previously checked out (by get()) mysql entry */
            if ($update_id!==false) {
            
                if ($this->_datamodel->isFrameworkValid()) {
                    $fields[] = array('date_lastedit',date('Y-m-d H:i:s'));
                }
                
            
                $query = 'UPDATE ' . $this->_datamodel->getTableName() . ' SET';
                foreach ($fields as $num => $arr) {
                    $query .= ' ' . $arr[0] . '=\'' . $arr[1] . '\'';
                    if ($num<count($fields)-1) $query .= ',';
                }
                
                $query .= ' WHERE ' . $this->_datamodel->getPrimaryKeyField() . ' = \''. $this->escape($update_id) . '\'';
                if ($this->_datamodel->hasGlobalWhere()) $query .= $this->formGlobalWhere();
            
            }
            
            
            /* we're adding a completely new entry in the database */
            else {
            
                if ($this->_datamodel->isFrameworkValid()) {
                    $fields[] = array('date_lastedit',date('Y-m-d H:i:s'));
                    $fields[] = array('date_added',date('Y-m-d H:i:s'));
                }
            
                $query = 'INSERT INTO ' . $this->_datamodel->getTableName() . ' (';
                
                foreach ($fields as $num => $arr) {
                    $query .= $arr[0];
                    if ($num<count($fields)-1) $query .= ',';
                }
                
                $query .= ') VALUES(';
                foreach ($fields as $num => $arr) {
                    $query .= '\'' . $arr[1] . '\'';
                    if ($num<count($fields)-1) $query .= ',';
                }
                
                $query .= ')';
                
                
            }

            return $this->query($query);
        
        }
        
        /**
         * insert()
         *
         * inserts a new entry in the database
         */
        public function insert($data, $load_to_cache = true) {
        
            if ($load_to_cache === true) {
                
                $this->clearLoadedEntry();
                foreach ($data as $field => $value) {
                    $this->$field = $value;
                }
                
                return $this->save();
                
            }
            
            return $this->save($data, $update_id = false, $insert = true);
        
        }
        
        /**
         * update()
         *
         * updates the current checked-out entry or the one given by $id
         */
        public function update($data, $id=false) {
        
            if (!$id) $id = $this->loadedEntry;
            if (!$id) return false;
            
            foreach ($data as $var => $val) {
                $this->$var = $val;
            }

            // return false if the query failed
            if(!$this->save($data = '', $update_id = $id)) return false;
            
            // if not, reload entry to get the current values
            $this->get($id);
            
        }
        
        /**
         * delete()
         *
         * deletes the current checked-out entry or the one given by $id
         * does a real delete if table construct isn't framwork valid or $hard is set to true,
         * otherwise it sets deleted to true.
         */
        public function delete($id=false, $hard=false) {
        
            if (!$id) $id = $this->loadedEntry;
            if (!$id) return false;
            
            if ($id == $this->loadedEntry) $this->clearLoadedEntry();
            
            if ($this->_datamodel->isFrameworkValid()&&$hard!=true) $query = 'UPDATE ' . $this->_datamodel->getTableName() . ' SET deleted = 1';
            else $query = 'DELETE FROM ' . $this->_datamodel->getTableName();
            
            $query .= ' WHERE';
            
            if (!is_array($id)) $id = array($id);
            
            foreach ($id as $num => $key) {
            
                if ($num > 0) $query .= ' OR';
                $key    = $this->escape($key);
                $query .= ' ' . $this->_datamodel->getPrimaryKeyField() . ' = \'' . $key . '\'';
            
            }
            
            return $this->query($query);
        
        }
        
        /**
         * returnQueryResult()
         *
         * returns an array of results which have been returned by MySQL
         */
        protected function query($query) {

            $return = array();
            
            if (!$result = $this->_db->query($query)) {
                if (Option::val('debug')) throw new Exception('MySQL Error #'. $this->_db->errno.': ' . htmlspecialchars($this->_db->error). ' <br /><br />Query: <strong>' . htmlspecialchars($query) . '</strong>');
                return false;
            }
            
            if ($result instanceof mysqli_result) {
            
                while ($row = $result->fetch_assoc()) {
                    
                    foreach ($row as $field => $value) {
                        foreach ($this->_datamodel->convertFromDb($field, $value) as $key => $data) {
                            $row[$key] = $data;
                        }
                    }
                    
                    $return[] = $row;
                }
                
                
                $result->free();
                return $return;
                
            }
            
            return true;
        
        }
        
        /**
         * loaded()
         *
         * returns whether an entry is currently loaded or not
         */
        public function loaded() {
        
            return $this->loadedEntry !== false;
        
        }
        
        /**
         * lastInsertId()
         *
         * gets the ID of the last inserted entry
         */
        public function lastInsertId() {
        
            return $this->_db->insert_id;
            
        }
        
        /**
         * escape()
         *
         * alias for MySQLi::real_escape_string()
         */
        public function escape($str) {
        
            return $this->_db->real_escape_string($str);
        
        }
        
        /**
         * formGlobalWhere()
         *
         * reads the global-where array from the datamodel and transforms it to an escaped string
         */
        public function formGlobalWhere() {
        
            $where     = $this->_datamodel->getGlobalWhere();
            $where_str = '';
            $num       = 0;
            
            foreach ($where as $field => $value) {
                if ($num > 0) $where_str .= ' AND';
                $where_str .= ' ' . $this->escape($field) . ' = \'' . $this->escape($value) . '\'';
            }
            
            return $where_str;
        
        }
    
    }
    
?>