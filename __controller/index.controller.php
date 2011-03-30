<?php

    class Controller_index extends Controller {
    
        protected $_pageTitle   = 'Example Page Title';
        
        protected function work() {
        
            parent::set('example_var','Example Value');
            parent::set('array',array('foo','bar'));
        
        }
    
    }
    
    
?>