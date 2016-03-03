<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/18/2016
 * Time: 10:08 PM
 */
ini_set("display_errors", "1");
error_reporting(E_ALL);



include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();
//
//$query = "SELECT * FROM teammatches;";
//$params = null;
//$result = $helper->queryDB($query,$params,false);
//$teleAuto = ['tele','auto'];
//foreach($result as $match){
//
//  $teamMatchID = $match['id'];
//  for($i = 0;$i<=rand(1,12);$i++){
//    $query = "INSERT INTO matchFeeds(teamMatchID,orderID,`mode`,zoneID)
//                                    VALUES(:teamMatchID,:orderID,:mode,:zone)";
//    $params = array(
//      ":teamMatchID" => $teamMatchID,
//      ":orderID" => 1,
//      ":mode" => $teleAuto[array_rand($teleAuto)],
//      ":zone" => array_rand([1,2,3,4,5,6,7]) + 1
//    );
//    var_dump($params);
//    $helper->queryDB($query,$params,false);
//  }
//
//  for($i = 0;$i<=rand(1,12);$i++){
//    $query = "INSERT INTO matchBreaches(teamMatchID,orderID,`mode`,startZone,defenseID,endZone,fail)
//                                    VALUES(:teamMatchID,:orderID,:mode,
//                                    :startZone
//                                    ,:defenseID,
//                                    :endZone,:fail)";
//    $params = array(
//      ":teamMatchID" => $teamMatchID,
//      ":orderID" => 1,
//      ":mode" => array_rand(["tele","auto"]),
//      ":startZone" => rand(1,7),
//      ":defenseID" => rand(1,9),
//      ":endZone" => rand(1,7),
//      ":fail" => rand(0,1)
//    );
//
//    var_dump($params);
//    $helper->queryDB($query,$params,false);
//  }
//
////  for($i = 0;$i<=rand(1,12);$i++){
////    $query = "INSERT INTO matchShoots(teamMatchID,orderID,`mode`,coordX,coordY,highLow,scoreMiss)
////                                    VALUES(:teamMatchID,:orderID,:mode,:coordX,:coordY,:highLow,:scoreMiss)";
////    $params = array(
////      ":teamMatchID" => $teamMatchID,
////      ":orderID" => 1,
////      ":mode" => array_rand(["tele","auto"]),
////      ":coordX" => rand(0,$helper::HALF_FIELD_LENGTH_INCHES),
////      ":coordY" => rand(0,$helper::HALF_FIELD_HEIGHT_INCHES),
////      ":highLow" => rand(0,1),
////      ":scoreMiss" => rand(0,1)
////    );
////    $helper->queryDB($query,$params,false);
////  }
//
////    $query = "INSERT INTO matchClimbs(teamMatchID, `mode`,batterReached,duration,defensiveRating,offensiveRating)
////                                    VALUES(:teamMatchID,:mode,:batterReached,:duration,:defensiveRating,:offensiveRating)";
////    $params = array(
////      ":teamMatchID" => $teamMatchID,
////      ":mode" => array_rand(["tele","auto"]),
////      ":batterReached" => rand(0,1),
////      ":duration" => rand(0,20),
////      ":defensiveRating" => rand(1,10),
////      ":offensiveRating" => rand(1,10)
////    );
////
////    $helper->queryDB($query,$params,false);
//
//}

$query = "SELECT * FROM matches;";
$params = null;
$result = $helper->queryDB($query,$params,false);

foreach($result as $match){
  $slots = array(
    array("red",1),
    array("red",2),
    array("red",3),
    array("red",4),
    array("red",5),
    array("blue",1),
    array("blue",2),
    array("blue",3),
    array("blue",4),
    array("blue",5)
  );
$usedDefenses = array();
  foreach($slots as $slot){
    $query = "INSERT INTO matchdefenses(matchID,side,slot,defenseID) VALUES(:matchID,:side,:slot,:defenseID)";

    $defenseID = null;
    $continue = true;
    while($continue){
      $d = rand(1,9);
      if(in_array($d,$usedDefenses)){

      }
      else{
        $defenseID = $d;
        $continue = false;
      }
    }

    $params = array(
      ":matchID" => $match['id'],
      ":side" => $slot[0],
      ":slot" => $slot[1],
      ":defenseID" => $defenseID
    );
    $helper->queryDB($query,$params,true);


  }
  echo "done";

}
