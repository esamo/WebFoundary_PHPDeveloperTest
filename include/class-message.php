<?php

class Message
{

    public $id;
    public $type = 'message';
    public $attributes = array(
        'title' => null,
        'comment' => null,
        'userId' => null,
        'receipientId' => null,
        'creationTime' => null
    );
    
    protected $user;
    protected $receipient;
    
    function __construct( $comment = null, $title = null, $receipient = null, $user = null ) {
        
        // $comment can be existing bean object, in that case just copy attributes
        
        if( is_object( $comment ) ) {
            
            $this->id = $comment->id;
            $this->attributes['userId'] = $comment->userId;
            $this->attributes['receipientId'] = $comment->receipientId;
            $this->attributes['comment'] = $comment->comment;
            $this->attributes['title'] = $comment->title;
            $this->attributes['creationTime'] = $comment->creationTime;
            
            return;
            
        }
        
        // otherwise create the message
        
        // assume correct receipient and user objects
        // as errors of these should be handled already
        
        // halt on error: empty comment
        if( empty( $comment ) ) {
            throw new Exception('JAC_Message_emptyComment: Comment can not be empty.: comment');
            return;
        }
        
        // halt on error: empty title
        if( empty( $title ) ) {
            throw new Exception('JAC_Message_emptyTitle: Title can not be empty.: title');
            return;
        }
        
        // DB: create red bean object
        $dbPointer = R::dispense( 'message' );
        
        // assign values for the dbPointer and attributes
        // remembering that input should be url-encoded
        $dbPointer->userId = $this->attributes['userId'] = $user->id;
        $dbPointer->receipientId = $this->attributes['receipientId'] = $receipient->id;
        $dbPointer->comment = $this->attributes['comment'] = urldecode( $comment );
        $dbPointer->title = $this->attributes['title'] = urldecode( $title );
        $dbPointer->creationTime = $this->attributes['creationTime'] = date('Y-m-d H:i:s');
        
        // store the message in DB
        $this->id = R::store( $dbPointer );
        
    }
    
}

?>
