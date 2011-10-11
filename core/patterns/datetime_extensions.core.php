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

    class DateTime_de_DE extends DateTime {
    
        public function format($format) {
        
            $str = parent::format($format);
        
            $english = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
            $german  = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');
            $str     = str_replace($english, $german, $str);
            
            $english = array('January','February','March','April','May','June','July','August','September','October','December');
            $german = array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','Dezember');
            $str     = str_replace($english, $german, $str);
            
            return $str;
        }
        
    }
?>