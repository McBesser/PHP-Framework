<?php
   class permission extends page
      {
         public function check($permission_id, $redirect = true)
            {
               $user = Session::get('user');
               $permission = false;
               if(!empty($user))
                  {
                     if(is_array($permission_id))
                        {$permission_id_array = $permission_id;}
                     else
                        {$permission_id_array = array($permission_id);}
                     
                     foreach($permission_id_array as $key => $value)
                        {
                           $db = new Database(); 
                           $data_array = array(array('type' => 'i',
                                                     'data' => Session::get('user')),
                                               array('type' => 'i',
                                                     'data' => $value));
                           $sql = "SELECT 
                                      account_id, permission_id    
                                   FROM 
                                      account_has_permission
                                   WHERE
                                          account_id = ?
                                      AND permission_id = ?
                                   LIMIT
                                      1";
                           $data = $db->get($sql, $data_array);
                           foreach($data as $key => $value)
                              {
                                 $permission = true;
                              }
                           $db->close();
                           if($permission)
                              {return true;}     
                        }
                     if(!$permission)
                        {
                           if($redirect)
                              {$this->redirect('/login');}
                           return false;
                        } 
                  }
               else
                  {
                     if($redirect)
                        {$this->redirect('/login');}
                     return false;
                  }
               return $permission;
            }
         public function login($name, $pw)
            {
               if(isset($_SESSION['user']) && !empty($_SESSION['user']))
                  {
                     $this->redirect('/logout');
                  }
               else
                  {
                     $account_permission_array = array();
                     #$database = new Database();
                     #$session = new Session();
                     $db = new Database(); 
                     $data_array = array(array('type' => 's',
                                               'data' => $name),
                                         array('type' => 's',
                                               'data' => md5($pw)));
                     $sql = "SELECT 
                                a.id, a.name, a.pw,
                                ap.account_id, ap.permission_id    
                             FROM 
                                account AS a,
                                account_has_permission AS ap
                             WHERE
                                    a.id = ap.account_id             
                                AND a.name = ?
                                AND a.pw = ?
                             LIMIT
                                1";
                     $data = $db->get($sql, $data_array);
                     foreach($data as $key => $value)
                        {
                           #$content = mcrypt_encrypt(MCRYPT_3DES, SESSION_KEY, $value['permission_id'], MCRYPT_MODE_ECB);  
                           #$_SESSION['permission'][$key] = $content;
                           Session::set('user', $value['id']);
                        }
                     $db->close();
                  }
               return (isset($_SESSION['user']) && !empty($_SESSION['user']))?true:false; 
            }
         
      }
?>