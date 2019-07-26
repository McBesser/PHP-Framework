<?php  
   #####################################
   # TODO: class (trait) UEberarbeiten #
   #####################################
   // - static uebergabe??? evtl.
   interface I_Modified_Exception
      {
         public function msg();
      }
   trait T_Modified_Exception
      {
         
      }
   class Modified_Exception extends Exception
      {   
            
         public function __construct($msg=NULL)
            {
               if(!is_null($msg))
                  { 
                     parent::__construct($msg);  
                  } 
            }
         public function msg($mode='')
            {  
               if(!empty($this->getTrace()))
                  {
                      $exception_array = $this->getTrace();  
                      $exception_array =  $exception_array[0];
                  }
               else
                  {
                      $exception_array['line'] = '-';
                      $exception_array['file'] = '-';  
                  }  
               $exception_array = $this->getTrace();  
               $exception_array =  $exception_array[0];  
               switch($mode)
                  {
                     case 'modified_array':
                        $data['message'] = $this->getMessage(); 
                        $data['trigger_line'] = $exception_array['line']; 
                        $data['trigger_file'] = $exception_array['file']; 
                        $data['throw_line'] = $this->getLine(); 
                        $data['throw_file'] = $this->getFile();
                        return $data;
                     break;
                     case 'modified_msg_var':
                        $output = '';                        
                        $output .= '<section class="exception">';
                           $output .= '<h1>'.$this->getMessage().'</h1>'; 
                           $output .= '<article>'; 
                              $output .= '<p>[Auslöser] Zeile: '.$exception_array['line'].' ('.$exception_array['file'].')</p>';
                              $output .= '<p>[throw] Zeile: '.$this->getLine().' ('.$this->getFile().')</p>';
                           $output .= '</article>'; 
                        $output .= '</section>';
                        $output .= '<div></div>';                                    
                        return $output;                
                     break;
                     case 'modified_msg_echo':         
                        echo '<section class="exception">';
                           echo '<h1>'.$this->getMessage().'</h1>'; 
                           echo '<article>'; 
                              echo '<p>[Auslöser] Zeile: '.$exception_array['line'].' ('.$exception_array['file'].')</p>';
                              echo '<p>[throw] Zeile: '.$this->getLine().' ('.$this->getFile().')</p>';
                           echo '</article>'; 
                        echo '</section>';
                        echo '<div></div>';                                    
                        return; 
                     break;
                     case 'print_r':
                        echo '<br />'; 
                        echo '<pre>';
                        print_r($exception_array[0]);
                        echo '</pre>';
                        return;
                     break;                        
                     case 'log':
                        return $this->getMessage();
                     break;
                     default:
                        return $this->getMessage();
                     break;
                  } 
            }
      }  
?>