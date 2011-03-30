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
    
    abstract class Controller extends Sitebuilder {
    
        protected $_vars;
    
        final public function __construct() {
        
            if (isset($this->_expectStatementInUri) && $this->_expectStatementInUri == true) $this->prepareRequestStatementInUri();
        
            global $_VAR;
            $this->_vars = $_VAR;
        
            $this->work();
            if (isset($this->_pageTitle)) Templatevars::set('_pageTitle', $this->_pageTitle);
        
        }
        
        final protected function set($name, $val) {
        
            Templatevars::set($name,$val);
        
        }
        
        final protected function prepareRequestStatementInUri() {
        
            if (Option::val('header_enable_urlget')) {
            
                    Option::set('paramsInUri', true);
                    return true;

            }
            else {
                
                throw new Exception('::prepareRequestStatementInUrl() disallowed by configuration file. Please set header_enable_urlget to true.');
                
            }
        
        }
        
        protected function work() {}
    
    }
    
 ?>