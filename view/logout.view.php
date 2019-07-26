<?php
   if(isset($_POST) && isset($_POST['user_logout']))
      {
         Session::end();
         $this->redirect('/login');
      }
   include('tpl/header.tpl.php');
   include('tpl/logout.tpl.php');
   include('tpl/footer.tpl.php');
?>