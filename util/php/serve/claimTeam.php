<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/4/2016
 * Time: 10:49 PM
 */


include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");
$match = new Match($_POST['matchNumber'],$_POST['compID']);

if($match->isTeamClaimed($_POST['teamNumber'])){
  echo "fail";
}
else{
  if($match->claimTeam($_POST['teamNumber'],$_COOKIE['deviceID'],$_POST['scouterName'])){
    echo "success";
  }
  else{
    echo "fail";
  }
}




?>