<?php
ini_set('session.cookie_lifetime', 2592000); 
ini_set('session.gc_maxlifetime', 2592000);
session_start();

if(!isset($_SESSION['is_logged_in'])) {
    header('Location: login.php');
    exit();
}