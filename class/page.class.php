<?php
   class page extends uri
      {         
         private $alias_array = array();
         
         public function set_alias($settings_array)
            {
               $this->alias_array = $settings_array;
            }
         public function visit()
            {
               $alias = self::get_path();
               if(isset($this->alias_array[$alias]))
                  {
                     $file = $this->alias_array[$alias].'.view.php';
                  }
               elseif(file_exists($_SERVER['DOCUMENT_ROOT'].'/view/'.$alias.'.view.php'))
                  {
                     $file = $alias.'.view.php';
                  }
               else
                  {
                     $file = NULL;
                  }
              
               if(!is_null($file) && file_exists($_SERVER['DOCUMENT_ROOT'].'/view/'.$file))
                  {
                     include_once('view/'.$file);
                  }
               else
                  {
                     include_once('view/error_404.view.php');
                  }
            }
         public function redirect($url, $permanent = false)
            {
                header('Location: ' . $url, true, $permanent ? 301 : 302);
                exit();
            }
      }
?>