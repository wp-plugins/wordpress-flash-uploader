<?php
/**
 *   This file contains the methods used by the synch part from the WFU class
 *
 */

if (!class_exists("WFUSync")) {
    class WFUSync {

        function printSync($devOptions, $istab) {

            // this is printed first to get a header while generating thumbnails.
            echo '<div class="wrap wfupadding">
<form method="post" action="'. $_SERVER["REQUEST_URI"] . '">
<div id="icon-upload" class="icon_jfu"><br></div>
<h2>Synchronize Media Library</h2>';
flush();
            $mlf = WFUSync::getMediaLibraryFiles();
            $uff = WFUSync::getUploadFolderFiles('../' . WFUSync::getUploadPath());
            $enable_sych = ($uff !== false);
            if (!$enable_sych) {
              $uff = array();
            }  
            $mfo = WFUSync::getMediaLibraryOnly($mlf);
            $fuo = WFUSync::findUploadOnly($mlf, $uff);

            if (isset($_POST['synchronize_media_library']) || isset($_POST['clean_media_library'])  || isset($_GET['clean_media_library']) ||
                isset($_POST['synchronize_media_library']) || isset($_POST['import_media_library']) || isset($_GET['import_media_library'])) {
                echo '<script type="text/javascript">
              if (window.parent.frames[window.name] && (parent.document.getElementsByTagName(\'frameset\').length <= 0)) {
                window.parent.document.getElementById("status_text").innerHTML = "Starting synchronisation.";
              }</script>';
            }

            if (isset($_POST['synchronize_media_library']) || isset($_POST['clean_media_library'])) {
                // we remove the ones tat are not in the upload folder anymore.
                echo '<div class="updated"><p><strong>';
                if (count($mfo) > 0) {
                  foreach($mfo as $item) {
                      if ($item->type == 'main') {
                          wp_delete_post($item->post_id);
                      } else { // metadata stuff! we update the database table!
                          $data = $item->data;
                          wp_update_attachment_metadata($item->post_id, $data);
                      }
                  } 
                  echo _e("Invalid media library entries where removed.", "WFU");  
                } else {
                  echo _e("No invalid media library entries found.", "WFU");    
                }
                echo '</strong></p></div>';
                
            }

            if (isset($_POST['synchronize_media_library'])
                || isset($_POST['import_media_library']) || isset($_GET['import_media_library'])) {
                $sum = count ($fuo);
                $current = 0;
                foreach($fuo as $item) {
                    $current++;
                    WFUSync::handle_import_file($item, $current, $sum);
                }
                echo '<div class="updated"><p><strong>';
                if ($current > 0) {
                  echo _e("Files imported to media library.", "WFU");
                } else {
                  echo _e("No files found which are not already in the media library.", "WFU");
                }
                echo '</strong></p></div>';
            }

            echo '<script type="text/javascript">
      if (window.parent.frames[window.name] && (parent.document.getElementsByTagName(\'frameset\').length <= 0)) {
        window.parent.document.getElementById("status_text").innerHTML = "Synchronisation finished.";
      }</script>';

            if (isset($_POST['synchronize_media_library'])
                || isset($_POST['clean_media_library'])
                || isset($_POST['import_media_library'])) {
                // we reload the data.
                $mlf = WFUSync::getMediaLibraryFiles();
                $uff = WFUSync::getUploadFolderFiles('../' . WFUSync::getUploadPath());
                if (!$enable_sych) {
                  $uff = array();
                } 
                $mfo = WFUSync::getMediaLibraryOnly($mlf);
                $fuo = WFUSync::findUploadOnly($mlf, $uff);
            }

            $count_mfo = 0;
            foreach($mfo as $item) {
                if ( $item->type == 'main') $count_mfo++;
            }

            $nr_ok = count($mlf) - $count_mfo;
if ($enable_sych) {
            echo '
<p>
If you upload files by WFU or FTP or by any other tool than the internal uploader of Wordpress the files do not get listed in the media library.
<div class="wfu_reg">
';
            if (!$istab) {
                echo '
<p><b>Import files to Media Library:</b> All files below the "'.WFUSync::getUploadPath().'" folder are checked if they do already exist in the media library. If they don\'t exist they are entered and can be managed in the media library. Image exif/iptc data are used as defaults for title and caption if possible.</p>
<p><b>Remove invalid Media Library entries:</b> The database is checked if all files still exist. Data of deleted files (link, title, caption ...) are removed from the media library.</p>
<p><b>Synchronize Media Library:</b> Import and Remove.</p>
';
            }
            echo '
<div class="submit">';
            if (!$istab) {
                echo '
<input type="submit" name="synchronize_media_library" value="';
                echo _e('Synchronize Media Library', 'WFU');
                echo '" />';
            }
            echo '
<input type="submit" name="import_media_library" value="';
            echo _e('Import files to Media Library', 'WFU');
            echo '" />';
            if (!$istab) {
                echo '
<input type="submit" name="clean_media_library" value="';
                echo _e('Remove invalid Media Library entries', 'WFU');
                echo '" />';
            }

            echo '</div>';

            echo '<h3>Current status</h3>';
            echo '<table><tr valign="top"><td>Files in upload folder and media library<br>which are in sync.<br>&nbsp;<br></td><td style="padding-left:20px;">'.$nr_ok.'<br>&nbsp;</td><td> </td></tr>
<tr valign="top"><td>Files only in upload folder</td><td style="padding-left:20px;">'.count($fuo) . '</td><td style="padding-left:20px;">';
            foreach($fuo as $item) {
                echo htmlentities(WFUSync::stripAboveUpload($item)) . '<br>';
            }
            echo '&nbsp;';
            echo '</td></tr>
<tr valign="top"><td>Files only in media library</td><td style="padding-left:20px;">'.count($mfo) . '</td><td style="padding-left:20px;">';
            foreach($mfo as $item) {
                echo htmlentities(WFUSync::stripAboveUpload($item->meta_value)) . (($item->type == 'main') ? '': ' <span style="color:#666;"><small>('.$item->type.')</small></span>') .'<br>';
            }
            echo '&nbsp;';
            echo '</td></tr>
</table></form>';
            echo '<br>';
            echo '<div class="howto"><small>* Please note: The numbers are always the number of original images.<br>Thumbnails, medium and large images are not counted here but they are synchronized as well.<br>Not existing thumbnails, medium and large images are removed from the meta data of the image.</small></div>';
            echo '</div>';

            if (!$istab && $devOptions['hide_donate'] == 'false') {
                echo '
    <br>&nbsp;
    <table><tr><td>You like this plugin? Support the development with a small donation. </td><td>&nbsp;&nbsp;&nbsp;<A target="_blank" HREF="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40mdempfle%2ede&item_name=WP%20Flash%20Uploader&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=EUR&lc=EN&bn=PP%2dDonationsBF&charset=UTF%2d8"><img src="../wp-content/plugins/wordpress-flash-uploader/img/btn_donate_SM.gif"></A></td></tr></table>
    
            
    ';    
            }
            }
        }
        
              
        function stripAboveUpload($str) {
            $str = str_replace("\\","/",$str);
            if (stristr($str, WFUSync::getUploadPath()) === false) {
                return $str;
            } else {
                $pos = stripos($str, WFUSync::getUploadPath() . '/');
                return substr($str, $pos + strlen(WFUSync::getUploadPath())+1);
            }
        }
        
        /*
         function stripAboveUpload($str) {
            $str = str_replace("\\","/",$str);
            if (stristr($str, 'uploads') === false) {
                return $str;
            } else {
                $pos = stripos($str, 'uploads/');
                return substr($str, $pos + 8);
            }
        }
        */


        function stripAfterUpload($str) {
            echo $str . "<br>";
            $str = str_replace("\\","/",$str); 
            
            $pos = stripos($str, WFUSync::getUploadPath() . '/');
            $str =  substr($str, $pos + strlen(WFUSync::getUploadPath())+1);
             
           /* $str =  stristr($str, WFUSync::getUploadPath());
            echo $str . "<br>";
            $str = substr(stristr($str, '/'),1); 
            echo $str . "<br>";
            */
            echo $str . "<br>";
            return $str;
        }
      
         /*  
         function stripAfterUpload($str) {
            echo $str . "<br>";
            $str = str_replace("\\","/",$str);
            $str =  stristr($str, 'uploads');
            echo $str . "<br>"; 
            $str = substr(stristr($str, '/'),1); 
            echo $str . "<br>";
            return $str;
        }
        */

        function getMediaLibraryFiles() {
            global $wpdb;
            $sql= "SELECT pm.post_id, pm.meta_id, pm.meta_value, pma.meta_value as meta_att FROM $wpdb->posts p,$wpdb->postmeta pm, $wpdb->postmeta pma WHERE pm.post_id=p.id and pma.post_id=pm.post_id and p.post_type = 'attachment' and pm.meta_key='_wp_attached_file' and pm.meta_value <> pma.meta_value order by pm.meta_value ";
            $mlf = $wpdb->get_results( $sql );
            return $mlf;
        }

        function getUploadFolderFiles( $from = '../wp-content/uploads') {
            if(!is_dir($from)) {
               echo '<div class="updated"><p><strong>';
               echo _e("Upload folder does not exist yet. Please upload at least one file.", "WFU");
               echo '</strong></p></div>';
               return false;
            }

            $files = array();
            $dirs = array( $from);
            while( NULL !== ($dir = array_pop( $dirs)))
            {
                if( $dh = opendir($dir))
                {
                    while( false !== ($file = readdir($dh)))
                    {
                        if( $file == '.' || $file == '..')
                        continue;
                        $path = $dir . '/' . $file;
                        if( is_dir($path))
                        $dirs[] = $path;
                        else
                        $files[] = $path;
                    }
                    closedir($dh);
                }
            }
            return $files;
        }

        function getMediaLibraryOnly($mlf) {
            $mfo = array();
            foreach($mlf as $item) {
                $main = false;
                // echo $item->meta_value . '<br>';
                // files have either a full path or the relative path in the uploads folder.
                if (!file_exists($item->meta_value) && !file_exists('../' . WFUSync::getUploadPath() . '/' . $item->meta_value)) {
                    $item->type = 'main';
                    $main = true;
                    $mfo[] = $item;
                }

                if (!$main) { // we check the meta data if the main image is o.k.
                    $data = unserialize($item->meta_att);
                    // todo - check for thumbnails
                    $base = dirname($data['file']);

                    if (isset($data['sizes'])) {
                        if (isset($data['sizes']['thumbnail']) && isset($data['sizes']['thumbnail']['file'])) {
                            $thumbnail =  $base . '/' . $data['sizes']['thumbnail']['file'];
                            if (!file_exists($thumbnail) && !file_exists('../'.WFUSync::getUploadPath().'/' . $thumbnail)) {
                                unset($error);
                                $error->meta_value = $thumbnail;
                                $error->type = 'thumbnail';
                                $error->post_id = $item->post_id;
                                unset($data['sizes']['thumbnail']);
                                $error->data = $data;
                                $mfo[] = $error;
                            }
                        }
                        if (isset($data['sizes']['medium']) && isset($data['sizes']['medium']['file'])) {
                            $medium =  $base . '/' . $data['sizes']['medium']['file'];
                            if (!file_exists($medium) && !file_exists('../'.WFUSync::getUploadPath().'/' . $medium)) {
                                unset($error);
                                $error->meta_value = $medium;
                                $error->type = 'medium';
                                $error->post_id = $item->post_id;
                                unset($data['sizes']['medium']);
                                $error->data = $data;
                                $mfo[] = $error;
                            }
                        }
                        if (isset($data['sizes']['large']) && isset($data['sizes']['large']['file'])) {
                            $large =  $base . '/' . $data['sizes']['large']['file'];
                            if (!file_exists($medium) && !file_exists('../'.WFUSync::getUploadPath().'/' . $large)) {
                                unset($error);
                                $error->meta_value = $large;
                                $error->type = 'large';
                                $error->post_id = $item->post_id;
                                unset($data['sizes']['large']);
                                $error->data = $data;
                                $mfo[] = $error;
                            }
                        }
                    }
                }
            }

            return $mfo;
        }

        function findUploadOnly($media, $filesystem) {
            $fuo = array();
            $wfuOptions = $this->getAdminOptions();
             
            foreach($filesystem as $fitem) {

                $found = false;
                foreach($media as $item) {
                    $v1 =  realpath('../'.WFUSync::getUploadPath().'/' . $item->meta_value);
                    // echo $v1 . "<br>";
                    $v2 =  realpath($item->meta_value);
                    $v3 = ($v1) ? $v1:$v2;
                    if (realpath($fitem) == $v3) {
                        // echo "found";
                        $found = true;
                        break; // we have found this element - we search the next one.
                    }

                    $base = dirname($v3);
                    // now we check the metadata
                    $data = unserialize($item->meta_att);
                    if (isset($data['sizes']) && isset($data['sizes']['thumbnail']) && isset($data['sizes']['thumbnail']['file'])) {
                      $thumbnail =  realpath($base . '/' . $data['sizes']['thumbnail']['file']);
                      if (realpath($fitem) == $thumbnail) { $found = true; break; }
                    }
                    if (isset($data['sizes']) && isset($data['sizes']['medium']) && isset($data['sizes']['medium']['file'])) {
                      $medium =  realpath($base . '/' . $data['sizes']['medium']['file']);
                      if (realpath($fitem) == $medium) { $found = true; break; }
                    }
                    if (isset($data['sizes']) && isset($data['sizes']['large']) && isset($data['sizes']['large']['file'])) {
                      $large =  realpath($base . '/' . $data['sizes']['large']['file']);
                      if (realpath($fitem) == $large) { $found = true; break; }
                    }
                }
                if (!$found) {
                    $add = true;
                    
                    if ($wfuOptions['detect_resized'] == "true") {
                      foreach($filesystem as $itemcomp) {
                        // we check if the file is maybe already a crunched file and if yes we ignore it
                        // the detection is very basic - I check the file name and if another one has 
                        // the same filename with a - as next character we ignore it. 
                        if (strlen($fitem) > strlen($itemcomp)) { // we check if it is longer                   
                          $c1 = WFUSync::removeExtension($itemcomp) . '-';
                          $c2 = substr($fitem,0,strlen($c1));
                          if ($c1 == $c2) {
                            $add = false;  
                          } 
                        }
                      }  
                    }
                    
                    if ($add) {
                      $fuo[] = realpath($fitem);
                    }
                }
            }
            return $fuo;
        }

        //Handle an individual file import. This function is based on the one from add-from-server
        function handle_import_file($file, $current, $sum, $post_id = 0) {
            $post_id = isset($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : 0;

            $file = str_replace('\\', '/',$file);

            // we have to replace special characters because wordpress does not handle them properly.
            $filenorm = WFUSync::normalizeFileNames($file);
            if (rename ($file, $filenorm)) {
                $file =  $filenorm;
            }
            // $path = WFUSync::stripAfterUpload($file);
            $path = WFUSync::stripAboveUpload($file);
            
            $time = current_time('mysql');
            $uploads = wp_upload_dir();
            $wp_filetype = wp_check_filetype( $file, null );
            extract( $wp_filetype );
            $filename = basename($file);
            $new_file = $path;
            $url = $uploads['baseurl'] . '/' . $path;

            // get the right time.
            // it it is in an folder with date we use this one
            // if not we use the current time
            // the handling if it can from a post has to be handled in the flash implementation!
            $time = current_time('mysql');
            if ( $post = get_post($post_id) ) {
                if ( substr( $post->post_date, 0, 4 ) > 0 )
                $time = $post->post_date;
            } else {
                $time = filemtime($file);
            }

            $post_date = date( 'Y-m-d H:i:s', $time);
            $post_date_gmt = gmdate( 'Y-m-d H:i:s', $time);



            //Apply upload filters
            $return = apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ) );
            $new_file = $return['file'];
            $url = $return['url'];
            $type = $return['type'];
            $title = preg_replace('!\.[^.]+$!', '', basename($file));
            $content = '';
            // use image exif/iptc data for title and caption defaults if possible
            if ( $image_meta = @wp_read_image_metadata($new_file) ) {
                if ( '' != trim($image_meta['title']) )
                $title = trim($image_meta['title']);
                if ( '' != trim($image_meta['caption']) )
                $content = trim($image_meta['caption']);
            }

            // Construct the attachment array
            $attachment = array(
            'post_mime_type' => $type,
            'guid' => $url,
            'post_parent' => $post_id,
            'post_title' => $title,
            'post_name' => $title,
            'post_content' => $content,
            'post_date' => $post_date,
            'post_date_gmt' => $post_date_gmt
            );
            // Save the data
            $id = wp_insert_attachment($attachment, $new_file, $post_id);
            if ( !is_wp_error($id) ) {
                echo 'Crunching ('.$current.'/'.$sum.'): ' . htmlentities($filename) . '<br>';
                echo '<script type="text/javascript">
      if (window.parent.frames[window.name] && (parent.document.getElementsByTagName(\'frameset\').length <= 0)) {
        window.parent.document.getElementById("status_text").innerHTML = "Crunching ('.$current.'/'.$sum.'): ' . htmlentities($filename).'";
      }</script>';
                flush(); ob_flush();
                $data = wp_generate_attachment_metadata( $id, $file );
                $data['file'] = $new_file; // fix to get the right file name into the database!

                wp_update_attachment_metadata( $id, $data );
            }
            return $id;
        }

        function normalizeFileNames($imageName){
            // we make the file name lowercase
            $imageName = strtolower($imageName);

            // Some characters I know how to fix ;).
            $imageName=str_replace(array('ä','ö','ü','ß'),array('ae','oe','ue','ss'),$imageName);  
            // and some others might need
            $imageName=str_replace(array('á','à','ã','â','ç','¢','é','ê','è','ë','í','î','ï','ì','ñ','ô','ó','õ','ò','š','r','ú','ù','û','ü','ý','ÿ','ž'),
                                   array('a','a','a','a','c','c','e','e','e','e','i','i','i','i','n','o','o','o','o','s','r','u','u','u','u','y','y','z'),$imageName);  
            // we remove the rest of unwanted chars
            $patterns[] = '/[\x7b-\xff]/';  // remove all characters above the letter z.  This will eliminate some non-English language letters
            $patterns[] = '/[\x21-\x2c]/'; // remove range of shifted characters on keyboard - !"#$%&'()*+
            $patterns[] = '/[\x5b-\x60]/'; // remove range including brackets - []\^_`
            // we remove all kind of special characters for utf8 encoding as well
            $patterns[] = '/[\x7b-\xff]/u';  // remove all characters above the letter z.  This will eliminate some non-English language letters
            $patterns[] = '/[\x21-\x2c]/u'; // remove range of shifted characters on keyboard - !"#$%&'()*+
            $patterns[] = '/[\x5b-\x60]/u'; // remove range including brackets - []\^_`
            $replacement ="_";
            return preg_replace($patterns, $replacement, $imageName);
        }

        function getUploadPath() {
          $upload_path = get_option('upload_path');
          if ($upload_path == '') {
              $upload_path = 'wp-content/uploads';
            }
           // we have to make the path relative! if we find wp-content we remove everything before!
            if (stristr($upload_path, 'wp-content') !== false) {
                $upload_path = stristr($upload_path, 'wp-content');
            }
          return $upload_path;
        }
        
        function removeExtension($name)
        {
            return substr($name, 0, strrpos ($name, '.'));
        }


    }}

?>