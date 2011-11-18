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
     
     
    abstract class DateTimeExtension extends DateTime {
    
        protected $translation = array(); // Use the same format as in DateTimeExtension::$original
        protected $timezone, $original = array(
            'days' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
            'months' => array('January','February','March','April','May','June','July','August','September','October','December')
        );
    
        /**
         * __construct()
         *
         * only throw construct exception if debug is allowed
         * if giving data given by a user into the constructor, the system won't fail
         * in production environment
         */
        public function __construct($time='now', $timezone=null) {
        
            $this->timezone = (isText($this->timezone)) ? $this->timezone : date_default_timezone_get();
        
            if (is_null($timezone))
                $timezone = new DateTimeZone($this->timezone);
        
            try {
                return parent::__construct($time,$timezone);
            }
            catch (Exception $e) {
            
                // only throw exception if debug is enabled
                if (Option::val('debug'))
                    throw new Exception($e);

                // if not, return current time
                return new DateTime();
            }
        
        }
        
        /**
         * format()
         *
         * gets the string of the parent function, replaces relevant translation data
         * and returns new string
         */
        public function format($format) {
        
            $str = parent::format($format);
            $str = str_replace($this->original['days'], $this->translation['days'], $str);
            $str = str_replace($this->original['months'], $this->translation['months'], $str);
            
            return $str;

        }
    
    }
    
    
?>