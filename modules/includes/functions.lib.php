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
      *
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
     *
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
     *
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
    
    /**
     * isText()
     *
     * checks if text is set and not empty, if trim is set to true (default), trims before it checks
     */
    function isText($text='', $trim=true, $text_in_array=false) {
    
        if (is_array($text)&&$text_in_array) {
            $return = true;
            foreach ($text as $line) {
                $return = (isText($line, $trim)) ? $return : false;
            }
            return $return;
        }
    
        if (!is_string($text)) return false;
    
        if ($trim) $text = trim($text);
        return !empty($text);
        
    }
    
    /**
     * isDatetimeArray()
     *
     * checks if array is valid for datetime conversion
     */
    function isTimeArray($array,$datetime=true) {
    
        if (!is_array($array)) return false;
        
        $required = array('year','month','day');
        $return   = true;
        if ($datetime) $required = array_merge($required,array('hour','minute','second'));
    
        foreach ($required as $field) {
            if (!isset($array[$field])) $return = false;
        }
        
        return $return;
        
    }
    
    /**
    * check_email_address()
    *
    * checks whether an email address is correct or not
    */
    function check_email($email) {

        return filter_var($email, FILTER_VALIDATE_EMAIL);
    
    }
    
    /**
     * val_to_array()
     *
     * adds a numeric value to all elements of an array
     */
    function val_to_array($array, $val) {
    
        foreach ($array as $key => $orig_val) {
            $array[$key] = $orig_val + $val;
        }
        
        return $array;
    
    }
    
    /**
     * str_replace_once()
     *
     * replaces only the first occurrence of $needle
     */
    function str_replace_once($needle , $replace , $haystack){

        $pos = strpos($haystack, $needle);
        
        if ($pos === false)
            return $haystack;

        return substr_replace($haystack, $replace, $pos, strlen($needle));
        
    }
    
    /**
     * amount_decode()
     *
     * decodes a varchar value to an integer or float value, based on the locale setting
     */
    function amount_decode($amount) {
    
        if (is_float($amount)||is_int($amount))
            return $amount;
            
        switch (Option::val('locale')) {
        
            case 'de_de':
                return number_format(str_replace(',', '.', str_replace('.','', $amount)), 2, '.', '');
            break;
            case 'en_en':
                return number_format(str_replace(',','', $amount), 2, '.', '');
            break;
            default:
                return number_format(str_replace(',','', $amount), 2, '.', '');
            
        }
    
    }
    
    /**
     * amount_encode()
     *
     * encodes an integer or float value to a varchar, based on the locale setting
     */
    function amount_encode($amount) {
    
        if (!is_int($amount)&&!is_float($amount))
            $amount = (float)$amount;
    
        switch (Option::val('locale')) {
        
            case 'de_de':
                return number_format($amount, 2, ',', '.');
            break;
            case 'en_en':
                return number_format($amount, 2, '.', ',');
            break;
            default:
                return number_format($amount, 2, '.', ',');
        
        }
    
    }
    
    /**
     * array_next_key()
     *
     * returns the value of the element followed by $key
     * returns the given element if the key is the last one
     */
    function array_next_key($array, $key) {
    
        $return_val = $array[$key];
        $get_next   = false;
        
        foreach($array as $a_key => $val) {
        
            if ($get_next) {
                $return_val = $array[$a_key];
                break;
            }
        
            if ($a_key == $key)
                $get_next = true;
        
        }
        
        return $return_val;
    
    }
    
    /**
     * hex2rgb()
     *
     * returns an assoziative array (r,g,b) based on the hex value
     */
    function hex2rgb($hex) {
        $color = str_replace('#','',$hex);
        $rgb = array(
            'r' => hexdec(substr($color,0,2)),
            'g' => hexdec(substr($color,2,2)),
            'b' => hexdec(substr($color,4,2)));
        return $rgb;
    }
    
    /**
     * rgb2cmyk
     *
     * turns a rgb value into a cmyk value
     */
    function rgb2cmyk($var1) {
        if(is_array($var1)) {
            $r = $var1['r'];
            $g = $var1['g'];
            $b = $var1['b'];
        }
        else { 
            $r=$var1;
            $cyan    = 255 - $r;
            $magenta = 255 - $g;
            $yellow  = 255 - $b;
            $black   = min($cyan, $magenta, $yellow);
            $cyan    = @(($cyan    - $black) / (255 - $black)) * 255;
            $magenta = @(($magenta - $black) / (255 - $black)) * 255;
            $yellow  = @(($yellow  - $black) / (255 - $black)) * 255;
            return array(
                'c' => $cyan / 255,
                'm' => $magenta / 255,
                'y' => $yellow / 255,
                'k' => $black / 255
            );
        }
    }

    
?>