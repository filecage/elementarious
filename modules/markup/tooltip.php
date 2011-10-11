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
     
    class Markup_Extension_tooltip extends Markup_Extension {
    
        protected $_endTag = '</div><div class="footer"></div></div>';
        #protected $_pattern = '<a href="#" id="<div class="tooltip">%';
        
        public function get($args=null) {
        
            $id = strtolower(preg_replace('/\W/u', '_', $args['short']).uniqid());
            $href = (isset($args['href'])) ? $args['href'] : '#';
        
            $return = '<a href="' . Option::val('path') . '/' . $href . '" id="t_' . $id . '" class="tooltip_trigger';
            if (isset($args['noicon'])&&$args['noicon']=='true') $return .= ' noicon';
            
            $return .= '">' . $args['short'] . '</a><div class="tooltip';
            if (isset($args['tooltipClass'])) $return .= ' ' . $args['tooltipClass'];
            
            return $return . '" id="c_' . $id . '"><div class="inner">';
        
        }
        
    }
    
?>