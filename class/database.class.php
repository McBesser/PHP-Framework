<?php
   // Notiz
   // Bei insert noch hinzufuegen (return) --- mysqli_insert_id($this->link);
   // 
   interface I_Database
      {
         public function __construct();
         public function basic_sql($sql);
         public function get($sql, $data_array=array());
         public function set($sql, $data_array=array());
         public function insert($sql, $data_array=array());
         public function update($sql, $data_array=array());
         public function delete($sql, $data_array=array());
         //public function setting();
         // private function prepare();
         // private function close();
         
         // $data_array = array(array('type' => 'i',
         //                           'data' => '5'),
         //                     array('type' => 's',
         //                           'data' => 'bla'));
         //private function execute($sql, $data_array);
         
      }
   class Database extends mysqli implements I_Database
      { 
         use T_Log 
             {
                log_add as private;
             }
             
         public static $mysql = array();
         public static $setting_set = false;
             
         public function __construct($safemode=false)
            {
               if($safemode)
                  {
                     // siehe public function basic_sql();
                  }
               else
                  {
                     try
                        {
                           parent::__construct(DB_IP,DB_UN,DB_PW,DB_DB);
                           if ($this->connect_error) 
                              {
                                 throw new Modified_Exception('Konnte DB nicht verbinden ggf. "'.CONFIG_DATABASE.'" prüfen.');
                              }
                           $this->set_charset('utf8');
                        }
                     catch(Modified_Exception $view)
                        { 
                           if(!DEBUG_EXCEPTION and !DEBUG_LOG)
                              {
                                 echo $view->msg();
                                 exit;
                              }
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
                     $this->setting();
                  }
               
               
                
               $debug = (DEBUG_LOG)?self::log_add(__LINE__,__FILE__,'Load','als Objekt geladen',__FILE__):'';
            } 
         public function basic_sql($sql)
            {
               $mysqli = new mysqli(DB_IP,DB_UN,DB_PW);
               if($mysqli->connect_error) 
                  {
                     die('Konnte DB nicht verbinden ggf. "'.CONFIG_DATABASE.'" prüfen. ('.$mysqli->connect_error.')');
                  }
               $query = $mysqli->query($sql);   
               if($query === false) 
                  {
                     echo "Fehler: " . $mysqli->error;
                  }
               $db_array = array();
               if(!is_bool($query))
                  {
                     $db_array = $query->fetch_assoc();
                  }
               
              
               $mysqli->close();
               return $db_array;
            }
         private function execute($sql, $data_array=array())
            {
               try
                  {
                     if(!$query = $this->prepare($sql))
                        {
                           throw new Modified_Exception($this->error);
                        }
                     if(!empty($data_array))
                        {
                            $null = NULL;
                           $types = '';
                           $data_explode_array = array(); 
                           foreach($data_array as $key => $value)
                              {
                                 $types .= $value['type'];
                                 /**/
                                 if($value['type']  == 'b')
                                    {
                                       array_push($data_explode_array, $null);
                                    }
                                 else
                                    { 
                                       array_push($data_explode_array, $value['data']);
                                    }                                 
                              }
                           array_unshift($data_explode_array, $types); 
                           $tmp = array();
                           foreach($data_explode_array as $key => $value) 
                              {
                                 $tmp[$key] = &$data_explode_array[$key];                        
                              } 
                           try
                              {
                                 if(!call_user_func_array(array($query, 'bind_param'), $tmp))
                                    {
                                       throw new Modified_Exception('[MySQLi] Es wurde ein array übergeben jedoch die Platzhalter fehlen.');                       
                                    }
                                 /**/
                                 foreach($data_array as $key => $value)
                                    {
                                       if($value['type']  == 'b')
                                          {
                                             $query->send_long_data($key, $value['data']);
                                          }                                
                                    }
                                 
                                 try
                                    {
                                       if(!$query->execute())
                                          {
                                             throw new Modified_Exception('[MySQLi] Platzhalter wurden gesetzt, es wird ein array vermisst.');                       
                                          };
                                       return $query;   
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
                     else
                        {
                           try
                              {
                                 if(!$query->execute())
                                    {
                                       throw new Modified_Exception('[MySQLi] konnte SQL nicht ausführen. (evtl. fehlt die $data_array)');                       
                                    };
                                 return $query;   
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
         public function get($sql, $data_array=array())
            {  
               /*
               ** Bsp.:
               ** ----------
               ** $db = new Database(); 
               ** $sql = "SELECT * FROM test;";
               ** $data = $db->get($sql);
               ** foreach($data as $key => $value)
               **    {
               **       echo 'id: '.$value['id'].', text: '.$value['text'].'<br />';          
               **    }
               ** $db->close();
               ** -----------------------------
               ** $data_array = array(array('type' => '',
               **                           'data' => ''));
               ** $data = $db->get($sql, $data_array);
               */
               $query = $this->execute($sql, $data_array);
                 
               $data = $query->get_result();
               $db_array = array();
               while($db = $data->fetch_assoc())
                 {
                    array_push($db_array, $db);                     
                 }  
               $query->close();
               // $debug = (DEBUG_LOG)?self::log_add(__LINE__,__FILE__,'Info','blub',__FILE__):'';
               return $db_array;
            }  
         public function set($sql, $data_array=array())
            {  
               /*
               ** Bsp.:
               ** ----------
               ** $db = new Database(); 
               ** $sql = "SELECT * FROM test;";
               ** $data = $db->set($sql);
               ** $db->close();
               ** -----------------------------
               ** $data_array = array(array('type' => '',
               **                           'data' => ''));
               ** $data = $db->set($sql, $data_array);
               */
               $query = $this->execute($sql, $data_array);
                
               $query->close();
               return true; 
            }   
         public function insert($sql, $data_array=array())
            {  
               /*
               ** Bsp.:
               ** ----------
               ** $db = new Database(); 
               ** $sql = "INSERT INTO test () VALUES ();";
               ** $sql = "INSERT INTO test () VALUES (),(),();"; 
               ** $insert_id = $db->insert($sql);
               ** $db->close();
               ** -----------------------------
               ** $data_array = array(array('type' => '',
               **                           'data' => ''));
               ** $data = $db->insert($sql, $data_array);
               */
               $query = $this->execute($sql, $data_array);
                 
               $insert_id = $query->insert_id; 
               $query->close();
               return $insert_id;
            }
         public function update($sql, $data_array=array())
            {  
               /*
               ** Bsp.:
               ** ----------
               ** $db = new Database(); 
               ** $sql = "UPDATE test SET bla = ?, blub = ? WHERE ID = ?";
               ** $bool_true = $db->update($sql);
               ** $db->close();
               ** -----------------------------
               ** $data_array = array(array('type' => '',
               **                           'data' => ''));
               ** $data = $db->update($sql, $data_array);
               */
               $query = $this->execute($sql, $data_array);
                 
               $query->close();
               return true;
            }   
         public function delete($sql, $data_array=array())
            {  
               /*
               ** Bsp.:
               ** ----------
               ** $db = new Database(); 
               ** $sql = "DELETE FROM test WHERE a = ?";
               ** $bool_true = $db->delete($sql);
               ** $db->close();
               ** -----------------------------
               ** $data_array = array(array('type' => '',
               **                           'data' => ''));
               ** $data = $db->delete($sql, $data_array);
               */
               $query = $this->execute($sql, $data_array);
                 
               $query->close();
               return true;
            }
         private function setting()
            {
               if(self::$setting_set === false)
                  {
                     $setting_array = self::$mysql;
                     if(!empty($setting_array))
                        {
                           $sql = "SET GLOBAL max_allowed_packet = ".self::$mysql['max_allowed_packet'];
                           $db = new self(true);
                           $db_array = $db->basic_sql($sql);
                           // read only?
                           /*
                           $sql = "SET GLOBAL innodb_log_file_size = ".self::$mysql['innodb_log_file_size'];
                           $db = new self(true);
                           $db_array = $db->basic_sql($sql);
                           */
                           self::$setting_set = true;
                        }   
                  }
            }
           
      }
?>