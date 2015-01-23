<?php
define('A', TRUE);
session_start();

include $_SERVER['DOCUMENT_ROOT'].'/config.php';

if(!$_SESSION['auth']['admin']){
    header("Location: " .URL. "admin/auth/enter.php");
    exit;
}else{
    header("Location: " .URL. "admin/");
    exit;
}

