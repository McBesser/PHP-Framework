<?php
   class uri extends Filter 
      {
         public static function get_path()
            {
               $clean_uri = self::clean_input('server', 'REQUEST_URI', 'url');
               #$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               #print'test: '.$clean_uri.'<br>';
               $uri_path = parse_url($clean_uri, PHP_URL_PATH);
               return $uri_path;
            }
         public static function get_segment($number='0')
            {
               $clean_uri = self::clean_input('server', 'REQUEST_URI', 'url');
               #$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
               #print'test: '.$clean_uri.'<br>';
               $uri_path = parse_url($clean_uri, PHP_URL_PATH);
               $uri_segments = explode('/', $uri_path);
               return $uri_segments[$number];
            }
      }
?>