<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/9/2016
 * Time: 6:24 PM
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);
include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$match = new Match($_POST['matchNumber'],$_POST['compID']);


if($match->deleteData($_POST['teamNumber'])){
  echo "success";
}
else{
  echo "fail";
}

?>