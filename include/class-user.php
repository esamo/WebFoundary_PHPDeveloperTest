<?php

class User
{
    
    public $id;
    public $name;
    public $email;
    public $errors = array();
    public $dbPointer;
    
    function __construct( $email = null ) {
        
        $email = urldecode( $email );
        
        // halt on error: wrong email format
        if( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            throw new Exception('JAC_User_WrongEmail: Format of the provided email address is not correct.');
            return;
        }
        
        // email address is valid - assign the value
        $this->email = $email;

        // load the user or create one if it does not exists (by email address)
        $this->dbPointer = R::findOrCreate( 'user', array( 'email' => $email ) );
        
        // assign the values from DB
        $this->name = $this->dbPointer->name;
        $this->id = $this->dbPointer->id;
        
    }
    
}

?>
