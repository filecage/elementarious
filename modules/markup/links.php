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
    
        protected $_endTag = '</a>&nbsp;';

        public function get($args=null) {
        
            $href = trim($args['for'],'/');
        
            $return = '<a href="' . Option::val('path') . '/' . $href . '"';
            if ('/'.$href == Option::val('page')) $return .= ' class="active"';
            elseif ($args['for'] == '/' && Option::val('page') == '/index') $return .= ' class="active"';
            
            return $return . '>';
        
        }
        
    }
    
    class Markup_Extension_intern extends Markup_Extension {
    
        protected $_endTag = '</a>';
        protected $_pattern;
        
        public function get($args) {
        
            if (isset($args['alone'])&&$args['alone']=='true') {
                $args['end'] = 'true';
                $args['start'] = 'true';
            }
        
            $this->_endTag = '</a>';
            $this->_endTag .= (isset($args['end'])&&$args['end']=='true') ? '' : '&nbsp;';
            
            $tag = (isset($args['start'])&&$args['start']=='true') ? '' : '&nbsp;';
            
            $tag .= '<a href="' . Option::val('path') . '/' . $args['to'] . '"';
            
            if (isset($args['click']) && isText($args['click'])) {
                $tag .= ' onclick="' . $args['click'].';';
                if (isset($args['ret_false'])&&$args['ret_false']) $tag .= 'return false;';
                $tag .= '"';
            }
            
            if (isset($args['class']) && isText($args['class']))
                $tag .= ' class="' . $args['class'].'"';
            
            
            return $tag . '>';
            
        }
        
    }
    
?>