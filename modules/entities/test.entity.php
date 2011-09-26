<?php

    class Test_Entity extends Entity {
    
        public function __construct() {
            parent::__construct(new Test_Datamodel());
        }
        
    }
    
?>