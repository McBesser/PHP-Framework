<?php
   $permission = new permission();
   $permission->check('1');
   
   include('tpl/header.tpl.php');
   include('tpl/test.tpl.php');
   include('tpl/footer.tpl.php');
?>