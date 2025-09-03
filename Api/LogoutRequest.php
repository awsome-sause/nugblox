<?php
include '../main/config.php';

if(!$isloggedin){
  header("location: /");
  exit;
}

session_start();
session_unset();
session_destroy();
header("Location: /");
exit;

?>