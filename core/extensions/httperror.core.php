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
     
     
    class HttpError extends Exception {
    
        private $num;

        public function __construct($num, $file=null) {
        
            $this->num = $num;

            $info = Option::val('error_http_messages');
            $info = $info[$num];
            
            //Templatevars::clear();
            Templatevars::set(
                array(
                    '_pageTitle' => $info['name'],
                    'httpErrorOccured' => true,
                    'errorNum' => $num,
                    'errorName' => $info['name'],
                    'errorDescription' => $info['description']
                )
            );
            
            new HttpErrorController();
            
        }
        
        public function getNum() {
            return $this->num;
        }
    
    }
    
    class HttpErrorController extends Controller {}
    
?>