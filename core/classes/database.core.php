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
     
     
     
    class Database {
    
        static private $db;
        static private $init = false;
    
        static public function init() {
        
            if (self::$init) return false;
        
            if (!Option::val('mysql_allow')) throw new Exception('Fatal: Cannot initialize Database; disabled by configuration. Enable or remove Database call.');
            
            $credentials = Option::val('mysql_credentials');
            Option::set('mysql_credentials',null,true);
            
            if (!isText($credentials['username'])&&!isText($credentials['database']))
                return true;
            
            self::$db   = new mysqli($credentials['server'], $credentials['username'], $credentials['password'], $credentials['database']);
            self::$init = true;

            if (!self::$db) throw new Exception('Fatal: Cannot establish MySQL connection (error #' . self::$db->connect_errno .'): ' . self::$db->connect_error); 
            
            if (Option::val('mysql_force_utf8')) self::$db->set_charset('utf8');
        
        }
        
        static public function getResource() {
        
            return self::$db;
        
        }
    
    
    }
    
 ?>