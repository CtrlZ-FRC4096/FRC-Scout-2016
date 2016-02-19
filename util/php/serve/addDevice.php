<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/9/2016
 * Time: 5:09 PM
 */

include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();

if($helper->addDevice($_POST['deviceID'])){
  echo "Success";
}
else{
  echo "Fail";
}





?>