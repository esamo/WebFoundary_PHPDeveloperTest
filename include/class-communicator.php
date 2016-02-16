<?php

class Communicator
{

    public $errors = array();
    public $links = array();
    public $data = array();
    
    protected $user;
    protected $receipient;
    protected $headerResult;
    protected $request; // POST and GET data storage
    
    public function __construct( $request ) {
        
        // load and setup the Red Bean ORM
        require_once('include/RedBeanPHP4_3_1/rb.php');
        R::setup( 'mysql:host='. JAC_DB_HOST .';dbname='. JAC_DB_NAME,
        JAC_DB_USER, JAC_DB_PASS );
        R::setAutoResolve( TRUE );
        
        // set the uri of current request
        $this->getSelfUri();

        // assign request array
        $this->request = $request;
        
    }
    
    public function replyJSON() {
        
        // set the default status header
        if( !$this->headerResult ) $this->headerResult = 'HTTP/1.1 200 OK';

        // set the status and content-type headers
        header($this->headerResult);
        header('Content-Type: application/vnd.api+json');

        // print the json encoded version of the object
        echo json_encode( $this );
        
    }

    public function handleAction( $action ) {

        // error: no action specified
        if( !$action ) {
            $this->setError( array('JAC_noAction', 'Action parameter must be provided.', 'action') );
            return;
        }

        switch( $action ) {
            
            // ACTION: Send Message
            case 'sendMessage':
                $this->actionSendMessage();
                break;
            
            // ACTION: List Conversations
            case 'listConversations':
                $this->actionListConversations();
                break;
            
            // ACTION: View Conversation
            case 'viewConversation':
                $this->actionViewConversation();
                break;
               
            // error: unkown action
            default:
                $this->setError( array('JAC_UnknownAction','Have no procedure for requested action: '. $action, 'action') );
                break;
            
        }

    }
    
    
    
    // PROTECTED FUNCTIONS BELOW
    
    protected function actionViewConversation() {
        
        // create the user object
        $this->user = $this->setUser( $this->request['user'] );
        
        // halt on fail
        if( !$this->user ) return;
        
        // create the receipient object
        $this->receipient = $this->setReceipient( $this->request['receipient'] );
        
        // halt on fail
        if( !$this->receipient ) return;
        
        // get the messages between the user and receipient
        $rows = R::find(
            'message',
            '  (user_id = :user_id AND receipient_id = :receipient_id)
            OR (user_id = :receipient_id AND receipient_id = :user_id)',
            array(
                ':user_id' => $this->user->id,
                ':receipient_id' => $this->receipient->id
            )
        );
        
        // convert array into bean objects
        $messages = R::convertToBeans( 'message', $rows );
        
        // create message objects from bean objects
        $conversation = array();
        foreach( $messages as $id => $message ) {
            $conversation[ $id ] = new Message( $message );
        }
        
        // add them to the data array for output
        $this->data = $conversation;
    }
    
    protected function actionListConversations() {
        
        // create the user object
        $this->user = $this->setUser( $this->request['user'] );
        
        // halt on fail
        if( !$this->user ) return;
        
        // get messages sent from the user
        // one latest message for each receipient
        
        $rows = R::getAll('
            SELECT *
            FROM message
            RIGHT JOIN (
                SELECT max(creation_time) as creation_time
                FROM message
                WHERE user_id = :user_id
                GROUP BY receipient_id
            ) AS latest ON message.creation_time = latest.creation_time
            WHERE user_id = :user_id',
            array( ':user_id' => $this->user->id,  )
        );
        
        // convert array into bean objects
        $messages = R::convertToBeans( 'message', $rows );
        
        // create conversations objects based on messages
        $conversations = array();
        foreach( $messages as $id => $message ) {
            $conversations[ $id ] = new Conversation( $message );
        }
        
        // add them to the data array for output
        $this->data = $conversations;
        
    }
    
    protected function actionSendMessage() {

        // create the user object
        $this->user = $this->setUser( $this->request['user'] );
        
        // halt on fail
        if( !$this->user ) return;
        
        // create the receipient object
        $this->receipient = $this->setReceipient( $this->request['receipient'] );
        
        // halt on fail
        if( !$this->receipient ) return;
        
        // create the message
        try {
            $message = new Message( $this->request['comment'], $this->request['title'], $this->receipient, $this->user );
        } catch( Exception $e ) {
            $this->setError( $e->getMessage() );
            // halt on: message creation failed
            return;
        }
        
        // add the message to 'data' array so it will be returned
        $this->data['message'] = $message;
        
    }
    
    protected function setUser( $email ) {
        
        // create and set the user of communicator by email address
        try {
            $result = new User( $email );
        } catch( Exception $e ) {
            $this->setError( $e->getMessage() .': user' );
            $result = false;
        }
        
        return $result;
        
    }
    
    protected function setReceipient( $email ) {
        
        // create and set the receipient by email address
        try {
            $result = new User( $email );
        } catch( Exception $e ) {
            $this->setError( $e->getMessage() .': receipient' );
            $result = false;
        }
        
        return $result;
        
    }
    
    protected function getSelfUri() {
        
        // stringify POST part
        $postPart = $_GET ? null : '?';
        foreach( $_POST as $param => $val ) {
            if( !$val ) continue;
            $postPart .= $param .'='. urlencode( $val ). '&';
        }
        $postPart = substr( $postPart, 0, -1 );
        
        // server name, uri path + script file + _GET, _POST
        $this->links['self'] = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . $postPart;
        
    }
    
    protected function setError( $array = array() ){
        
        // errors come either as an array: title, detail, parameter
        // or string with these elements separated by ': ' with same order
        
        if( !is_array( $array ) ) $array = explode( ': ', $array );
        
        // follow the JSON API error structure
        $this->errors[] = array(
            'title' => $array[ 0],
            'detail' => $array[ 1],
            'source' => array(
                'parameter' => $array[ 2]
            )
        );
        
    }
    
}

?>
