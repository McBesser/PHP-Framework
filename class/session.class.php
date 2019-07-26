<?php
   interface I_Session
      {
          
         
      }
   class Session implements I_Session
      {
         private static $session_key = 'empty';
         private static $session_lifetime = '5';
         
         public static function start()
            {
               self::$session_key = SESSION_KEY; 
               self::$session_lifetime = SESSION_LIFETIME;
               session_cache_limiter(self::$session_lifetime);
               session_start();
               
            }
         public static function set($id, $content)
            {
               if(empty($content))
                  {
                     return self::write($id, $content);
                  }
               else
                  {
                     $content = mcrypt_encrypt(MCRYPT_3DES, self::$session_key, $content, MCRYPT_MODE_ECB);
                     return self::write($id, $content);
                  }
            }
         public static function get($id)
            {
               $content = self::read($id);
               if(empty($content))
                  {
                     return $content;
                  }
               else
                  {
                     return trim(mcrypt_decrypt(MCRYPT_3DES, self::$session_key, $content, MCRYPT_MODE_ECB), "\0");
                  }               
            }
         public static function end()
            {
               $_SESSION = array();
               unset($_SESSION);
               if(ini_get("session.use_cookies")) 
                  {
                     $params = session_get_cookie_params();
                     setcookie(session_name(), 
                               '', 
                               time() - 42000, 
                               $params["path"],
                               $params["domain"], 
                               $params["secure"], 
                               $params["httponly"]);
                  }                
               session_destroy(); 
               return true;
            }
         private static function write($id, $content)
            {
               $_SESSION[$id] = $content;
               return $_SESSION[$id];
            }
         private static function read($id)
            {
               if(!isset($_SESSION[$id]))
                  {
                     $_SESSION[$id] = '';
                  }
               return $_SESSION[$id];
               
            }
      }
?>