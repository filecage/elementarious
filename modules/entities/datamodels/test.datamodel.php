<?php

    class Test_Datamodel extends Datamodel {
    
        protected $_tableName = 'blog_posts';
        protected $_primaryKey = 'ID';
    
        protected function configure() {
        
            $this->setFields(array(
                'ID' => array('type'=>'int','readonly'=>true),
                'post_title' => array('type'=>'text'),
                'post_content' => array('type'=>'text','html_allowed'=>true)
            ));
            
        }
        
    }
    
?>