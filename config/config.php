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
        'html_doctype' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',  // The doctype to send on top of the document 
        'html_markupextension_enabled' => true, // Allow markupextensions
        'html_markupextension_standalone' => array('link','meta','img','input','br'), // All markup elements which can end by a simple /> (standalone). Markupextensions don't need to be added as they will be ended by Extension::endTag
        
        /**
         * Compression
         */
        'compression_enable_gzip' => true, // Wether to enable gzip compression or not
        
        
        /**
         * Headers
         */
        'header_send_utf8' => true, // Wether to send proper UTF-8 charset (turn on when you're having trouble with correct UTF-8 source files but still enconding problems)
        'header_enable_shortget' => false, // Wether to enable parsing GET parameters with no value but setted to be TRUE or unset. (e.g. http://www.example.org?myexampletag is turned into $_GET['myexampletag'] = true if turned on, NULL if turned off)
        'header_enable_urlget' => true, // Wether to enable parsing GET parameters in the URL, e.g. www.example.org/existingfile/foo/bar will be parsed as file:existingfile, $_GET['foo'] = bar. Has to be enabled for every controller expecting such data by Controller:prepareRequestStatementInUri().
        
        
        /**
         * Error configuration
         */
        'debug' => true, // Wether to enable debug mode or not. Do NOT turn off when in development environment! This would cause elementarious NOT to search for new created controllers or markup classes. Must be turned off in productive environment
        'error_http_messages' => array( // The http error messages shown when the Exception HttpError() is called. Elementarious will send a statuscode header and show the messages below. See /__view/html/error/http.xml for markup info.
            404 => array(
                'name'        => 'Inhalt nicht gefunden',
                'description' => 'Der von dir aufgerufene Inhalt ist nicht (mehr) verfügbar.'
            ),
            403 => array(
                'name'        => 'Zugriff verweigert',
                'description' => 'Der von dir aufgerufene Inhalt ist momentan gesperrt und darf nicht aufgerufen werden.'
            ),
        )
        
    );