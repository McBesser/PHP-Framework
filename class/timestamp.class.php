<?php
   ###############
   #  TODO: 
   #  - vervollstaendigen / ueberarbeiten
   #  - alle kombinationen testen
   #  Log::log_add(__LINE__,__FILE__,'now','U: '.$output,__FILE__); 
   # edit: 2018-03-05
   #       - add: 2  => 'd.m.Y H:i'
   ###############
   interface I_Timestamp
      {
         // sprachcode: ISO 639-2/B
         // https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
         //
         // $time = Datum und Zeit in Sekunden
         // now: Aktuelles Datum und Zeit in Sekunden
         
         // maskieren "verschluesseln"  
         public static function time_encode($time='now', $language='mysql', $type='timestamp', $format='0');
         // Unix Timestamp "entschluesseln"
         public static function time_decode($time_encode, $language='mysql', $type='timestamp', $format='0');
         // Differenz aus 2 Zeitstempel in Sekunden
         public static function timestamp_diff($timestamp, $timestamp2);
      
      }
   // DateTime - http://php.net/manual/de/class.datetime.php
   class Timestamp extends DateTime implements I_Timestamp
      {                                                                 
         public static $format_array = array('mysql' => array('timestamp' => array(0 => 'Y-m-d H:i:s'),
                                                              'date' =>      array(0 => 'Y-m-d'),
                                                              'time' =>      array(0 => 'H:i:s')),
                                             // -------------------------------------------------------------------
                                             'ger' =>   array('timestamp' => array(0         => 'd.m.Y H:i:s',
                                                                                   1         => 'l, d. F Y',
                                                                                   'WdMyhm'  => 'l, d. F Y H:i',
                                                                                   2  => 'd.m.Y H:i'),
                                                              'date' =>      array(0     => 'd.m.Y',
                                                                                   'dmy' => 'd.m.Y',
                                                                                   1     => 'd',
                                                                                   'd'   => 'd',
                                                                                   2     => 'm',
                                                                                   'm'   => 'm',
                                                                                   3     => 'Y',
                                                                                   'y'   => 'Y',
                                                                                   4     => 'l, d. F Y',
                                                                                   'WdMy'=> 'l, d. F Y',
                                                                                   5     => 'l',
                                                                                   'W'   => 'l',
                                                                                   6     => 'F',
                                                                                   'M'   => 'F'),
                                                              'date_short' => array(0     => 'j.n.Y',
                                                                                    'dmy' => 'j.n.Y',
                                                                                    1     => 'j',
                                                                                    'd'   => 'j',
                                                                                    2     => 'n',
                                                                                    'm'   => 'n',
                                                                                    3     => 'y',
                                                                                    'y'   => 'y',
                                                                                    4     => 'D, d. M Y',
                                                                                    'WdMy'=> 'D, d. M Y',
                                                                                    5     => 'D',
                                                                                    'W'   => 'D',
                                                                                    6     => 'M',
                                                                                    'M'   => 'M'),
                                                              'time' =>      array(0      => 'H:i:s',
                                                                                   'hms'  => 'H:i:s',
                                                                                   1      => 'H:i',
                                                                                   'hm'   => 'H:i',
                                                                                   2      => 'H',
                                                                                   'h'    => 'H',
                                                                                   3      => 'i',
                                                                                   'm'    => 'i',
                                                                                   4      => 's',
                                                                                   's'    => 's'),
                                                              'time_short' => array(0      => 'G:i:s',
                                                                                    'hms'  => 'G:i:s',
                                                                                    1      => 'G:i',
                                                                                    'hm'   => 'G:i',
                                                                                    2      => 'G',
                                                                                    'h'    => 'G',
                                                                                    3      => 'i',
                                                                                    'm'    => 'i',
                                                                                    4      => 's',
                                                                                    's'    => 's')));
         private static $weekday_array = array('Monday'     => 'Montag',
                                               'Mon'        => 'Mo',
                                               'Tuesday'    => 'Dienstag',
                                               'Tue'        => 'Di',
                                               'Wednesday'  => 'Mittwoch',
                                               'Wed'        => 'Mi',
                                               'Thursday'   => 'Donnerstag',
                                               'Thu'        => 'Do',
                                               'Friday'     => 'Freitag',
                                               'Fri'        => 'Fr',
                                               'Saturday'   => 'Samstag',
                                               'Sat'        => 'Sa',
                                               'Sunday'     => 'Sonntag',
                                               'Sun'        => 'So');
         private static $month_array = array('January'   => 'Januar',
                                             'Jan'       => 'Jan',
                                             'February'  => 'Februar',
                                             'Feb'       => 'Feb',
                                             'March'     => 'März',
                                             'Mar'       => 'Mär',
                                             'April'     => 'April',
                                             'Apr'       => 'Apr',
                                             'May'       => 'Mai',
                                             'May'       => 'Mai',
                                             'June'      => 'Juni',
                                             'Jun'       => 'Jun',
                                             'July'      => 'Juli',
                                             'Jul'       => 'Jul',
                                             'August'    => 'August',
                                             'Aug'       => 'Aug',
                                             'September' => 'September',
                                             'Sep'       => 'Sep',
                                             'October'   => 'Oktober',
                                             'Oct'       => 'Okt',
                                             'November'  => 'November',
                                             'Nov'       => 'Nov',
                                             'December'  => 'Dezember',
                                             'Dec'       => 'Dez');
         
         // maskieren "verschluesseln"  
         public static function time_encode($time='now', $language='mysql', $type='timestamp', $format='0')
            {
               //
               switch($time)
                  {
                     case 'now':      
                        $time_object = new parent();
                     break;
                     default:
                        $time_object = new parent();
                        $time_object->setTimestamp($time);
                     break;
                  }
               // Format bestimmen
               $format_array = self::$format_array;
               $format = $format_array[$language][$type][$format];
               
               // Wochentag bestimmen
               $weekday_array = self::$weekday_array;
               $weekday = $weekday_array[$time_object->format('l')];
               // Monat bestimmen
               $month_array = self::$month_array;
               $month = $month_array[$time_object->format('F')];
               Log::log_add(__LINE__,__FILE__,'encode','WM: '.$weekday.'; '.$month,__FILE__);
               // Log::log_add(__LINE__,__FILE__,'test','T: '.$month,__FILE__); 
               $output = $time_object->format($format);
               // // Wildcard ersetzen
               // [W] - weekday
               $output = preg_replace("!".$time_object->format('l')."!", $weekday, $output);
               // [M] - month
               $output = preg_replace("!".$time_object->format('F')."!", $month, $output);
               
               // return strftime($format, $time);
               return $output;
            }
         // entschluesseln
         public static function time_decode($time_encode, $language='mysql', $type='timestamp', $format='0')
            {
               $format_array = self::$format_array;
               $weekday_array = self::$weekday_array;
               $month_array = self::$month_array;
               $format = $format_array[$language][$type][$format];
               foreach($weekday_array as $key => $value)
                  {
                     $time_encode = preg_replace("!".$value."!", $key, $time_encode);
                  }
               foreach($month_array as $key => $value)
                  {
                     $time_encode = preg_replace("!".$value."!", $key, $time_encode);
                  }
               $time = self::createFromFormat($format, $time_encode);
               $output = $time->format('U'); 
               
               return $output;
            }
           
         public static function timestamp_diff($timestamp, $timestamp2)
            {
               $time['0'] = new parent($timestamp);
               $time['1'] = new parent($timestamp2); 
               $diff = $time['0']->diff($time['1']);
               $output = $diff->format('U');
               return $output;   
            }
      }
?>