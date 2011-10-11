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
    
    interface Filewriter_Interface {
    
        public function __construct();
        public function __destruct();
        public function _write();
    
    }
     
    abstract class Filewriter implements Filewriter_Interface {
    
        protected $_fileRes;

        /**
         * ::__construct()
         * checks if target directory and target file are writeable
         * creates file if neccessary (if ::_$writeMode is set either to w(+) or a(+))
         * throws an exception if file or directory is not writeable
         */
        public function __construct($file=null,$directory=null) {
        
            if (!is_null($file)) $this->_targetFile = $file;
            if (!is_null($directory)) $this->_targetDirectory = $directory;

            if (!is_writeable($this->_targetDirectory)) throw new Exception ('Target directory (' . $this->_targetDirectory . ') is not writeable');
            if (!file_exists($this->_targetDirectory . $this->_targetFile)) $this->_prepareFile();
            if (!is_writeable($this->_targetDirectory . $this->_targetFile)) throw new Exception ('Target file (' . $this->_targetFile . ') is not writeable');
            if (!is_resource($this->_fileRes)) $this->_prepareFile();
        
        }
        
        /**
         * ::_write()
         * writes data to the previously opened filepointer
         * if the method is called without arguments, it gets the data from the member variable ::$_data
         */
        public function _write($data=null) {
        
            if (is_null($data)) $data = $this->_data;
            fwrite($this->_fileRes, $data);
        
        }
        
        /**
         * ::_prepareFile()
         * creates the file pointer and stores it in ::_$fileRes
         * should usually be called in the constructor
         */
        protected function _prepareFile() {
        
            $this->_fileRes = fopen($this->_targetDirectory . $this->_targetFile, $this->_writeMode);
        
        }
        
        /**
         * ::__destruct()
         * closes file resource if still opened
         */
        public function __destruct() {
        
            if (is_resource($this->_fileRes)) fclose($this->_fileRes);
            
        }
    
    }
    
?>