<?php

include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();
$currCompetition = $helper->getCurrentCompetition();
if(isset($_COOKIE['deviceID'])){

  $status = $helper->getCurrentStatusOfUser($_COOKIE['deviceID'],$currCompetition->id);
  if(strpos($status ,"teamSelection") !== false){

    unset($_COOKIE["matchData"]);
// empty value and expiration one hour before
    $res = setcookie("matchData", '', time() - 3600);

    if(strpos($status,"-")){
      $arr = explode("-",$status);
      $WAITING_FOR_CONFIG = true;
      $WAITING_ON_TEAM = intval($arr[1]);
      $match = new Match(intval($arr[2]),intval($currCompetition->id));
    }
    else{
      $WAITING_FOR_CONFIG = false;
      $WAITING_ON_TEAM = null;
    }

    require_once("teamSelection.php");
  }
  else{
    $arr = explode("-",$status);
    $match = new Match(intval($arr[1]),intval($currCompetition->id));
    if($match->isConfigured()){
      require_once("matchScouting.php");
    }
    else{
      require_once("teamSelection.php");
    }
  }


}

else{
      setcookie("deviceID",$helper->getRandomDeviceID(),3650,"/");
  $WAITING_FOR_CONFIG = false;
  require_once("teamSelection.php");
}




?>