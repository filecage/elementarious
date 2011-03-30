<?php

    class Controller_index extends Controller {
    
        protected $_pageTitle   = 'PHP, (X)HTML, CSS, JavaScript';
        
        protected function work() {
        
            parent::set('mutter','Schraube');
            parent::set('arr','Strubbelwubbel');
            $this->set('foo',array('bums','bams','bims'));
        
        }
    
    }
    
    
?>