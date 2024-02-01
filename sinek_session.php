<?php
    require_once __DIR__ . '/../../init.php';
    $_SESSION['cart'] = array();
    
    header("Location: " . $_GET['redirect_url']);
?>