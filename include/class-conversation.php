<?php

class Conversation
{

    public $id;
    public $type = 'conversation';
    public $attributes = array(
        'title' => null,
        'comment' => null,
        'userId' => null,
        'receipientId' => null,
        'lastMessageTime' => null
    );
    
    protected $user;
    protected $receipient;
    
    function __construct( $message = null, $user = null, $receipient = null ) {
        
        // assume correct receipient and user objects
        // as errors of these should be handled already
        
        if( $user ) $this->user = $user;
        if( $receipient ) $this->receipient = $receipient;
        
        // load the message if not provided
        if( !$message ) {
        }
        
        // assign the values from the message
        $this->id = $message->id;
        $this->attributes['title'] = $message->title;
        $this->attributes['comment'] = $message->comment;
        $this->attributes['userId'] = $message->userId;
        $this->attributes['receipientId'] = $message->receipientId;
        $this->attributes['lastMessageTime'] = $message->creationTime;
        
    }
    
}

?>
