<?php

    class Controller_mail extends Controller {
    
        protected $_pageTitle = 'Mail example';
        
        protected function work() {
        
            $mail = new Mail('example');
            $mail->setSubject('New email system')
                 ->setSenderAddress('your@sender.address')
                 ->setSenderName('elementarious awesome PHP framework')
                 ->setVar('exampleArray',array('This','class','allows','chaining')) // Like the controller, you can also use Mail::setVars() and give an array
                 ->send('your@receiver.address')
                 ->send('another@receiver.address');
        
        }
    
    }
    
    
?>