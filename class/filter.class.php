<?php
         
   interface I_Filter
      {     
         // überprüft ob ein "string" die gültige Konsistenz hat
         // bsp.: Filter::validate('test', 'float'); // false
         public static function validate($content, $validate_type='integer');
         // saubert z. B. GET und POST uebergaben
         // bsp.: Filter::clean_input('post', 'user_name', 'chr'); // user_name: Strumpf<b>loch</b> = Strump&lt;floch&gt;
         public static function clean_input($input_type, $input_key, $clean_type='html');
      
         public static function clean_var($content, $clean_type='html');
      }
   
   class Filter implements I_Filter
      {   
         public static $input_array = array('post'    => 'INPUT_POST', 
                                            'get'     => 'INPUT_GET',
                                            'cookie'  => 'INPUT_COOKIE',
                                            'env'     => 'INPUT_ENV',
                                            'server'  => 'INPUT_SERVER',
                                            'session' => 'INPUT_SESSION',
                                            'request' => 'INPUT_REQUEST');
         
         public static $validate_array = array('integer' => 'FILTER_VALIDATE_INT',
                                               'int'     => 'FILTER_VALIDATE_INT',
                                               'float'   => 'FILTER_VALIDATE_FLOAT',
                                               'boolean' => 'FILTER_VALIDATE_BOOLEAN',
                                               'bool'    => 'FILTER_VALIDATE_BOOLEAN',
                                               'email'   => 'FILTER_VALIDATE_EMAIL',
                                               'mail'    => 'FILTER_VALIDATE_EMAIL',
                                               'url'     => 'FILTER_VALIDATE_URL',
                                               'ip'      => 'FILTER_VALIDATE_IP',
                                               'mac'     => 'FILTER_VALIDATE_MAC',
                                               'regexp'  => 'FILTER_VALIDATE_REGEXP',
                                               'preg'    => 'FILTER_VALIDATE_REGEXP');
         // säubern
         public static $sanitize_array = array('number_int'    => 'FILTER_SANITIZE_NUMBER_INT',
                                               'integer'       => 'FILTER_SANITIZE_NUMBER_INT',
                                               'int'           => 'FILTER_SANITIZE_NUMBER_INT',
                                               'number_float'  => 'FILTER_SANITIZE_NUMBER_FLOAT',
                                               'float'         => 'FILTER_SANITIZE_NUMBER_FLOAT',
                                               'string'        => 'FILTER_SANITIZE_STRING',
                                               'str'           => 'FILTER_SANITIZE_STRING',
                                               'special_chars' => 'FILTER_SANITIZE_SPECIAL_CHARS',
                                               'varchar'       => 'FILTER_SANITIZE_SPECIAL_CHARS',
                                               'chars'         => 'FILTER_SANITIZE_SPECIAL_CHARS',
                                               'char'          => 'FILTER_SANITIZE_SPECIAL_CHARS',
                                               'chr'           => 'FILTER_SANITIZE_SPECIAL_CHARS',
                                               'html'          => 'FILTER_SANITIZE_SPECIAL_CHARS',
                                               'email'         => 'FILTER_SANITIZE_EMAIL',
                                               'mail'          => 'FILTER_SANITIZE_EMAIL',
                                               'url'           => 'FILTER_SANITIZE_URL',
                                               'stripped'      => 'FILTER_SANITIZE_STRIPPED',
                                               'magic_quotes'  => 'FILTER_SANITIZE_MAGIC_QUOTES',
                                               'encoded'       => 'FILTER_SANITIZE_ENCODED',
                                               'encoded_url'   => 'FILTER_SANITIZE_ENCODED',
                                               'encode_url'    => 'FILTER_SANITIZE_ENCODED');

         public static $flag_array = array('none'              => 'FILTER_FLAG_NONE',
                                           'allow_octal'       => 'FILTER_FLAG_ALLOW_OCTAL',
                                           'allow_hex'         => 'FILTER_FLAG_ALLOW_HEX',
                                           'strip_low'         => 'FILTER_FLAG_STRIP_LOW',
                                           'strip_high'        => 'FILTER_FLAG_STRIP_HIGH',
                                           'encode_low'        => 'FILTER_FLAG_ENCODE_LOW',
                                           'encode_high'       => 'FILTER_FLAG_ENCODE_HIGH',
                                           'encode_amp'        => 'FILTER_FLAG_ENCODE_AMP',
                                           'no_encode_quotes'  => 'FILTER_FLAG_NO_ENCODE_QUOTES',
                                           'empty_string_null' => 'FILTER_FLAG_EMPTY_STRING_NULL',
                                           'allow_fraction'    => 'FILTER_FLAG_ALLOW_FRACTION',
                                           'allow_thousand'    => 'FILTER_FLAG_ALLOW_THOUSAND',
                                           'allow_scientific'  => 'FILTER_FLAG_ALLOW_SCIENTIFIC',
                                           'path_required'     => 'FILTER_FLAG_PATH_REQUIRED',
                                           'query_reqired'     => 'FILTER_FLAG_QUERY_REQUIRED',
                                           'ipv4'              => 'FILTER_FLAG_IPV4',
                                           'ipv6'              => 'FILTER_FLAG_IPV6',
                                           'res_range'         => 'FILTER_FLAG_NO_RES_RANGE',
                                           'no_priv_range'     => 'FILTER_FLAG_NO_PRIV_RANGE');
         
         public static $other_array = array('require_scalar' => 'FILTER_REQUIRE_SCALAR',
                                            'require_array' => 'FILTER_REQUIRE_ARRAY',
                                            'force_array' => 'FILTER_FORCE_ARRAY',
                                            'null_on_failure' => 'FILTER_NULL_ON_FAILURE',
                                            'default' => 'FILTER_DEFAULT',
                                            'unsafe_raw' => 'FILTER_UNSAFE_RAW',
                                            'callback' => 'FILTER_CALLBACK');
         
         public static function validate($content, $validate_type='integer')
            {
               try
                  {
                     if(array_key_exists($validate_type, self::$validate_array))
                        {
                           $output = filter_var($content, constant(self::$validate_array[$validate_type]));
                           return $output;
                        }
                     else
                        {
                           $exception_type = '';
                           foreach(self::$validate_array as $key => $value)
                              {
                                 $exception_type .= $key.', ';
                              }
                           $exception_type = rtrim($exception_type, ', ');   
                           throw new Modified_Exception('Validate Filter Type ('.$validate_type.') existiert nicht. Gültig ist: '.$exception_type);
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
         public static function clean_input($input_type, $input_key, $clean_type='html')
            {
               try
                  {
                     if(array_key_exists($input_type, self::$input_array))
                        {
                           if(array_key_exists($clean_type, self::$sanitize_array))
                              {
                                 $output = filter_input(constant(self::$input_array[$input_type]), $input_key, constant(self::$sanitize_array[$clean_type]));
                                 return $output;
                              }
                           else
                              {
                                 $exception_type = '';
                                 foreach(self::$sanitize_array as $key => $value)
                                    {
                                       $exception_type .= $key.', ';
                                    }
                                 $exception_type = rtrim($exception_type, ', ');   
                                 throw new Modified_Exception('Säuberungs Filter Type ('.$clean_type.') existiert nicht. Gültig ist: '.$exception_type);
                              }
                        }
                     else
                        {
                           $exception_type = '';
                           foreach(self::$input_array as $key => $value)
                              {
                                 $exception_type .= $key.', ';
                              }
                           $exception_type = rtrim($exception_type, ', ');   
                           throw new Modified_Exception('Input Filter Type ('.$input_type.') existiert nicht. Gültig ist: '.$exception_type);
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
         public static function clean_var($content, $clean_type='html')
            {
               try
                  {
                     if(array_key_exists($clean_type, self::$sanitize_array))
                        {
                           $output = filter_var($content, constant(self::$sanitize_array[$clean_type]));
                           return $output;
                        }
                     else
                        {
                           $exception_type = '';
                           foreach(self::$sanitize_array as $key => $value)
                              {
                                 $exception_type .= $key.', ';
                              }
                           $exception_type = rtrim($exception_type, ', ');   
                           throw new Modified_Exception('Säuberungs Filter Type ('.$clean_type.') existiert nicht. Gültig ist: '.$exception_type);
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
         public static function print_r()
            {
               echo '<pre>input_array = ';
                  print_r($self::$input_array);
               echo '</pre>';
               echo '<pre>validate_array = ';
                  print_r($self::$validate_array);
               echo '</pre>';
               echo '<pre>sanitize_array = ';
                  print_r($self::$sanitize_array);
               echo '</pre>';
               echo '<pre>flag_array = ';
                  print_r($self::$flag_array);
               echo '</pre>';
               echo '<pre>other_array = ';
                  print_r($self::$other_array);
               echo '</pre>';
            }
      }
?>