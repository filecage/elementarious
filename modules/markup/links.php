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
     
    class Markup_Extension_menu extends Markup_Extension {
    
        protected $_endTag = '</a>';

        public function get($args=null) {
        
            $href = trim($args['for'],'/');
        
            $return = '<a href="' . dirname($_SERVER['PHP_SELF']) . '/' . $href . '"';
            if ('/'.$href == Option::val('page')) $return .= ' class="active"';
            elseif ($args['for'] == '/' && Option::val('page') == '/index') $return .= ' class="active"';
            
            return $return . '>';
        
        }
        
    }
    
    class Markup_Extension_intern extends Markup_Extension {
    
        protected $_endTag = '</a>';
        protected $_pattern;
        
        public function get($args) {
        
            $this->_endTag = '</a>';
            $this->_endTag .= (isset($args['end'])) ? '' : '&nbsp;';
            
            $tag = '&nbsp;<a href="' . dirname($_SERVER['PHP_SELF']) . '/' . $args['to'] . '"';
            
            if (isset($args['click']) && isText($args['click'])) {
                $tag .= ' onclick="' . $args['click'].';';
                if ($args['ret_false']) $tag .= 'return false;';
                $tag .= '"';
            }
            
            
            return $tag . '>';
            
        }
        
    }
    
?>