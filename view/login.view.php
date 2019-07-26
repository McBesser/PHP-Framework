<?php
   if(isset($_POST) && isset($_POST['user_name']) && isset($_POST['user_password']))
      {
         
         $name = Filter::clean_input('post', 'user_name', 'chr');
         $pw = Filter::clean_input('post', 'user_password', 'chr');
         if(isset($name) && isset($pw))
            {
               $permission = new permission();
               $login = $permission->login($name, $pw); 
               if($login == false){$this->redirect('/login/fail');}
            }
      }
   if(isset($_SESSION['user']) && !empty($_SESSION['user']))
      {
         $this->redirect('/counter');
      }
   include('tpl/header.tpl.php');
   include('tpl/login.tpl.php');
   include('tpl/footer.tpl.php');
?>