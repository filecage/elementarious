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
     
     
    $config = array(

        /**
         * Markup-Relevant settings
         */
        'html_doctype' => '<!DOCTYPE html>',  // The doctype to send on top of the document 
        'html_markupextension_enabled' => true, // Allow markupextensions
        'html_markupextension_standalone' => array('link','meta','img','input','br','hr','list_select'), // All markup elements which can end by a simple /> (standalone). Markupextensions don't need to be added as they will be ended by Extension::endTag
        
        /**
         * Compression
         */
        'compression_enable_gzip' => true, // Whether to enable gzip compression or not

        /**
         * Locale settings
         */
        'locale' => 'de_de', // Which locale should be used? DateTime objects from database will be extended to use the right names; see /core/patterns/datetime_extensions.core.php
        'date_format' => 'd. F Y', // Which standard date format should be used
        
        /**
         * Headers
         */
        'header_send_utf8' => true, // Whether to send proper UTF-8 charset (turn on when you're having trouble with correct UTF-8 source files but still enconding problems)
        'header_enable_shortget' => false, // Whether to enable parsing GET parameters with no value but setted to be TRUE or unset. (e.g. http://www.example.org?myexampletag is turned into $_GET['myexampletag'] = true if turned on, NULL if turned off)
        'header_enable_urlget' => true, // Whether to enable parsing GET parameters in the URL, e.g. www.example.org/existingfile/foo/bar will be parsed as file:existingfile, $_GET['foo'] = bar. Has to be enabled for every controller expecting such data by Controller:prepareRequestStatementInUri().
        'header_allow_crossdomain_xhr' => false, // Whether to allow crossdomain ajax requests on this page
        'header_start_session' => false, // If set to true, elementarious will create a session for each user, not depending on if any session value has been set or not
        
        /**
         * Error configuration
         */
        'debug' => true, // Whether to enable debug mode or not. Do NOT turn off when in development environment! This would cause elementarious NOT to search for new created controllers or markup classes. Must be turned off in productive environment
        'debug_show_info' => true, // Whether to show debug information (e.g. parsing time) when debug is set to true
        'error_http_messages' => array( // The http error messages shown when the Exception HttpError() is called. Elementarious will send a statuscode header and show the messages below. See /__view/html/error/http.xml for markup info.
            404 => array(
                'name'        => 'Inhalt nicht gefunden',
                'description' => 'Der von dir aufgerufene Inhalt ist nicht (mehr) verfügbar.'
            ),
            403 => array(
                'name'        => 'Zugriff verweigert',
                'description' => 'Der von dir aufgerufene Inhalt ist momentan gesperrt und darf nicht aufgerufen werden.'
            ),
        ),
        
        /**
         * MySQL configuration
         * (changes will ONLY work for elementarious' ORM!)
         */
        'mysql_allow' => true, // Whether to enable MySQL or not (if set to true, connection will be established automatically; if set to false, an exception will be thrown if a script calls the database)
        /*****************************************
        * IMPORTANT NOTE:
        * The option above will NOT ensure that there is NEVER a MySQL request.
        * If mysql_allow is set to false, this only disables the framework-intern ORM!
        *****************************************/
        
        'mysql_table_prefix' => '', // The global prefix to use for database requests; can be set manually in the datamodel object (leave empty to disable)
        'mysql_force_utf8' => true, // Whether to force the databse module to use UTF-8 or the default value
        'mysql_framework_valid' => true, // Defines whether the MySQL tabledesign is framework-valid, i.e. each table has it's fields deleted(bool), added(datetime) and lastedit(datetime)
        'mysql_credentials' => array(
            'server'   => 'localhost',
            'port'     => 3306,
            'database' => '',
            'username' => 'root',
            'password' => '',
            'crypt'    => 'plain', // Which crypt method has been used for the password or credentials? Possible values are plain (uncrypted), base64, rot13, zlib
            'file'     => '' // You can define a file where the full credentials are safed; however, if this option is enabled (value not empty), this will disable the credentials above and require that username and password are saved in one line, separated by a semicolon, crypted by the method defined in crypt.
            
        ),
        
        'mysql_globalwhere' => array(), // set an array of where-statements which will always have to match for every select/update request that is made through the entity system. Can be manually disabled in a datamodel by setting Datamodel::$_globalWhere to false.
        
    );