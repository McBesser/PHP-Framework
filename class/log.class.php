<?php
   /*
   **
   ** $log_settings_array['path'] = 'log/';
   ** $log_settings_array['file'] = 'action.log';
   ** $log_settings_array['line_length'] = 4;  
   ** $log_settings_array['file_length'] = 20;
   ** $log_settings_array['date_time_format'] = 'd.m.Y H:m:s';
   ** $log_settings_array['sleep_time'] = 1;
   ** $log_settings_array['log_debug'] = false;                
   ** Log::log_settings($log_settings_array);
   ** Log::log_add(__LINE__,__FILE__,'test','test',__FILE__,true);
   **    
   ** Log::log_add(__LINE__,__FILE__,'test','test',__DIR__);
   **
   **
   */            
   interface I_Log
      {
          public static function log_add($text, $prefix='Info', $line='-', $file='-');
          public static function log_new($text='Log geloescht bzw. neu erstellt.', $prefix='Log', $line='-', $file='-');
      }
      
   trait T_Log
      {
         protected static $log_settings_array = array('path' => 'log/',
                                                      'file' => 'action.log',
                                                      'line_length' => 4,
                                                      'file_length' => 20,
                                                      'date_time_format' => 'd.m.Y H:m:s',
                                                      'sleep_time' => 1,
                                                      'log_debug' => false);
         
         private static function log_settings($log_settings_array)            
            {          
               self::$log_settings_array['path']             = (isset($log_settings_array['path']))             ?strval($log_settings_array['path'])             :self::$log_settings_array['path'];
               self::$log_settings_array['file']             = (isset($log_settings_array['file']))             ?strval($log_settings_array['file'])             :self::$log_settings_array['file'];
               self::$log_settings_array['line_length']      = (isset($log_settings_array['line_length']))      ?intval($log_settings_array['line_length'])      :self::$log_settings_array['line_length'];
               self::$log_settings_array['file_length']      = (isset($log_settings_array['file_length']))      ?intval($log_settings_array['file_length'])      :self::$log_settings_array['file_length'];
               self::$log_settings_array['date_time_format'] = (isset($log_settings_array['date_time_format'])) ?strval($log_settings_array['date_time_format']) :self::$log_settings_array['date_time_format'];
               self::$log_settings_array['sleep_time']       = (isset($log_settings_array['sleep_time']))       ?intval($log_settings_array['sleep_time'])       :self::$log_settings_array['sleep_time'];
               self::$log_settings_array['log_debug']        = (isset($log_settings_array['log_debug']))        ?intval($log_settings_array['log_debug'])        :self::$log_settings_array['log_debug'];
               try
                  {
                     if(isset($log_settings_array['sleep_time']) and (!is_int($log_settings_array['sleep_time']) or intval($log_settings_array['sleep_time'] < 0)))
                        { 
                          throw new Modified_Exception('sleep_time ist keine Zahl.');
                        }
                  }  
               catch(Modified_Exception $view)
                  {    
                     if(DEBUG_EXCEPTION)
                        {
                           $data = $view->msg('modified_array');
                           new Control_Exception($data);
                        }
                     if(DEBUG_LOG)
                        {
                           Log::log_add(__LINE__,__FILE__,'debug',$view->msg('log'),__FILE__);
                        }                      
                  } 
            }  
         
         private static function log_file($action, $line, $file, $prefix, $text, $path)
            {                    
               try
                  {  
                     if(!is_writable(self::$log_settings_array['path']))
                        {                               
                           throw new Modified_Exception('Log Ordner ist schreibgeschützt ('.self::$log_settings_array['path'].')');
                        }   
                     $line = str_pad($line, self::$log_settings_array['line_length'], ''.chr(007), STR_PAD_LEFT);
                     $file = explode('\\',$file);
                     $file = array_reverse($file);
                     $file = str_pad($file[0], self::$log_settings_array['file_length'], ''.chr(007));
                     $time = date(self::$log_settings_array['date_time_format']);
                     $FH = fopen(self::$log_settings_array['path'].self::$log_settings_array['file'], $action);
                     fputs($FH, $time." | ".$line." | ".$file." | [".$prefix."] ".$text." (".$path.") \r\n");
                     fclose($FH); 
                     return;                          
                  }  
               catch(Modified_Exception $e)
                  {    
                     if(DEBUG_EXCEPTION)
                        {
                           $data = $view->msg('modified_array');
                           new Control_Exception($data);
                        }
                     if(DEBUG_LOG)
                        {
                           Log::log_add(__LINE__,__FILE__,'debug',$view->msg('log'),__FILE__);
                        }                        
                  } 
            }   
            
         private static function log_add($line='', $file='', $prefix='', $text='', $path='', $sleep=false)
            {                                     
               $line   = (!empty($line))   ?$line   :'-';
               $file   = (!empty($file))   ?$file   :'-';
               $prefix = (!empty($prefix)) ?$prefix :'Info';
               $text   = (!empty($text))   ?$text   :'!!! Kein Log Text !!!';
               $path   = (!empty($path))   ?$path   :'-';
               
               self::log_file('a', $line, $file, $prefix, $text, $path);
               
               if($sleep)
                  {
                     flush(); 
                     sleep(self::$log_settings_array['sleep_time']);
                  };  
               return;
            }
            
         private static function log_new($line='', $file='', $prefix='', $text='', $path='')
            {
               $line   = (!empty($line))   ?$line   :'-';
               $file   = (!empty($file))   ?$file   :'-';
               $prefix = (!empty($prefix)) ?$prefix :'Log';
               $text   = (!empty($text))   ?$text   :'Log geloescht bzw. neu erstellt.';
               $path   = (!empty($path))   ?$path   :'-';
               
               self::log_file('w', $line, $file, $prefix, $text, $path);
               return;
            }
      }
      
   class Log implements I_Log
       {
          use T_Log 
             {
                log_settings as public;
                log_add as public;
                log_new as public;
             }               
       }
?>