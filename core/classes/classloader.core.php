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
    
    
    class Classloader_Cachebuilder extends Filewriter {
    
        protected $_targetDirectory = CACHE_PATH;
        protected $_targetFile = 'classcache.json';
        protected $_writeMode = 'w';
    
    }
     
    class Classloader {
    
        static private $cache;
        static private $init   = false;
        static private $ignore = array('.', '..');
        static private $files  = array();
        static private $search = false;
    
    
        /**
         * ::init()
         * this method has to be called BEFORE we do anything
         * it loads the classnames from the cache and creates a new one if neccessary
         * this method is actually the same as __construct(), except that it's just static
         */
        static public function init() {
            
            self::$cache = @json_decode(file_get_contents(CACHE_PATH . 'classcache.json'),true);
            self::$init  = true;
            
            if (count(self::$cache) < 1) self::search();

        }
    
        /**
         * ::load()
         * used to load a new class or instance an object
         * it checks if the class has been defined yet and it not, it defines it
         * defining is based on the cache which allows us to store any class in any file
         */
        static public function load($class) {
        
            if (!self::$init) self::init();

            if (!class_exists($class)) include_once(self::getFileName($class));
        
            // if the class is still not defined, the file has been renamed or restored (or deleted)
            if (!class_exists($class)) {
                self::search();
                include_once(self::getFileName($class));
            }
        
        }
        
        /**
         * ::getFileName()
         * used to get a file name of a class out of the cache
         * if the class is not in the cache, ::getFileName() starts a new directory scan
         * (this might take a few secs but it should usually only happen in your dev-environment
         */
        static public function getFileName($class,$allowfail=false,$nosearch=false) {
        global $time;
            $class = strtolower($class);
            
            if (isset(self::$cache[$class])) return self::$cache[$class];
            if ($nosearch&&!Option::val('debug')) return false;

            
            self::search();
            if (isset(self::$cache[$class])) return self::$cache[$class];
            
            if ($allowfail) return false;
            throw new Exception ('Classloader called to load unexisting or wrong-stored (not in the application directory) class ' . $class . '.');
        
        }
        
        /*
         * ::search()
         * used to scan the whole root-directory for class definitions.
         * writes them into a json-decoded file in the cache-directory for later use
         * scanning may be very time-intensive so we try to use it not too often.
         */
        static private function search() {
        
            if (self::$search == true) return false;

            // this opens up a new filewriter, defined above this static class
            $cache = new Classloader_Cachebuilder();
        
            // this method scans the directory and stores all files into the static variable $files
            self::getFiles();
            $ret = array();
        
            foreach (self::$files as $file) {
            
                foreach (file($file) as $line) {
                    
                    preg_match ('/(class|interface) ([A-Za-z0-9-_]+)[^{]+?{/i', $line, $match);
                    
                    if (count($match)>0) $ret[strtolower($match[2])] = $file;
                
                }
            
            }
            
            // write to cachefile and store new entries
            $cache->_write(json_encode($ret));
            self::$cache  = $ret;
            self::$search = true;
            
            
        }
        
        
        /**
         * ::getFiles()
         * used to get all files in the whole directory
         * goes recursively trough the whole environment and writes every file in an array
         * does only return true. The result is stored in the static variable $files.
         */
        static private function getFiles($path='..') {

            $dh  = @opendir($path);
            $ret = array();
            
            while (($file = readdir($dh)) !== false) {
            
                if(!in_array($file, self::$ignore)) {
                    
                    if(is_dir($path . '/' . $file)) {

                        self::getFiles($path . '/' . $file);
                    
                    } 
                    else {
                    
                        self::$files[] = $path . '/' . $file;
                    
                    }
                
                }
            
            }
            
            closedir( $dh );
                
        }
    
    }
    
    /**
     * Elementarious allows you to use class-aliases stored as own class in /modules/alias/
     * You can either only define the private variable $_originalClass, which will cause overwriting
     * this class with another. However, it's not as usefull as other things you can do with alias, e.g.
     * instancing a class called DatabaseEngine which instances 3 more classes as properties when calling
     * the constructor. I.e. calling Classloader::load('DatabaseEngine'); will return an object with other
     * objects as property.
     */
    abstract class Object_Alias {
    
        public function __construct() {
        
            if (isset($this->_originalClass)) {
            
                return Classloader::load($this->_originalClass);
                
            }
        
        }
    
    }
    
?>