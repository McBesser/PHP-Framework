<!doctype html>
<html lang="de">
   <head>
      <title>McBesser - PHP-Framework</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="referrer" content="origin">
      <!-- <meta name="robots" content="follow, index"> -->
      <meta name="robots" content="noindex,nofollow">
      <link rel="stylesheet" href="/css/default.css" media="all"> 
      <script src="/js/default.js" defer></script> 
   </head>
   <body>
      <header>
         <nav id="main-menu">
            <?php $permission = new permission(); ?>
            <div class="menu">
               <ul>
               <?php if(isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
                  <li><a href="/user/logout">Abmeldung</a></li>
               <?php else: ?>
                  <li><a href="/user/login">Anmeldung</a></li>
               <?php endif ?>
               </ul>
            </div>
         </nav>
      </header>
      <main>