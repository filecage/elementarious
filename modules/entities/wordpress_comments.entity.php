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
     
    class Wordpress_Comments extends Entity {
    
        public function __construct() {
            parent::__construct(new Wordpress_Comments_Datamodel());
        }
        
        
        /**
         * getComments
         *
         * gets all approved comments related to $article_id
         * parent comments are given by the parent_comments key
         */
        public function getComments($article_id) {
        
            return $this->proceedComments($this->getAll(array('comment_post_ID'=>$article_id, 'comment_parent'=>0)));
        
        }
        
        /**
         * getComments
         *
         * gets all approved parent comments related to $comment_id
         * parent comments are given by the parent_comments key
         */
        public function getParentComments($comment_id) {
        
            return $this->proceedComments($this->getAll(array('comment_parent'=>$comment_id)));

        }
        
        /**
         * proceedComments
         *
         * proceeds an Entity-return-array and sets formatted date and parent_comments
         */
        private function proceedComments($comments) {
        
            // return an empty array if there are no comments
            if (count($comments) < 1)
                return array();
            
            foreach ($comments as $key => $comment) {
            
                $comments[$key]['comment_content'] = nl2br($comments[$key]['comment_content']);
                $comments[$key]['date_formatted']  = $comments[$key]['comment_date']->format('d. F Y');
                $comments[$key]['parent_comments'] = $this->getParentComments($comments[$key]['comment_ID']); // recursive call
            
            }
            
            return $comments;
            
        }
    
    }
    
?>