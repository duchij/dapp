<?php

error_reporting(-1);
ini_set('display_errors', 1);
//header("Access-Control-Allow-Origin: *");

setlocale(LC_ALL, "sk_SK");

if (session_id()===""){
    session_start();
}


$_SESSION["sid"] = session_id();

//var_dump(__DIR__);


//64 Jozko Babala
//$_SESSION["_sf2_attributes"]["election2017data"]["member_id"] = "M-0055";
//$_SESSION['_sf2_attributes']["espes_valid_member"]["membership_id"] = 64; //67,71,122



DEFINE("APP_DIR",__DIR__.DIRECTORY_SEPARATOR);

//try {
  //  $settings = yaml_parse_file(APP_DIR."/settings/main.yaml");
 //}catch (Exception $e)
 //{
     $settings = parse_ini_file("settings/main.ini");
 //}


DEFINE("MAIN_SERVER",$settings["main_server"]);
DEFINE("SQL_DB", $settings["storage_url"]);
DEFINE("OS_DIR",$settings["os_dir"]);
DEFINE("O_URL",$settings["o_url"]); //Main DICOM/ORTHANC Server
// DEFINE("O_C_URL",$settings["client_server"]); //Client DICOM/ORTHANC
DEFINE("WEB_URL",$settings["web_url"]);
DEFINE("ROUTER",$settings["o_router"]);
DEFINE("INCLUDE_DIR",__DIR__.DIRECTORY_SEPARATOR."include".DIRECTORY_SEPARATOR);
//DEFINE("IM_DIR",$settings["imagemagick_dir"]);
DEFINE("PUBLIC_DIR",__DIR__.DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR);
DEFINE("SALT",$settings["salt"]);

DEFINE("MAIL_SERV",$settings["mail_server"]);
DEFINE("MAIL_ACC",$settings["mail_acc"]);
DEFINE("MAIL_PSS",$settings["mail_pss"]);
DEFINE("MAIL_SMTP",$settings["mail_smtp"]);
DEFINE("MAIL_PORT",$settings["mail_smtp_out"]);
DEFINE("SEND_MAIL",intval($settings["send_mail"]));

DEFINE("MODULE",$settings["main_module"]);
DEFINE("DBSYSTEM",$settings["dbsystem"]);

#DEFINE("AUTO_START","depman"); //this the autostart class, put init function in the class to auto call the class;

$_SESSION[MODULE]["server"] = $settings["mysql_server"];
$_SESSION[MODULE]["user"] = $settings["mysql_root"];
$_SESSION[MODULE]["passwd"] = $settings["mysql_passwd"];
$_SESSION[MODULE]["db"] = $settings["mysql_db"];

$_SESSION[MODULE]["user_tab"] = $settings["user_tab"];

//require_once INCLUDE_DIR.'orthanc.class.php';
require_once INCLUDE_DIR.'log.class.php';
require_once APP_DIR.'smarty/Smarty.class.php';
require_once INCLUDE_DIR."commJs.class.php";
require_once INCLUDE_DIR."database.interf.php";

if (DBSYSTEM=="sqlite"){
    require_once INCLUDE_DIR."sqlite.class.php";
}
if (DBSYSTEM=="mysql"){
    require_once INCLUDE_DIR."mysql2.class.php";
}
require_once INCLUDE_DIR.'main.class.php';
require_once APP_DIR."phpmailer/class.phpmailer.php";
// require_once INCLUDE_DIR.'orthanc.class.php';

?>
