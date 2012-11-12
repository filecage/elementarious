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
     
     
    class Location {
    
        /**
         * Coordinates of the location
         */
        protected $longitude = null,
                  $latitude = null;
        
        /**
         * array containing street, streetnumber, zip, city, country and state
         */
        protected $address,
                  $addressSchema = array('street' => '',
                                   'streetnumber' => '',
                                   'zipcode' => '',
                                   'city' => '',
                                   'country' => '',
                                   'state' => '');
                            
        /**
         * object name and id
         */
        protected $name = null,
                  $id = null;
        
        
        /**
         * __construct()
         *
         * class constructor expects latitude and longitude, otherwhise an exception is thrown
         * creating an instance of this class without these data would not make any sense
         */
        public function __construct($latitude,$longitude) {
        
            if (!is_numeric($longitude)||!is_numeric($latitude))
                throw new Exception('Location::__construct() called without (or with invalid) coordinates data');
                
            $this->longitude = $longitude;
            $this->latitude  = $latitude;
            
            $this->id = microtime(true);
            $this->name = (isText($this->name)) ? $this->name : 'Location #' . $this->id;
            
            $this->address = (is_array($this->address)) ? $this->address : $this->address_schema;

        }
        
        /**
         * getDistance()
         *
         * returns the distance between this object and another location object in kilometres
         */
        public function getDistance($object) {
        
            if (!$object instanceof Location)
                return false;
                
            $target = $object->getCoordinates();
            $self   = array();

            $target['lat'] = deg2rad($target['latitude']);
            $self['lat']   = deg2rad($this->latitude);
            
            $ball   = sin($self['lat']) * sin($target['lat']);
            $ball2  = cos($self['lat']) * cos($target['lat']) * cos($target['lon'] - $self['$lon']);
            
            return acos(sin($self['lat'])*sin($target['lat'])+cos($self['lat'])*cos($target['lat'])*cos(deg2rad($this->longitude - $target['longitude'])))*6371;

        }
        
        
        
        /**
         * setName()
         *
         * sets the name of the location object
         */
        public function setName($name) {
            $this->name = $name;
        }
        
        /**
         * getName()
         *
         * returns the location objects name
         */
        public function getName() {
            return $this->name;
        }
        
        
        /**
         * setCoordinates()
         *
         * sets new coordinates for the location object
         */
        public function setCoordinates($latitude, $longitude) {
        
            if (!is_numeric($latitude)||!is_numeric($longitude))
                return false;
                
            $this->latitude = $latitude;
            $this->longitude = $longitude;
            
            return true;
            
        }
        
        /**
        * getCoordinates()
        *
        * returns an assoziative array with latitude and longitude
        */
        public function getCoordinates() {
            return array('longitude'=>$this->longitude,'latitude'=>$this->latitude);
        }
         
        
        /**
         * setAddress()
         *
         * sets the location's full address, expects an array
         * does only set keys predefined in Location::$addressSchema
         */
        public function setAddress($address) {
            
            foreach ($this->addressSchema as $key => $empty) {
                $this->address[$key] = $address[$key];
            }
            
            return true;

        }
        
        /**
         * getAddress()
         *
         * returns the address array predefined or the empty address schema if nothing defined yet
         */
        public function getAddress() {
            return $this->address;
        }
        
        
        /**
         * setStreet()
         *
         * sets the location's street
         * additional second parameter can set streetnumber
         */
        public function setStreet($street, $streetnumber=null) {
            
            if (!is_null($streetnumber))
                $this->address['streetnumber'] = $streetnumber;
                
            $this->address['street'] = $street;
            return true,
        
        }
        
        /**
         * getStreet()
         *
         * returns the location's street
         * giving true as first parameter will cause the return being an assoziative array with street
         * and streetnumber
         */
        public function getStreet($with_streetnumber=false) {
        
            if ($with_streetnumber)
                return array(
                    'street' => $this->address['street'],
                    'streetnumber' => $this->address['streetnumber']
                );
                
            return $this->address['street'];
            
        }
        
        
        /**
         * setStreetnumber()
         *
         * sets the location's streetnumber
         */
        public function setStreetnumber($streetnumber) {
            $this->address['streetnumber'] = $streetnumber;
            return true;
        }
        
        /**
         * getStreetnumber()
         *
         * returns the location's streetnumber
         */
        public function getStreetnumber() {
            return $this->address['streetnumber'];
        }
        
        
        /**
         * setZipcode()
         *
         * sets the location's zipcode
         */
        public function setZipcode($zipcode) {
            $this->address['zipcode'] = $zipcode;
            return true;
        }
        
        /**
         * getZipcode()
         *
         * returns the location's zipcode
         */
        public function getZipcode() {
            return $this->address['zipcode'];
        }
        
        
        /**
         * setCity()
         *
         * sets the location's city
         */
        public function setCity($city) {
            $this->address['city'] = $city;
            return true;
        }
        
        /**
         * getCity()
         *
         * returns the location's city
         */
        public function getCity() {
            return $this->address['city'];
        }
        
        
        /**
         * setCountry()
         *
         * sets the location's country
         */
        public function setCountry($country) {
            $this->address['country'] = $country;
            return true;
        }
        
        /**
         * getCountry()
         *
         * returns the location's country
         */
        public function getCountry() {
            return $this->address['country'];
        }
        
        
        /**
         * setState()
         *
         * sets the location's state
         */
        public function setState($state) {
            $this->address['state'] = $state;
            return true;
        }
        
        /**
         * getState()
         *
         * returns the location's state
         */
        public function getState() {
            return $this->address['state'];
        }
        
    }

?>