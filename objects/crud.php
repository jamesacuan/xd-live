<?php
if(isset($_POST["action"]))  
{  
   
     if($_POST["action"] == "Load")  
     {  
          $user->read();  
     }  
     if($_POST["action"] == "insert")  
     {  
          echo $_POST['displayname'];
          $user->nickname = $_POST['displayname'];
          $user->username = $_POST['username'];
          $user->role     = $_POST['role'];
          $user->password = $_POST['password'];
          $user->addUser();
     }
}
else{
   echo 'testset';
}

?>