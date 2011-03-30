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
     
    class Template_Cachebuilder extends Filewriter {

        protected $_writeMode = 'w';
        
    }
    
    
    class Templateparser extends Sitebuilder {
    
        private $xml;
        private $domXML;
        private $xmlArray;
        private $buffer = '';
        private $sourceFile;
    
        public function __construct() {
        
            $this->domXML = new DOMDocument();
            $this->domXML->strictErrorChecking = false;
            $this->domXML->validateOnParse = true;
            $this->domXML->encoding = 'UTF-8';
        
        }
        
        /**
         * ::get()
         * checks wether a template already exists as cache file and if it's the newest version
         */
        public function get($file) {
        
            if (!Option::val('request_type')) throw new Exception('Templateparser called to parse a document before the queryparser worked.');
            $this->sourceFile = $this->getFile($file);
            
            if (!$this->parseCachedFile($file)) $this->parse($file);
            return $this->callPHP(Option::val('html_doctype') . $this->buffer);
        
        }
        
        /**
         * ::parse()
         * manages the parse-proccess, i.e. creating an xml object and parsing it
         */
        public function parse($file) {
        
            $this->domXML->loadXML($this->parseVars($this->sourceFile));
            $this->parseXMLElement($this->domXML->getElementsByTagName('*'),true);

            $cache = new Template_Cachebuilder('.' . md5($file), CACHE_PATH . 'tpl/');
            $cache->_write(md5($this->sourceFile) . "\n" . $this->buffer);

        }
        
        /**
         * ::parseXMLElement()
         * extends ::parseXMLObject()
         * required for recursive parsing
         */
        private function parseXMLElement($nodelist,$first=false) {
        
            foreach ($nodelist as $item) {
                
                if ($item->nodeName != '#text' && $item->nodeName != '#cdata-section' && $item->nodeName != '#comment') {
                
                    $buffer = '';
                    
                    if (Classloader::getFileName('Markup_Extension_' . strtolower($item->nodeName),true,true) !== false && Option::val('html_enable_markupextension')) {
                    
                        if ($item->hasAttributes()) {
                        
                            $buffer = 'array(';
                            foreach ($item->attributes as $attr) {
                                $buffer .= '"' . $attr->nodeName . '"=>"' . str_replace('<?php echo', '" . ', str_replace('; ?>', ' . "', $attr->nodeValue)) . '",';
                            }
                            $buffer .=')';
                        
                        }
                    
                        $this->writeToBuffer('<?php if (!isset($markup_extension_'.strtolower($item->nodeName).')) { $markup_extension_'.strtolower($item->nodeName).' = new Markup_Extension_' . strtolower($item->nodeName) . '; } echo $markup_extension_'.strtolower($item->nodeName).'->get('.$buffer.'); ?>');
                        if ($item->hasChildNodes()) $this->parseXMLElement($item->childNodes);
                        $this->writeToBuffer('<?php echo $markup_extension_'.strtolower($item->nodeName).'->endTag(); ?>');
                    
                    }
                    else {
                
                        if ($item->hasAttributes()) {
                            foreach ($item->attributes as $attr) {
                                $buffer .= ' ' . $attr->nodeName . '="' . $attr->nodeValue . '"';
                            }
                        }
                        
                        $buffer .= ($this->noChildNodes($item)) ? '/' : '';
                        $this->writeToBuffer('<' . $item->nodeName . $buffer . '>');
                        
                        if ($item->hasChildNodes()) $this->parseXMLElement($item->childNodes);
                        if (!$this->noChildNodes($item)) $this->writeToBuffer('</'.$item->nodeName.'>');
                    
                    }

                }
                else {
                
                    $textContent = ($item->nodeName=='#text') ? $item->textContent : $item->nodeValue;
                    if ($item->nodeName == '#comment') $textContent = '<!-- ' . $item->textContent . ' !-->';
                
                    if ($item->parentNode->nodeName != 'pre' && $item->parentNode->nodeName != 'textarea') {
                        while (strstr($textContent,'  ') !== false) {
                        
                            $textContent = str_replace('  ', ' ', $textContent);
                            
                        }
                    
                    }
                    
                    if ($item->nodeName=='#cdata-section'||$item->nodeName=='#comment') {
                        $textContent = str_replace('&lt;?php ', '<?php ', $textContent);
                        $textContent = str_replace(' ?&gt;', '?>', $textContent);
                    }
                    
                    $this->writeToBuffer($textContent);
                    
                }

                if ($first) { break; }
                
            }

        }

        /**
         * ::parseVars()
         * gets the templatevars and parses all ifs, loops and other variables and replaces them to get parsed by PHP itself
         */
        private function parseVars($tpl) {

            $continue = false;
            
            // find all occurencies of foreach and if in template
            $if      = strpos_all($tpl, '{if(' , false);
            $if      = array_merge($if, strpos_all($tpl, '{elseif(' , false));
            $foreach = strpos_all($tpl, '{foreach(' , false);
            
            foreach ($foreach as $char) {
                $start    = strpos($tpl, '$', $char)+1;
                $length   = strpos($tpl,' ',$start)-$start;
                $var_name = substr($tpl, $start, $length);
                $tpl      = substr_replace($tpl, $var_name.'\']', $start, $length);
            }
            foreach ($if as $char) {
                $start    = strpos($tpl, '$', $char)+1;
                $length   = lowest(strpos($tpl,')',$start),strpos($tpl,' ',$start),strpos($tpl,'=',$start),strpos($tpl,'!',$start))-$start;
                $var_name = substr($tpl, $start, $length);
                $tpl      = substr_replace($tpl, '$vars[\''.$var_name.'\']', $start-1, $length+1);
            }
            
            // replace foreach and if
            $tpl = str_ireplace('{if(', '&lt;?php if(', $tpl);
            $tpl = str_ireplace('{elseif(', '&lt;?php <?php%%ENDBRACKET_SINGLE%%?> elseif(', $tpl);
            $tpl = str_ireplace('{foreach($', '&lt;?php foreach($vars[\'', $tpl);
            $tpl = str_replace(')}', '){ ?&gt;', $tpl);
            $tpl = str_replace('{else}', '&lt;?php <?php%%ENDBRACKET_SINGLE%%?> else { ?&gt;', $tpl);
            
            $tpl = str_replace('{/foreach}','<?php%%ENDBRACKET%%?>', $tpl);
            $tpl = str_replace('{/if}','<?php%%ENDBRACKET%%?>', $tpl);
            
            
            // this is a foreach-variable
            $tpl = str_replace('{%', '&lt;?php echo $', $tpl);
            $tpl = str_replace('%}', '; ?&gt;', $tpl);
            
            
            // replace the normal vars
            $tpl = str_replace('{$', '&lt;?php echo $vars[\'', $tpl);
            $tpl = str_replace('}', '\']; ?&gt;', $tpl);
            $tpl = str_replace('<?php%%ENDBRACKET%%?>', '&lt;?php } ?&gt;', $tpl);
            $tpl = str_replace('<?php%%ENDBRACKET_SINGLE%%?>', '}', $tpl);

            return $tpl;
            
        }
        
        /**
         * ::callPHP
         * calls PHP and sends the template to parse it
         */
        private function callPHP($tpl) {
        
            $vars = Templatevars::get();
        
            // start output (eval writes directly in the output buffer)
            ob_start();
            
            // eval code
            eval('?>' . $tpl);
            
            // get content and clean output buffer
            $tpl = ob_get_contents();
            ob_end_clean();
            
            return $tpl;
        
        }
        
        /**
         * ::writeToBuffer()
         * writes the parsed HTML from the XML template to the buffer
         */
        private function writeToBuffer($text) {
        
            $text = trim($text);
            if (!empty($text)) $this->buffer .= str_replace('&nbsp;', ' ', str_replace("\n",'',$text));
            
        }
        
        /**
         * ::getFile()
         * reads the file from the correct directory (depending on REQUEST_TYPE)
         * also adds the XML-intro tag to the file
         */
        private function getFile($file) {
        
            if (!$layout = @file_get_contents(VIEW_PATH . 'layout/' . Option::val('request_type') . '.xml')) throw new Exception('No layout for request_type "' . Option::val('request_type') . '" and request "' . $file . '" found. Create layout or change request_type');
            if (!$file   = @file_get_contents(VIEW_PATH . Option::val('request_type') . $file . '.xml')) throw new HttpError(404, $file);
            
            return '<?xml version="1.0" encoding="utf-8"?>' . str_replace('{$__CONTENT}', str_replace('&', '&amp;', $file), str_replace('&', '&amp;', $layout)) . '';
            
        }
        
        /**
         * ::parseCachedFile()
         * gets a cached template file from the cache, checks if it's the latest version and if yes it calls PHP and writes it's output to the buffer
         */
        private function parseCachedFile($file) {

            $content = $this->getCachedFile($file);
            if (!$content) return false;

            if (md5($this->sourceFile) == $content['hash']) {
                $this->buffer = $content['tpl'];
                return true;
            }
            else {
                return false;
            }
            
        
        }
        
        /**
         * ::getCachedFile()
         * reads a cached template file and returns the actual hash and the file
         */
        private function getCachedFile($file) {
        
            $file = CACHE_PATH . 'tpl/.' . md5($file);
            if (!file_exists($file)) return false;
            
            if (!$content = file($file)) throw new HttpError(403);
            return array('hash'=>trim($content[0]), 'tpl'=>implode("\n", array_slice($content, 1)));
        
        }
        
        /**
         * ::noChildNodes()
         * checks if an element may have child nodes or not
         * important, because otherwise empty divs (e.g. <div class="space"></div>) would be written as <div/>
         */
        private function noChildNodes($item) {
        
            return in_array($item->nodeName, Option::val('html_markupextension_standalone'));
        
        }
        
        /**
         * ::getBuffer()
         * returns the current buffer
         */
        public function getBuffer() {
        
            return $this->buffer;
        
        }
    
    }
    
    
 ?>