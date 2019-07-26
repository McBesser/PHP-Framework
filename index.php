<?php
   // conf
      require_once('conf/uri_alias.conf.php');
      require_once('conf/db.conf.php');
   // const
      // Log und Exception
      $debug['log'] = false;
      $debug['exception'] = false;      
      $config['path']['log'] = 'log/';
      $config['file']['log'] = 'action.log';
      define('PATH_LOG',$config['path']['log']);
      define('FILE_LOG',$config['file']['log']);
      define('DEBUG_LOG', $debug['log']);
      define('DEBUG_EXCEPTION', $debug['exception']);
      
      // Session
      $session['key'] = 'McBesserMcBesserMcBesser'; // Encrypt key: 24 sign
      $session['lifetime'] = '5'; // in Min. session lifetime 
      define('SESSION_KEY', $session['key']);
      define('SESSION_LIFETIME', $session['lifetime']);
   
   // class
      require_once('class/log.class.php');
      require_once('class/modified_exception.class.php');
      require_once('class/session.class.php');
      define('CONFIG_DATABASE', 'conf/db.conf.php');
      require_once('class/database.class.php');
      require_once('class/timestamp.class.php');
      #require_once('class/img.class.php');
      require_once('class/filter.class.php');
      require_once('class/uri.class.php');      
      require_once('class/page.class.php');
      require_once('class/permission.class.php');
   
   Session::start();
   #$permission = new permission();
   $page = new page();
   $page->set_alias($uri_alias_array);
   $page->visit(); // visit uri
?>