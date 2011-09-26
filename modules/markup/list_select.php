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
     
    class Markup_Extension_list_select extends Markup_Extension {

        public function get($args=null) {
        
            if (!isset($args['list'])) return false;
            $list_name = $args['list'];
            
            if (isset($args['autoselect'])) $autoselect = $args['autoselect'];
            else $autoselect = 'post';
            
            if (isset($args['selected'])&&is_numeric($args['selected'])&&$args['selected']>0) $selected = $args['selected'];
            elseif (($autoselect == 'post'||$autoselect=='both')&&isset($_POST[$args['name']])) $selected = htmlspecialchars($_POST[$args['name']]);
            elseif (($autoselect == 'get'||$autoselect=='both')&&isset($_GET[$args['name']])) $selected = htmlsepcialchars($_GET[$args['name']]);
            else $selected = null;
            
            $return = '<select';
            
            if (isset($args['class'])) $return .= ' class="'.$args['class'].'"';
            if (isset($args['name'])) $return .= ' name="'.$args['name'].'"';

            $return .= '><option value="">Bitte wählen</option><option value="">--------------------</option>';
            
            $list = Lists::getItems($list_name);
            
            foreach ($list as $item) {
            
                $return .= '<option value="' . $item['id'] . '"';
                if ($item['id']==$selected) $return .= ' selected="selected"';
                $return .= '>' . $item['text'] . '</option>';
            }
            
            return $return . '</select>';
        
        }
        
    }
    
?>