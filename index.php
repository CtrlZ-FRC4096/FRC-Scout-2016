<?php

include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();
$currCompetition = $helper->getCurrentCompetition();
if(isset($_COOKIE['deviceID'])){

  $status = $helper->getCurrentStatusOfUser($_COOKIE['deviceID'],$currCompetition->id);


  if($status == "teamSelection"){
    require_once("teamSelection.php");
  }
  else{
    $arr = explode("-",$status);
    $match = new Match($arr[1],$currCompetition->id);
    if($match->isConfigured()){
      require_once("matchScouting.php");
    }
    else{
      require_once("teamSelection.php");
    }
  }


}

else{
  require_once("teamSelection.php");
}




?>