<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/9/2016
 * Time: 5:58 PM
 */


include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();
$match = new Match($_POST['matchNumber'],$_POST['compID']);

if($match->isConfigured()){
  echo "done";
}
else{
  echo "pending";
}


?>