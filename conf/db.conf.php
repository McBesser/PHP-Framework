<?php
   // DB settings change
   $db['set'] = 'local';
   #$db['set'] = 'online';
   #$db['set'] = 'home';
   
   // connection settings
   switch($db['set'])
      {
         case 'local':
            $db['ip'] = '127.0.0.1';
            $db['db'] = '';
            $db['pf'] = '';
            $db['un'] = '';
            $db['pw'] = '';            
         break;	
         case 'online':
            $db['ip'] = '127.0.0.1';
            $db['db'] = '';
            $db['pf'] = '';
            $db['un'] = '';
            $db['pw'] = '';
         break;	
         case 'home':
            $db['ip'] = '127.0.0.1';
            $db['db'] = '';
            $db['pf'] = '';
            $db['un'] = '';
            $db['pw'] = '';
         break;	         
         default:
            $db['ip'] = ''; // ip / url
            $db['db'] = ''; // db name
            $db['pf'] = ''; // prefix 
            $db['un'] = ''; // user name
            $db['pw'] = ''; // password
         break;	
      };
      
   ###################################################################
   // not change  
   define('DB_IP',$db['ip']);
   define('DB_DB',$db['pf'].$db['db']);
   define('DB_UN',$db['un']);
   define('DB_PW',$db['pw']);

?>