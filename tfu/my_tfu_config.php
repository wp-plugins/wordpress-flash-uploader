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
  tfu_debug("Config call, illegal direct access or missing session settings - your browser has to be closed to get a new session. Please check your session_save_path if you get this error all the time or create the folder session_cache in the tfu folder to activate the session workaround.");
  echo '
  <style type="text/css">
  body { 	font-family : Arial, Helvetica, sans-serif; font-size: 12px; background-color:#ffffff; }
  td { vertical-align: top; font-size: 12px; }
  .install { text-align:center; margin-left: auto;  margin-right: auto;  margin-top: 3em;  margin-bottom: 3em; padding: 10px; border: 1px solid #cccccc;  width: 450px; background: #F1F1F1; }
  </style>';
  echo '<div class="install">';
  echo 'You server is configured properly to access the needed files of WFU.<br>Please go to the Administration of Wordpress to see your server limits.';
  echo '</div>';
  // maybe the session is lost - we try to do the workaround if the file was called by a parameter!
  if (strlen($_SERVER['QUERY_STRING']) > 5) {
    checkSessionTempDir();
  }
  die();
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
$dir_chmod=($_SESSION["TFU_DIR_CHMOD"] == '') ? 0 : octdec($_SESSION["TFU_DIR_CHMOD"]);

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

// get user/role defined configs 
$user_loaded = false;
if (isset($_SESSION["WFU_USER_LOGIN"])) {
  // load user - look for tfu_config_<user>.php 
  if (file_exists('tfu_config_' . $_SESSION["WFU_USER_LOGIN"] . '.php')) {
    include ('tfu_config_' . $_SESSION["WFU_USER_LOGIN"] . '.php');
    $user_loaded = true;
  }
}

if (!$user_loaded && isset($_SESSION["WFU_USER_ROLE"])) {
  // load role - look for tfu_config_<role>.php
 if (file_exists('tfu_config_'. $_SESSION["WFU_USER_ROLE"] . '.php')) {
    include ('tfu_config_'. $_SESSION["WFU_USER_ROLE"] . '.php');
  }
}

$zip_folder = $folder; // has to be set again!

?>