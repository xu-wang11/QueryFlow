<?php
require_once 'RestUtils.php';


$data = RestUtils::processRequest();
echo json_encode($data);
?>