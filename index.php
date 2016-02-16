<?php

/**
  
  index.php
  
  This file works as client of the API.
  
  */

?><!DOCTYPE html>
<html>

    <head>
    
        <title>JSON API Communicator Client</title>
        <meta charset="UTF-8">
        
    </head>
    
    <body>
        
        <h1>JSON API Communicator Client</h2>
        
        <h2>Params</h2>
        <form id="apiCaller" action="#" method="post">
            
            Action: <select name="action">
                <option value="sendMessage">Send Message</option>
                <option value="listConversations">List Conversations</option>
                <option value="viewConversation">View Conversation</option>
            </select><br /><br />
            User [email]: <input type="text" name="user" value="user" /><br /><br />
            Title: <input type="text" name="title" value="title" /><br /><br />
            Comment: <input type="text" name="comment" value="comment" /><br /><br />
            Receipient [email]: <input type="text" name="receipient" value="receipient" /><br />
            <br />
            <button>Call API</button>
        
        </form>
        
        <h2>Answer</h2>
        <textarea id="resultPlain" style="width: 100%" rows="5"></textarea>
        
        
        <h2>readme.md</h2>
        <p><?php echo nl2br( file_get_contents('readme.md') ); ?></p>
        
        <script src="//code.jquery.com/jquery-2.2.0.min.js"></script>
        <script type="text/javascript">
        
            $(function() {
                // on form submit
                $('#apiCaller').submit(function (e) {
                    // cancel default action
                    e.preventDefault();
                    
                    // call api and put the answer in textarea
                    $('#resultPlain').load(
                        'api.php',
                        {
                            action: $('select[name=action]').val(),
                            user: $('input[name=user]').val(),
                            title: $('input[name=title]').val(),
                            comment: $('input[name=comment]').val(),
                            receipient: $('input[name=receipient]').val()
                        }
                    );
                    return false;
                });
            });
        
        </script>
        
    </body>

</html>