<?php

/**
  
  api.php
  
  This file loads libs and processes the client request by handling Communicator object.
  
  */

// load all the stuff
require_once('config.php');
require_once('include/class-communicator.php');
require_once('include/class-user.php');
require_once('include/class-message.php');
require_once('include/class-conversation.php');

// merge POST and GET into one array
$request = array_merge( $_POST, $_GET );

// create Communicator base object
$JAC = new Communicator( $request );

// handle the requested action
$JAC->handleAction( $request['action'] );

// send the JSON answer based on action request
$JAC->replyJSON();

?>
