<?php
require_once 'RestUtils.php';
//echo $_SERVER['REQUEST_URI'];

$data = RestUtils::processRequest();
echo json_encode($data);
?>