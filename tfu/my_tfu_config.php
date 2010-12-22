<?php
/**
 * Joomla Flash uploader 2.8 Freeware - for Joomla 1.0.x and Joomla 1.5.x - based on TWG Flash uploader 2.8
 *
 * Copyright (c) 2004-2008 TinyWebGallery
 * written by Michael Dempfle
 * 
 *  This is the config file where sll the JFU stuff from the wrapper is set.
 *  Since 2.8 99% of the TFU addoptions needed to use TFU for JFU are in this file! 
 *  
 *  The commented settings cannot be set by the backend - if you want to set them you 
 *  have to uncomment it an set it    
 * 
 *   Have fun using JFU
 */
/** ensure this file is being included by a parent file */
defined( '_VALID_TWG' ) or die( 'Direct Access to this location is not allowed.' );

/*
    Joomla related settings
*/


if (isset($_SESSION["IS_ADMIN"])) {
  $wp_path = "../wp-content/plugins/wordpress-flash-uploader/tfu/";
} else if (isset($_SESSION["IS_FRONTEND"])) {
  $wp_path = "wp-content/plugins/wordpress-flash-uploader/tfu/";
} else { // we only show the info.
  return;
}

/*
    WPU CONFIGURATION
*/

$login = "true"; // The login flag - has to set by yourself below "true" is logged in, "auth" shows the login form, "reauth" should be set if the authentification has failed. "false" if the flash should be disabled.  
$folder = $_SESSION["TFU_FOLDER"];
$base_dir = $wp_path; 


$maxfilesize = ($_SESSION["TFU_MAXFILESIZE"] !="") ?  $_SESSION["TFU_MAXFILESIZE"] : getMaximumUploadSize();
$resize_show = ($_SESSION["TFU_RESIZE_SHOW"] =="true") ? is_gd_version_min_20() : "false";
$resize_data = $_SESSION["TFU_RESIZE_DATA"];  
$resize_label = $_SESSION["TFU_RESIZE_LABEL"]; 
$resize_default = $_SESSION["TFU_RESIZE_DEFAULT"];            
$allowed_file_extensions = $_SESSION["TFU_ALLOWED_FILE_EXTENSIONS"]; 
$forbidden_file_extensions = $_SESSION["TFU_FORBIDDEN_FILE_EXTENSIONS"]; 
         
$enable_folder_browsing = $_SESSION["TFU_ENABLE_FOLDER_BROWSING"]; 
$enable_folder_creation = $enable_folder_deletion = $enable_folder_rename = $_SESSION["TFU_ENABLE_FOLDER_HANDLING"]; 
$enable_file_rename = $_SESSION["TFU_ENABLE_FILE_RENAME"]; 
     
$show_size = ($_SESSION["TFU_SHOW_SIZE"] == 'true') ? 'true' : '';
$normalise_file_names = $normalise_directory_names = $normalizeSpaces = $_SESSION["TFU_NORMALIZE"];
$file_chmod=($_SESSION["TFU_FILE_CHMOD"] == '') ? 0 : octdec($_SESSION["TFU_FILE_CHMOD"]);

$language_dropdown = $_SESSION["TFU_LANGUAGE_DROPDOWN"];
$use_image_magic = ($_SESSION["TFU_USE_IMAGE_MAGIC"] == "true");
$image_magic_path = $_SESSION["TFU_IMAGE_MAGIC_PATH"];

// enables automatic syncronisation after upload.
$upload_finished_js_url = 'true';
$delete_js_url='true';

// the text of the email is stored in the tfu_upload.php if you like to change it :)
$upload_notification_email = $_SESSION["TFU_UPLOAD_NOTIFICATION_EMAIL"];
$upload_notification_email_from = $_SESSION["TFU_UPLOAD_NOTIFICATION_EMAIL_FROM"];
$upload_notification_email_subject = $_SESSION["TFU_UPLOAD_NOTIFICATION_EMAIL_SUBJECT"];
$upload_notification_email_text = $_SESSION["TFU_UPLOAD_NOTIFICATION_EMAIL_TEXT"];
/**
 * Extra settings for the registered version
 */
$enable_file_download = $_SESSION["TFU_ENABLE_FILE_DOWNLOAD"];   
$enable_folder_move=$_SESSION["TFU_ENABLE_FOLDER_MOVE"];       
$enable_file_copymove=$_SESSION["TFU_ENABLE_FILE_COPYMOVE"];        
$preview_textfile_extensions = $_SESSION["TFU_PREVIEW_TEXTFILE_EXTENSIONS"]; 
$edit_textfile_extensions = $_SESSION["TFU_EDIT_TEXTFILE_EXTENSIONS"];  
$exclude_directories = array_map("trim", explode(",", $_SESSION["TFU_EXCLUDE_DIRECTORIES"])); 
$forbidden_view_file_filter = $_SESSION["TFU_FILE_FILTER"]; 

$zip_folder = $folder; // has to be set again!

?>