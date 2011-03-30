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
     
     
     /**
      * objectIntoArray()
      * converts an object into an array
      * usefull when using SimpleXMLElement
      */
    function objectIntoArray($arrObjData, $arrSkipIndices = array()) {
     
        $arrData = array();
           
        // if input is object, convert into array
        if (is_object($arrObjData)) {
            $arrObjData = get_object_vars($arrObjData);
        }
           
        if (is_array($arrObjData)) {
            foreach ($arrObjData as $index => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = objectIntoArray($value, $arrSkipIndices); // recursive call
                }
                if (in_array($index, $arrSkipIndices)) {
                    continue;
                }
                $arrData[$index] = $value;
            }
        }
        return $arrData;
        
    }
    
    /**
     * strpos_all()
     * returns an array with ALL positions of $needle found in $haystack
     */
    function strpos_all ($haystack, $needle, $case=true) {
    
        $return   = array();
        $continue = 0;

        while ($continue !== false) {
        
            $pos = ($case) ? strpos($haystack,$needle,$continue) : stripos($haystack,$needle,$continue);
            if ($pos === false) break;
            $return[] = $pos;
            $continue = $pos+strlen($needle);
        
        }
        
        return $return;
    
    }
    
    /**
     * lowest()
     * returns the lowest value from given values
     */
    function lowest() {

        $args = func_get_args();
        
        foreach ($args as $key => $val) {
            if (!is_numeric($val)) unset($args[$key]);
        }
        sort($args);
        
        return $args[0];
    
    }
