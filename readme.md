# WebFoundary_PHPDeveloperTest

JSON API Communicator (JAC)  
as PHP Developer Test for Web Foundry  
by Patryk Frey  
coded in 2016-02  

GIT: https://github.com/esamo/WebFoundary_PHPDeveloperTest  
Database: MySQL driven by ORM Red Bean PHP v4.3.1  
No security features  

---

INSTALLATION:

1. Copy the JAC application files onto the accessable server.  
2. Create the MySQL database and enter correct params into config.php file.  
3. Ready to go: either by client index.php or by calling the API externally.  

---

CALLING THE API

... happens by sending to the api.php HTTP GET or POST request with following (url-encoded) parameters:

&user = [email_address]  
&action = listConversations | viewConversation | sendMessage  
  
Action sendMessage requires additionally:  
&title = [title]  
&content = [content]  
&receipient = [email_address]  

Action viewConversation requires additionally:  
&receipient = [email_address]  
