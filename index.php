<?php
// echo $_SERVER['REQUEST_URI'];
require_once 'RestUtils.php';

$data = RestUtils::processRequest ();
echo json_encode ( $data, True);
?>