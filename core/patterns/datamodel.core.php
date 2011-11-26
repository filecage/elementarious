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
     
    abstract class Datamodel {
    
        protected $_tableName;
        protected $_primaryKey;
        protected $_prefixName;
        protected $_frameworkValid;
        protected $_globalWhere;
        protected $_dateFormat;
        
        protected $_lists = array();
        protected $_fields = array();
        protected $_searchFields = array();
        protected $_defaultValue = array();
        protected $_allowedTypes = array('int', 'varchar', 'text', 'bool', 'date', 'datetime', 'timestamp', 'amount', 'array');
        protected $_fieldTypeDefaultValues = array(
            'int' 		=> 0, 
            'varchar' 	=> '',
            'text'		=> '',
            'bool'		=> false,
            'date'		=> -1,
            'datetime'	=> -1,
            'timestamp'	=> 0,
            'amount'    => 0,
            'array'     => array(),
        );
    
    
        /**
        * construct()
        *
        * validates the datamodel, checks if required values are set and calls ::configure()
        */
        final public function __construct() {
        
            if (!Option::val('mysql_allow')) throw new Exception('Datamodel::__construct() called while MySQL is disabled by configuration; enable it or delete database call.');
            if (!isText($this->_tableName)) throw new Exception('Datamodel::__construct() called but ::$_tableName is undefined. Construct not possible.');
            if (!isText($this->_primaryKey)) throw new Exception('Datamodel for table ' . $this->_tableName . ' is invalid; please define the ::_primaryKey attribute.');
            
            $this->_prefixName = isText($this->_prefixName) ? $this->_prefixName : Option::val('mysql_table_prefix');
            $this->_frameworkValid = isset($this->_frameworkValid) ? $this->_frameworkValid : Option::val('mysql_framework_valid');
            $this->_globalWhere = (count(Option::val('mysql_globalwhere'))>0&&!isset($this->_globalWhere)) ? Option::val('mysql_globalwhere') : $this->_globalWhere;
            $this->_dateFormat = (isset($this->_dateFormat)) ? $this->_dateFormat : Option::val('date_format');
            
            $this->configure();
        
        }
        
        /**
         * configure()
         *
         * called right after ::__construct(), define fields here
         */
        protected function configure() {}
        
        /**
         * addField()
         *
         * validates and adds the field with the specified properties to the fieldlist
         */
        final protected function addField($name='', $properties) {
        
            if (!isText($name))
                $this->fieldException('no name definition given for field');
            
            
            // if theres no array we only got the type definition
            if (!is_array($properties)&&isText($properties))
                $properties = array('type'=>$properties);
        
        
            // check if type is correct
            if (!isText($properties['type']))
                $this->fieldException('missing type definition');
                
            elseif (!in_array($properties['type'], $this->_allowedTypes))
                $this->fieldException('invalid type defined');
            

            if ($properties['type'] == 'varchar' && !is_int($properties['length'])) $this->fieldException('varchar field type requires length definition');
            if (!isset($properties['real_fieldname'])) $properties['real_fieldname'] = '';
            if (!isset($properties['allow_html'])) $properties['allow_html'] = false;
            if (!isset($properties['date_format'])) $properties['date_format'] = $this->_dateFormat;
        
            $this->_defaultValue[$name] = isset($properties['default']) ? $properties['default'] : $this->_fieldTypeDefaultValues[$properties['type']];
            $this->_fields[$name] = $properties;
        
        }
        
        /**
         * addList()
         *
         * adds a new list entity
         */
        final protected function addList($list, $field) {
        
            $this->_lists[$field] = $list;
        
        }
        
        /**
         * addGlobalWhereField()
         *
         * specify a global where-clausel for the whole table
         * each query (select or update) will require those values
         * accepts only arrays or bool(false), if configuration-statements should be disabled for this datamodel
         */
        final protected function addGlobalWhereField($where) {
        
            if (is_array($where)) {
                foreach ($where as $field => $value) {
                    $this->_globalWhere[$field] = $value;
                }
            }

            return false;
        
        }
        
        /**
         * setFields()
         *
         * expects an array with multiple fields
         * adds them to the fieldlist by calling addField() for each field
         */
        final protected function setFields($fields) {
        
            if (!is_array($fields)) return false;
            if ($this->_frameworkValid) $fields = array_merge($fields, array('date_added'=>array('type'=>'datetime','readonly'=>true),'date_lastedit'=>array('type'=>'datetime','readonly'=>true)));
            
            foreach ($fields as $name => $properties) {
                $this->addField($name, $properties);
            }
            
            return true;
        
        }
        
        /**
         * defineSearchFields()
         *
         * defines all fields to be used when calling Entity::search()
         */
        final protected function defineSearchFields($fields) {
        
            if (!is_array($fields)) return false;
            if (count($this->_fields)<1) throw new Exception('Datamodel::defineSearchFields() called, but no field definition found. Make sure that Datamodel::setFields() or Datamodel::addField() has been called previously.');
            
            $this->_searchFields = array();
            
            foreach($fields as $name => $method) {
                
                $method = strtolower($method);
            
                if ($method!='complete'&&$method!='full')
                    throw new Exception('Datamodel::defineSearchFields() called with unsupported search method "' . $method . '"');
                    
                if (isset($this->_fields[$name]))
                    $this->_searchFields[] = array('name'=>$name,'method'=>$method);
            }
            
            return true;
        
        }
        
        
        /**
         * getTableName()
         *
         * returns the tablename of the datamodel; uses the default prefix (defined by config) or, if it has been set, the manual set
         */
        public function getTableName() {
        
            return $this->_prefixName . $this->_tableName;
        
        }
        
        /**
         * getSearchFields()
         *
         * returns the searchField array
         */
         public function getSearchFields() {
         
            return $this->_searchFields;
            
        }
        
        /**
         * hasGlobalWhere()
         *
         * returns true if we got global-where-statements, false if not
         */
        public function hasGlobalWhere() {
        
            return $this->_globalWhere !== false && count($this->_globalWhere) > 0;
            
        }
        
        /**
         * getGlobalWhere()
         *
         * returns the global where array
         */
        public function getGlobalWhere() {
        
            return $this->_globalWhere;
            
        }
        
        
        /**
         * convertForDb()
         *
         * converts or changes the given value to a valid database string/value
         */
        public function convertForDb($field, $value) {
        
            $properties = $this->_fields[$field];
            
            switch ($properties['type']) {
            
            
                case 'array':
                    return json_encode($value);
                break;
            
                case 'text':
                    if (!isText($value) && isset($properties['default']) && isText($properties['default'])) $value = $properties['default'];
                    return htmlspecialchars_decode($value);
                break;
            
                case 'varchar':
                    if (isset($properties['list_table'])) {
                        if (!Lists::isListItem($properties['list_table'], $value)) return $properties['default'];
                    }
                    $str = $value;
                    if (strlen($value) > $properties['length']) $str = substr($value,0,($properties['length']-3)).'...';
                    return htmlspecialchars_decode($str);
                break;
                
                case 'int':
                case 'timestamp':
                    if (!is_int($value)||!is_float($value)) $return = floatval($value);
                    if (isset($properties['list_table'])) {
                        if (!Lists::isListItem($properties['list_table'], $value)) return $properties['default'];
                    }
                    return $return;
                break;
                
                case 'date':
                case 'datetime':

                    if (is_numeric($value) && $value < 0) return date('Y-m-d H:i:s');
                    
                    if ($value instanceof DateTime) {
                        return $value->format('Y-m-d H:i:s');
                    }
                    
                    elseif (is_array($value)) {
                    
                        $return = $value['year'] . '-' . $value['month'] . '-' . $value['day'];
                        if ($properties['type']=='datetime'&&isTimeArray($value)) $return .= ' '. $value['hour'] . ':' . $value['minute'] . ':' . $value['second'];
                        
                    }
                    
                    elseif (is_int($value)) {
                    
                        if ($properties['type']=='date') return date('Y-m-d',$value);
                        elseif ($properties['type']=='datetime') return date('Y-m-d H:i:s',$value);
                        
                    }
                    
                    elseif (isText($value)) {
                        return $value;
                    }
                    
                    else {
                        return false;
                    }
                break;
                case 'bool':
                    return ($value) ? 1 : 0;
                break;
                case 'amount':
                    $value = amount_decode($value);
                    if (!is_int($value)||!is_float($value)) $value = floatval($value);
                    return $value;
                break;
                
            }
            
            return $value;
        
        }
        
        /**
         * convertFromDb()
         *
         * converts or changes the given value to a valid string/value that can be used for the view
         */
        public function convertFromDb($field, $value) {
        
            if (!isset($this->_fields[$field])) return array($field=>$value);
            $properties = $this->_fields[$field];
            
            switch ($properties['type']) {
            
                case 'array':
                    $value = json_decode($value,true);
                break;
            
                case 'text':
                    if ($properties['allow_html'] !== true) $value = htmlspecialchars($value);
                break;
            
                case 'varchar':
                    if (isset($properties['list_table'])) {
                        return array($field.'_title'=>Lists::getItem($properties['list_table'],$value),$field=>$value);
                    }
                    if ($properties['allow_html'] !== true) $value = htmlspecialchars_decode($value);
                break;
                
                case 'date':
                case 'datetime':
                    if (strtolower(Option::val('locale')) == 'de_de') $value = new DateTime_de_de($value);
                    else $value = new DateTime($value);
                    return array($field.'_formatted'=>$value->format($properties['date_format']),$field=>$value);
                break;
                
                case 'int':
                    if (isset($properties['list_table'])) return array($field.'_title'=>Lists::getItem($properties['list_table'],$value),$field=>$value);
                break;
                case 'bool':
                    if ($value == 1||$value == 'true') $value = true;
                    else $value = false;
                break;
                case 'amount':
                    return array($field.'_orig'=>$value,$field=>amount_encode($value));
                break;
                
            }
            
            return array($field=>$value);
        
        }
        
        
        
        /**
         * getDefaultValue()
         *
         * returns the default value for a field
         */
        public function getDefaultValue($field) {
        
            return $this->_defaultValue[$field];
        
        }
        
        /**
         * getFieldNames()
         *
         * returns an array of all field names
         * if $write is set to true, it will remove readonly variables
         */
        public function getFieldNames($write=false) {
        
            $names = array();
            
            foreach ($this->_fields as $name => $properties) {
                
                if (!isset($properties['readonly'])||$properties['readonly']==false||$write==false) $names[] = array($name,$properties['real_fieldname']);
              
            }
            
            return $names;
            
        }
        
        /**
         * getFields()
         *
         * returns an array with the fieldnames and their values
         */
        public function getFields() {
        
            $fields = array();
        
            foreach ($this->_fields as $name => $properties) {
                $fields[$name] = $this->_defaultValue[$name];
            }
            
            return $fields;
            
        }
        
        /**
         * isField()
         *
         * returns true if $field is defined in the datamodel, false if not
         */
        public function isField($field) {
        
            return isset($this->_fields[$field]);
            
        }
        
        /**
         * getFieldName()
         *
         * returns the field name of the specified field; only neccessary if theres an alternative field name
         */
        public function getFieldName($field) {
        
            return isText($this->_fields[$field]['real_fieldname']) ? $this->_fields[$field]['real_fieldname'] : $field;
            
        }
        
        /**
         * isFrameworkValid()
         *
         * returns if the table is framework valid
         */
        public function isFrameworkValid() {
        
            return $this->_frameworkValid;
            
        }
        
        /**
         * getPrimaryKeyField()
         *
         * returns the primary key fieldname of the table
         */
        public function getPrimaryKeyField() {
        
            return $this->_primaryKey;
            
        }
        
        /**
         * fieldException()
         *
         * throws an exception with a specific error message when a field is not defined properly; adds information about the datamodel to allow better debugging
         */
        final protected function fieldException($text,$field='') {
        
            $field = isText($field) ? '.' . $field : '';
            throw new Exception('Datamodel::addField() called with invalid arguments ('.$this->getTableName() . $field.'); '.$text);
        
        }
        
        
    }