<?php

    class Controller_wordpress_mypage extends Controller {
    
        protected $_pageTitle;
        protected $wordpress;
        
        protected function work() {
        
            $this->wordpress = new Wordpress_Data;  // creating wordpress object
            $this->wordpress->getSite('mypage');    /**
                                                     * getting the page with the name "mypage" 
                                                     * note that Wordpress_Data::getSite() will throw
                                                     * an exception of type HttpError 404 if theres no
                                                     * entry in the databse.
                                                     *
                                                     * see /modules/entities/wordpress_data.entity.php
                                                     * for further documentation.
                                                     */
            
            /**
             * $this->getSite() perfomed an Entity::get()-call, which means that the
             * ORM loaded the variables into the object. We can now access them by
             * calling Entity::$column_name.
             */
             
            $this->_pageTitle = $this->wordpress->post_title;  // Set the page title from database
            
            parent::setAll(
                array(
                    'title'   => $this->wordpress->post_title,  // Set the page title for the template
                    'content' => $this->wordpress->post_content // Set content for the template
                )
            );
            
            // another working call would be
            parent::set('page', $this->wordpress->getSite('mypage'));
            
            /**
             * Using the shorter version above is probably more comfortable than the longer version.
             * However, the page title set on line 27 can be called at any time after Entity::get()
             * has been called once. Instead of using $this->wordpress->getSite() on line 37 again,
             * you could also use $this->wordpress->getLoadedData() after $this->wordpress->getSite()
             * has been called.
             */
             
            /**
             * You should take a look into /__view/html/wordpress/mypage.xml to see how these two
             * methods differ in the template.
             */
             
        
        }
    
    }
    
    
?>