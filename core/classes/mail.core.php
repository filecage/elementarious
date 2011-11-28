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
     
    class Mail extends Templateparser {
    
        /**
         * Used to store the mail-individual variables
         */
        protected $vars = array();
        
        /**
         * Sender e-mail address and sender name
         */
        protected $senderAddress = null, $senderName = '';
        
        /**
         * Subject of email
         */
        protected $subject = null;
        
        /**
         * Allow html in email (this will not filter HTML out of a template but send the right headers)
         */
        protected $allowHtml = false;
        
        
    
        /**
         * __construct()
         *
         * loads the requested template file
         */
        public function __construct($tpl, $subject='') {
        
            // turn of XML parsing
            $this->useXML = false;
        
            if (!Option::val('mail_allow'))
                throw new Exception('Mail::__construct() called while mail system is disabled by configuration; Enable it or delete mail call.');
        
            $this->subject = $subject;
            $this->senderAddress = (is_null($this->senderAddress)) ? Option::val('mail_sender_address') : $this->senderAddress;
            $this->senderName = (!isText($this->senderName)) ? Option::val('mail_sender_name') : $this->senderName;

            $file = 'mail/'.$tpl;
            $this->sourceFile = $this->getFile('/'.$tpl, 'mail', $noxml=true);

            if (!$this->parseCachedFile($file))
                $this->parse();

        }
        
        /**
         * parse()
         *
         * parses the template without calling the usual XML parser
         */
        protected function parse() {
        
            $this->buffer = $this->parseVars($this->sourceFile);
            $this->cache();
                
        }
        
        /**
         * send()
         *
         * sends the parsed email file to the given address
         */
        public function send($mail, $name='') {
        
            if (!isset($this->senderAddress))
                throw new Exception('Mail::send() called, but no senderAddress defined. Call Mail::setSenderAddress() before sending emails or set config option "mail_sender_address".');
        
            if (!check_email($mail))
                return false;
                
            $recipient = (isText($name)) ? $mail . ' <' . $name . '>' : $mail;
            $sender = (isText($this->senderName)) ? $this->senderAddress . ' <' . $this->senderName . '>' : $this->senderAddress;
            
            if (!mail($recipient, $this->subject, $this->callPHP($this->buffer), 'From: ' . $sender))
                return false;
            
            return $this;
        
        }
        
        /**
         * setVar()
         *
         * sets a variable for the mailtemplate
         */
        public function setVar($var, $value=true) {
            $this->vars[$var] = $value;
            return $this;
        }
        
        /**
         * setVars()
         *
         * sets multiple vars for the mailtemplate
         */
        public function setVars($array) {
            
            foreach ($array as $var => $value) {
                $this->setVar($var,$value);
            }
            
            return $this;
            
        }
        
        /**
         * getVars()
         *
         * returns the individual vars for this mailtemplate
         * redefinition for PHP parser
         */
        protected function getVars() {
            return $this->vars;
        }
        
        /**
         * allowHtml()
         *
         * set to false or true
         */
        public function allowHtml($option = false) {
        
            if (is_bool($option))
                $this->allowHtml = $option;
            
            return $this;
            
        }
        
        /**
         * setSenderAddress()
         *
         * sets the sender email adress
         */
        public function setSenderAddress($address) {
        
            if (check_email($address))
                $this->senderAddress = $address;
            else
                throw new Exception('Sender address "' . $address . '" is no valid email address.');
                
            return $this;
            
        }
        
        /**
         * setSenderName()
         *
         * sets the sender name
         */
        public function setSenderName($name) {
            $this->senderName = $name;
            return $this;
        }
        
        /**
         * setSubject()
         *
         * sets the emails subject
         */
        public function setSubject($subject) {
            $this->subject = $subject;
            return $this;
        }
    
    }
    
?>