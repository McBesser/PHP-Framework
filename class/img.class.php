<?php 

   interface I_Image
      {
         // Leinwand
         /*
         ** $width = Leinwand Breite (ganze Zahl z. B.: 15)
         ** $height = Leinwand Hoehe (ganze Zahl z. B.: 10)
         ** $color = Leinwand Farbe (Hex z. B.: 'FFFFFF')
         ** $mime_type = Typ des Bildes ('png', 'gif', 'bmp', 'jpg')
         ** $truecolor = Aufloesung (true, false)
         ** $transparent = Farbe komplett 100 % Transparent machen (false, true)
         ** $alpha = Transparenz 0-100 % (false, 0-127)
         ** $debug = Logt alles mit (false, true)
         */
         public static function canvas($width, $height, $color='FFFFFF', $mime_type='png', $truecolor=false, $transparent=false, $alpha=false);
         // Ausgabe
         /*
         ** $image = diese class: img.class.php - canvas Ausgabe (return)
         **          inklusive alle zwischenzeitlichen veraenderungen
         **          oder anderes kreiertes Bild + veraenderungen
         ** $file_name = Name der Datei bzw. headers (z. B. 'cooles_bild')
         ** $mime_type = Typ des Bildes ('png', 'gif', 'bmp', 'jpg')
         ** $save = Speicher art ('img', 'file', 'bin', bin64)
         **         img = fuer html img (extra image.php; 
         **               header fuer das Bild wird uebergeben) 
         **               (z. B. <img src="image.php" />)
         **         file = Speichert das Bild als Datei in/als: $file_path.$file_name.$mime_type
         **                (z. B. $file_path = 'test/img/icon/'
         **                       $file_name = 'lachen'
         **                       $mime_type = 'png'
         **                       Pfad/Datei waere: 'test/img/icon/lachen.png')
         **         bin = Binaer Daten unmaskiert
         **         bin64 = Binaer Daten in base64 encodiert (base64_encode())
         ** $file_path = Pfad mit slash am ende (z. B.: 'test/img/icon/')   
         **
         ** --------------------------------------------------------------------------------
         ** mime typen: bmp, gif, jpg, png
         **             Standard: png
         **
         ** Speicher Art: file, bin, bin64, img
         **               Standard: img
         */
         public static function output($image, $file_name, $mime_type='png', $save='img', $file_path='');
         // z. B. Upload $_FILES['xyz']['tmp_name']
         public static function file_to_bin($file);
         
         public static function file_to_image($file);
         
         public static function image_to_bin($image, $mime_type='png');
         // GDlib image
         public static function bin_to_image($bin);
         // gibt das Bild aus also auch die Header informationen
         public static function bin_to_img($bin_data, $src_base64=true, $file_name='image');
         // veraendert die groesse und behaelt die propertionen bei        
         public static function image_resize($image, $width, $height, $color='FFFFFF', $mime_type='png', $truecolor=false, $transparent=false, $alpha=false);
         
      }
      
   class Image implements I_Image
      {   
         public static $image_type_array = array(1 => 'IMAGETYPE_GIF',
                                                 2 => 'IMAGETYPE_JPEG',
                                                 3 => 'IMAGETYPE_PNG',
                                                 4 => 'IMAGETYPE_SWF',
                                                 5 => 'IMAGETYPE_PSD',
                                                 6 => 'IMAGETYPE_BMP',
                                                 7 => 'IMAGETYPE_TIFF_II',
                                                 8 => 'IMAGETYPE_TIFF_MM',
                                                 9 => 'IMAGETYPE_JPC',
                                                 10 => 'IMAGETYPE_JP2',
                                                 11 => 'IMAGETYPE_JPX',
                                                 12 => 'IMAGETYPE_JB2',
                                                 13 => 'IMAGETYPE_SWC',
                                                 14 => 'IMAGETYPE_IFF',
                                                 15 => 'IMAGETYPE_WBMP',
                                                 16 => 'IMAGETYPE_XBM',
                                                 17 => 'IMAGETYPE_ICO');
         
         public static function canvas($width, $height, $color='FFFFFF', $mime_type='png', $truecolor=false, $transparent=false, $alpha=false)
            {   
               $color = (is_null($color))?'FFFFFF':$color;
               if($truecolor)
                  {
                     $image = imagecreatetruecolor($width, $height);
                  }
               else
                  {
                     $image = imagecreate($width, $height);
                  }
               $color = str_split($color, 2);
               $bg_red = hexdec($color['0']);
               $bg_green = hexdec($color['1']);
               $bg_blue = hexdec($color['2']);                 
               
               if(isset($alpha) === true and $mime_type == 'png' and $transparent === false)
                  {
                     imagealphablending($image, true);
                     imagesavealpha($image, true);
                     $bg_color = imagecolorallocatealpha($image, $bg_red, $bg_green, $bg_blue, $alpha);
                     if($truecolor === true)
                        {
                           imagefill($image, 0, 0, $bg_color);
                        }  
                  }
               elseif($alpha === false and $mime_type == 'png' and $transparent === false)
                  {
                     imagealphablending($image, false);
                     imagesavealpha($image, true);
                     $bg_color = imagecolorallocate($image, $bg_red, $bg_green, $bg_blue);
                  }
               elseif($transparent === true and ($mime_type == 'png' or $mime_type == 'gif'))
                  {                     
                     $bg_color = imagecolorallocate($image, $bg_red, $bg_green, $bg_blue);
                     if($truecolor === true)
                        {
                           imagefill($image, 0, 0, $bg_color);
                        }                     
                     imagecolortransparent($image, $bg_color);
                  }
               else
                  {
                     $bg_color = imagecolorallocate($image, $bg_red, $bg_green, $bg_blue);
                  }
               return $image; 
            }   
  
         public static function output($image, $file_name, $mime_type='png', $save='img', $file_path='')
            {   
               $data = false;
               switch($save)
                  {
                     case 'file':
                        switch($mime_type)
                           {
                              case 'bmp':
                                 imagewbmp($image, $file_path.$file_name.'.bmp');
                              break;
                              case 'gif':
                                 imagegif($image, $file_path.$file_name.'.gif');
                              break;
                              case 'jpg':
                                 imagejpeg($image, $file_path.$file_name.'.jpg', 100); 
                              break;
                              case 'png':
                                 imagepng($image, $file_path.$file_name.'.png');
                              break;
                           }
                     break;
                     case 'bin':
                        switch($mime_type)
                           {
                              case 'bmp':
                                 ob_start();
                                 imagewbmp($image);
                                 $data = ob_get_contents();
                                 ob_end_clean();
                              break;
                              case 'gif':
                                 ob_start();
                                 imagegif($image);
                                 $data = ob_get_contents();
                                 ob_end_clean();
                              break;
                              case 'jpg':
                                 ob_start();
                                 imagejpeg($image, NULL, 100);
                                 $data = ob_get_contents();
                                 ob_end_clean(); 
                              break;
                              case 'png':
                                 ob_start();
                                 imagepng($image);
                                 $data = ob_get_contents();
                                 ob_end_clean();
                              break;
                           }
                        // $data = pack('H*',$image);
                     break;
                     case 'bin64':
                        switch($mime_type)
                           {
                              case 'bmp':
                                 ob_start();
                                 imagewbmp($image);
                                 $data = base64_encode(ob_get_contents());
                                 ob_end_clean();
                              break;
                              case 'gif':
                                 ob_start();
                                 imagegif($image);
                                 $data = base64_encode(ob_get_contents());
                                 ob_end_clean();
                              break;
                              case 'jpg':
                                 ob_start();
                                 imagejpeg($image, NULL, 100);
                                 $data = base64_encode(ob_get_contents());
                                 ob_end_clean(); 
                              break;
                              case 'png':
                                 ob_start();
                                 imagepng($image);
                                 $data = base64_encode(ob_get_contents());
                                 ob_end_clean();
                              break;
                           }                        
                     break;
                     case 'img':
                        switch($mime_type)
                           {
                              case 'bmp':
                                 header('Content-Type: image/vnd.wap.wbmp');
                                 header('Content-disposition: inline; filename="'.$file_name.'.bmp"');
                                 imagewbmp($image);
                              break;
                              case 'gif':
                                 header('Content-Type: image/gif');
                                 header('Content-disposition: inline; filename="'.$file_name.'.gif"');
                                 imagegif($image);
                              break;
                              case 'jpg':
                                 header('Content-Type: image/jpeg');
                                 header('Content-disposition: inline; filename="'.$file_name.'.jpg"');
                                 imagejpeg($image, NULL, 100); 
                              break;
                              case 'png':
                                 header('Content-Type: image/png');
                                 header('Content-disposition: inline; filename="'.$file_name.'.png"');
                                 imagepng($image);
                              break;
                           }
                     break;               
                  }
               imagedestroy($image);
               return $data;                
            }
         
         public static function file_to_bin($file)
            {
               $bin_data = file_get_contents($file); 
               return $bin_data;  
            }
         public static function file_to_image($file)
            {
               $image = imagecreatefromstring(file_get_contents($file));
               return $image;
            }
         public static function image_to_bin($image, $mime_type='png')
            {
               return self::output($image, NULL, $mime_type, 'bin');
            }
         public static function bin_to_image($bin)
            {
               return imagecreatefromstring($bin);
            }
         public static function bin_to_img($bin_data, $src_base64=true, $file_name='image')
            {
               /*
               **
               ** $image_info_array:
               ** 0 = width
               ** 1 = height
               ** 2 = type bzw. extension type
               ** 3 = img attribute width="123" height="123"
               ** bits = Zahl (z. B. 8 Bits)
               ** mime = z. B. image/png 
               **   
               */
               $image_info_array = getimagesizefromstring($bin_data);
               if($src_base64)
                  {
                     return 'data:'.$image_info_array['mime'].';base64,'.base64_encode($bin_data);
                  }
               else
                  {
                     if($image_info_array[2] >= 1 and $image_info_array[2] <= 17)
                        {
                           $image_type_array = self::$image_type_array;
                           $file_extension = image_type_to_extension(constant($image_type_array[$image_info_array[2]]));

                           header('Content-Type: '.$image_info_array['mime']);
                           header('Content-disposition: inline; filename="'.$file_name.$file_extension.'"');
                           echo $bin_data;   
                        }   
                  }
            }
            
         public static function image_resize($image, $width, $height, $color='FFFFFF', $mime_type='png', $truecolor=false, $transparent=false, $alpha=false)
            {
               $color = (is_null($color))?'FFFFFF':$color;
               $image_width = imagesx($image);
               $image_height = imagesy($image);
               if($width < $image_width or $height < $image_height)
                  {
                                    // Breite, Hoehe
                     if ($width && ($image_width <= $image_height))
                        {
                           $width = ($height / $image_height) * $image_width;
                        }
                     else
                        {
                           $height = ($width / $image_width) * $image_height;
                        }
                  }
               else
                  {
                     $width = $image_width;
                     $height = $image_height;
                  };
                  
               $image_resized = self::canvas($width, $height, $color, $mime_type, $truecolor, $transparent, $alpha);
               ImageCopyResized($image_resized, $image, 0, 0, 0, 0, $width, $height, $image_width, $image_height);
               imagedestroy($image);
               return $image_resized;  
            }
         public static function image_resize_center($image, $width, $height, $color='FFFFFF', $mime_type='png', $truecolor=false, $transparent=false, $alpha=false)
            {
               $image_width = imagesx($image);
               $image_height = imagesy($image);
               if($image_width > $width or $image_height > $height)
                  {
                     $height_new = ($width / $image_width) * $image_height;
                     $width_new = ($height / $image_height) * $image_width; 
                     $width_space = ($width_new - $width) / 2;
                     $height_space = ($height_new - $height) / 2;
                     if($height_new < $height)
                        {
                           $height_new = $height;
                           $height_space = 0;
                        }
                     else
                        {
                           $width_new = $width;
                           $width_space = 0;
                        }
                  }
               else
                  {
                     $width_new = $width;
                     $width_space = 0;
                     $height_new = $height;
                     $height_space = 0;
                  }
               
               $image_resized = self::canvas($width_new, $height_new, $color, $mime_type, $truecolor, $transparent, $alpha);
               ImageCopyResized($image_resized, $image,0, 0, 0, 0, $width_new, $height_new, $image_width, $image_height);
               imagedestroy($image);
               
               $image_center = self::canvas($width, $height, $color, $mime_type, $truecolor, $transparent, $alpha);
               ImageCopy($image_center, $image_resized,(0-$width_space),(0-$height_space),0,0,$width_new, $height_new);
               imagedestroy($image_resized);      
               
               return $image_center;
            }
                
      }
?>