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

    $time = microtime(true);
     
    // define class autoloader
    function __autoload($class) {
        Classloader::load($class);
    }
    
    // define pathes
    define('ROOT_PATH', str_replace ( '\\', '/' , dirname(__FILE__)=='/'?'':dirname(__FILE__) . '/../'));
    
    // we only define pathes which do not contain any php code (especially classes)
    // classes are being included by the classloader when calling the constructor (except the classloader-CORE, which is being loaded below)
    define('CACHE_PATH',    ROOT_PATH . 'cache/');
    define('CONFIG_PATH',   ROOT_PATH . 'config/');
    define('CORE_PATH',     ROOT_PATH . 'core/');
    define('PUBLIC_PATH',   ROOT_PATH . 'public/');
    define('VIEW_PATH',     ROOT_PATH . '__view/');
    define('INCLUDE_PATH',  ROOT_PATH . 'modules/includes/');
    
    require_once(CORE_PATH . 'patterns/filewriter.core.php');
    require_once(CORE_PATH . 'classes/classloader.core.php');
    require_once(INCLUDE_PATH . 'functions.lib.php');
    
    // go trough some options which may be turned on (or off)
    if (Option::val('compression_enable_gzip')) ob_start('ob_gzhandler');
    if (Option::val('header_send_utf8')) Header('Content-type: text/html;charset=utf-8;');
    if (Option::val('debug')) error_reporting(E_ALL);
    else error_reporting(E_NONE);
    
    
    // start trying to create the site
    try {
    
        $site = new Sitebuilder();
        $site->create();
        echo $site->get();
    
    }
    // catch possible thrown http errors
    catch (HttpError $e) {
        
        Header('HTTP/1.1 ' . $e->getNum() );
        $error = new Templateparser();
        die($error->get('/error/http'));
        
    }
    // catch possible thrown exceptions
    catch (Exception $e) {
        
        die('Exception: '. $e->getMessage());
        
    }
    
    if (Option::val('debug')) echo '<div style="position:absolute;top:0px;left:0px;width:160px;height:20px;background:#000;color:#fff;">debug time ' . round(microtime(true)-$time,6). 's</div>';
    
 ?>