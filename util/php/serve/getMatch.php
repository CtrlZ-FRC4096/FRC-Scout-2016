<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/3/2016
 * Time: 6:01 PM
 */

include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$match = new Match($_POST['matchID'],$_POST['compID']);

if($_POST['records']){

}

echo json_encode($match);




?>