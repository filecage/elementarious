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
        private $sourceFilename;
        private $layoutContentLine;
    
        public function __construct() {
        
            $this->domXML = new DOMDocument();
            $this->domXML->strictErrorChecking = false;
            $this->domXML->validateOnParse = true;
            $this->domXML->encoding = 'UTF-8';
        
        }
        
        /**
         * ::get()
         * checks whether a template already exists as cache file and if it's the newest version
         */
        public function get($file) {
        
            if (!Option::val('request_type')) throw new Exception('Templateparser called to parse a document before the queryparser worked.');
            $this->sourceFile = $this->getFile($file);
            
            if (!$this->parseCachedFile($file)) $this->parse($file);
            return (Option::val('request_type')=='html') ? $this->callPHP(Option::val('html_doctype') . $this->buffer) : $this->callPHP($this->buffer);
        
        }
        
        /**
         * ::parse()
         * manages the parse-proccess, i.e. creating an xml object and parsing it
         */
        public function parse($file) {

            $this->domXML->loadXML($this->parseVars($this->sourceFile));
            $this->parseXMLElement($this->domXML->getElementsByTagName('*'),true);
            
            $cache = new Template_Cachebuilder('.' . md5($file), CACHE_PATH . 'tpl/');
            $cache->_write(md5($this->sourceFile) . Option::val('configHash') . "\n" . $this->buffer);

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
                    
                    if (Classloader::getFileName('Markup_Extension_' . strtolower($item->nodeName),true,true) !== false && Option::val('html_markupextension_enabled')) {
                    
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
            $literal  = array();
            
            // if we got any commented areas, delete them before parsing
            $tpl = preg_replace('/{\*(.*)\*}/si', '', $tpl);
            
            // if we got any literal areas, filter them, replace them with a unique string and replace them again later
            preg_match_all('/{literal}(.*){\/literal}/isU', $tpl, $literal_match);

            foreach ($literal_match[1] as $literal_str) {
                $id = '[literal_#'.sha1(microtime(true).$literal_str).md5(uniqid()).'/]';
                $literal[$id] = $literal_str;
                $tpl = str_ireplace('{literal}'.$literal_str.'{/literal}', $id, $tpl);
            }
            
            // find all occurencies of foreach in template and replace variable names
            $foreach = array_reverse(strpos_all($tpl, '{foreach(' , false));
            
            foreach ($foreach as $char) {
                $start    = strpos($tpl, '$', $char)+1;
                $length   = strpos($tpl,' ',$start)-$start;
                $var_name = substr($tpl, $start, $length);
                $tpl      = substr_replace($tpl, str_replace('.','\'][\'', $var_name).'\']', $start, $length);
                $tpl      = substr_replace($tpl, '&lt;?php if (count($vars[\''.$var_name.'\'])>0) { ?&gt;', $char, 0);
            }

            
            // find all occurencies of if and elseif in template and replace variable names
            preg_match_all('/{(else)?if\((.+)\)}/iU', $tpl, $match_if);
            
            foreach ($match_if[0] as $char) {
            
                    $statement = $char;
                    $modified_statement = $statement;
                
                    if (preg_match_all('/\$([a-zA-Z0-9_.]+)/', $statement, $match)) {
                    
                        foreach ($match[0] as $key => $var) {
                        
                            $var_name = str_replace('.','\'][\'', $var);
                            $modified_statement = str_replace_once($var, '$vars[\'' . trim($var_name,'$') . '\']', $modified_statement);
                        
                        }

                    }
                    
                    if (preg_match_all('/\%([a-zA-Z0-9_.]+)\%/', $modified_statement, $match)) {
                    
                        foreach ($match[0] as $key => $var) {
                        
                            $var_name = strstr($var,'.')!==false ? '\']' : '';
                            $var_name = str_replace('.','[\'',  trim($var,'%')) . $var_name;
                            $modified_statement = str_replace($var, '$' . $var_name, $modified_statement);
                        
                        }
                    
                    }
                    
                    $tpl = str_replace($statement, $modified_statement, $tpl);
                    
            }

            // replace foreach and if
            $tpl = str_ireplace('{if(', '&lt;?php if(', $tpl);
            $tpl = str_ireplace('{elseif(', '&lt;?php <?php%%ENDBRACKET_SINGLE%%?> elseif(', $tpl);
            $tpl = str_ireplace('{foreach($', '&lt;?php foreach($vars[\'', $tpl);
            $tpl = str_replace(')}', '){ ?&gt;', $tpl);
            $tpl = str_ireplace('{else}', '&lt;?php <?php%%ENDBRACKET_SINGLE%%?> else { ?&gt;', $tpl);
            $tpl = str_ireplace('{foreachelse}', '&lt;?php <?php%%ENDBRACKET_SINGLE%%?> <?php%%ENDBRACKET_SINGLE%%?> else { ?&gt;', $tpl);
            
            $tpl = str_ireplace('{/foreach}','<?php%%ENDBRACKET%%?><?php%%ENDBRACKET%%?>', $tpl);
            $tpl = str_ireplace('{/foreachelse}','<?php%%ENDBRACKET%%?>', $tpl);
            $tpl = str_ireplace('{/if}','<?php%%ENDBRACKET%%?>', $tpl);
            
            // replace all dots with a new array-level
            preg_match_all('/{\$(.+)}/U', $tpl, $array_match);

            foreach ($array_match as $occurence) {
                if (is_string($occurence) && strstr($occurence, '.')===false) continue; // do not proceed if theres no dot
                $modified_occurence = str_replace('.', '\'][\'', $occurence);
                $tpl = str_replace($occurence, $modified_occurence, $tpl);
            }

            preg_match_all('/{\%([^%]+)\%}/', $tpl, $array_match);

            foreach ($array_match[0] as $occurence) {
                $occurence = $occurence;
                if (is_string($occurence) && strstr($occurence, '.')===false && strstr($occurence, '\'][\'') === false) continue; // do not proceed if theres no dot
                $modified_occurence = (strstr($occurence,'.')===false) ? str_replace('\'][\'', '[\'', str_replace('%}','',$occurence)) : str_replace('.', '[\'', str_replace('%}','',$occurence));
                $tpl = str_replace($occurence, $modified_occurence.'\']%}', $tpl);
            }

            // this is a foreach-variable
            $tpl = str_replace('{%', '&lt;?php echo $', $tpl);
            $tpl = str_replace('%}', '; ?&gt;', $tpl);
            
            
            // replace the normal vars
            $tpl = str_replace('{$', '&lt;?php echo $vars[\'', $tpl);
            $tpl = str_replace('}', '\']; ?&gt;', $tpl);
            $tpl = str_replace('<?php%%ENDBRACKET%%?>', '&lt;?php } ?&gt;', $tpl);
            $tpl = str_replace('<?php%%ENDBRACKET_SINGLE%%?>', '}', $tpl);
            
            // transform the literal strings back after we finished all the other parsing blocks
            foreach ($literal as $id => $str) {
                $tpl = str_replace($id, $str, $tpl);
            }
            
            // die(str_replace('&lt;','<',str_replace('&gt;','>',$tpl))); // debug line
            
            $this->validatePHP($tpl);
            
            
            return $tpl;
            
        }
        
        /**
         * validatePHP
         *
         * transforms the parsed template to valid PHP and tries to execute to find possible syntax errors
         */
        private function validatePHP($tpl) {
        
            $tpl = substr($tpl,strpos($tpl,"\n"));
            $tpl = str_replace('?&gt;', '?>', $tpl);
            $tpl = str_replace('&lt;?php', '<?php', $tpl);
            $tpl = str_replace('&amp;', '&', $tpl);
            
            // set error reporting on (if it's off, we might lose error information)
            error_reporting(E_ALL);
            $php = $this->callPHP(trim($tpl));
           
            
            // turn it to old state after parsing template
            if (!Option::val('debug')) error_reporting('E_NONE');
            
            if (!is_array($php))
                return true;
                
            // if the return is an array, we got an error
            $return = strip_tags($php[1]);
            
            preg_match("/syntax error, (.+) in (.+) on line (\d+)$/s", $return, $code);

            throw new TemplateSyntaxError($code[1], str_replace(basename($this->sourceFilename),'<strong>'.basename($this->sourceFilename).'</strong>',$this->sourceFilename), $code[3]-$this->layoutContentLine-1);
        
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
            $eval = eval('?>' . $tpl);
            
            // get content and clean output buffer
            $tpl = ob_get_contents();
            ob_end_clean();
            
            // if eval returns false, there is probably a syntax error
            if ($eval === false)
                return array(false, $tpl);
            
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
        
            $this->sourceFilename = str_replace('public/../','',VIEW_PATH . Option::val('request_type') . $file . '.xml');
        
            if (!$layout = @file_get_contents(VIEW_PATH . 'layout/' . Option::val('request_type') . '.xml')) throw new Exception('No layout for request_type "' . Option::val('request_type') . '" and request "' . $file . '" found. Create layout or change request_type');
            if (!$file   = @file_get_contents(VIEW_PATH . Option::val('request_type') . $file . '.xml')) throw new HttpError(404, $file);
            
            foreach (explode("\n",$layout) as $num => $line) {
                if (strstr($line, '{$__CONTENT}') !== false) {
                    $this->layoutContentLine = $num-1;
                    break;
                }
            }
            
            return '<?xml version="1.0" encoding="utf-8"?><template>' . "\n" . str_replace('{$__CONTENT}', str_replace('&', '&amp;', $file), str_replace('&', '&amp;', $layout)) . '</template>';
            
        }
        
        /**
         * ::parseCachedFile()
         * gets a cached template file from the cache, checks if it's the latest version and if yes it calls PHP and writes it's output to the buffer
         */
        private function parseCachedFile($file) {

            $content = $this->getCachedFile($file);
            if (!$content) return false;
            
            if (md5($this->sourceFile) . Option::val('configHash') == $content['hash']) {
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