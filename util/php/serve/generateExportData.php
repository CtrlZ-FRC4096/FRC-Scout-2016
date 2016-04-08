<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 3/7/2016
 * Time: 9:02 PM
 */

include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();
$currCompetition = $helper->getCurrentCompetition();
$finalData = array();
$finalData['matchData'] = array();
foreach(json_decode($_POST['matchNumbers']) as $matchNumber){

  $match = array();

  $query = "SELECT * FROM matches WHERE compID = :compID AND matchNumber = :matchNumber";
  $params = array(":compID" => $currCompetition->id,":matchNumber" => $matchNumber);
  $result = $helper->queryDB($query,$params,false);
  $match['id'] = $result[0]['id'];
  $match['matchNumber'] = $result[0]['matchNumber'];
  $match['compID'] = $result[0]['compID'];

  $match['matchDefenses'] = array();

  $query = "SELECT * FROM matchdefenses WHERE matchID = :matchID";
  $params = array(":matchID" => $match['id']);
  $result =$helper->queryDB($query,$params,false);

  foreach($result as $row){
    $defense = array();
    $defense['side'] = $row['side'];
    $defense['slot'] = $row['slot'];
    $defense['defenseID'] = $row['defenseID'];
    array_push($match['matchDefenses'], $defense);
  }


  $match['teamMatches'] = array();

  $query = "SELECT * FROM teamMatches WHERE matchID = :matchID";
  $params = array(":matchID" => $match['id']);
  $result = $helper->queryDB($query,$params,false);
  $teamMatches = $result;

  foreach($teamMatches as $teamMatchRow){
    $teamMatch = array();

    $teamMatch['id'] = $teamMatchRow['id'];
    $teamMatch['matchID'] = $teamMatchRow['matchID'];
    $teamMatch['side'] = $teamMatchRow['side'];
    $teamMatch['position'] = $teamMatchRow['position'];
    $teamMatch['teamNumber'] = $teamMatchRow['teamNumber'];
    $teamMatch['deviceID'] = $teamMatchRow['deviceID'];
    $teamMatch['collectionStarted'] = $teamMatchRow['collectionStarted'];
    $teamMatch['collectionEnded'] = $teamMatchRow['collectionEnded'];

    $teamMatch['feeds'] = array();

    $query = "SELECT * FROM matchfeeds WHERE teamMatchID = :teamMatchID";
    $params = array(":teamMatchID" => $teamMatch['id']);
    $result = $helper->queryDB($query,$params,false);
    foreach($result as $feedRow){
      $feed = array();
      $feed['id'] = $feedRow['id'];
      $feed['orderID'] = $feedRow['orderID'];
      $feed['mode'] = $feedRow['mode'];
      $feed['zoneID'] = $feedRow['zoneID'];
      array_push($teamMatch['feeds'],$feed);
    }


    $teamMatch['breaches'] = array();


    $query = "SELECT * FROM matchbreaches WHERE teamMatchID = :teamMatchID";
    $params = array(":teamMatchID" => $teamMatch['id']);
    $result = $helper->queryDB($query,$params,false);

    foreach($result as $breachRow){
      $breach = array();

       $breach['id'] = $breachRow['id'];
       $breach['orderID'] = $breachRow['orderID'];
       $breach['mode'] = $breachRow['mode'];
       $breach['startZone'] = $breachRow['startZone'];
       $breach['defenseID'] = $breachRow['defenseID'];
       $breach['endZone'] = $breachRow['endZone'];
       $breach['fail'] = $breachRow['fail'];
      array_push($teamMatch['breaches'],$breach);

    }


    $teamMatch['shoots'] = array();

    $query = "SELECT * FROM matchshoots WHERE teamMatchID = :teamMatchID";
    $params = array(":teamMatchID" => $teamMatch['id']);
    $result = $helper->queryDB($query,$params,false);

    foreach($result as $shootRow){
      $shoot = array();

      $shoot['id'] = $shootRow['id'];
      $shoot['orderID'] = $shootRow['orderID'];
      $shoot['mode'] = $shootRow['mode'];
      $shoot['coordX'] = $shootRow['coordX'];
      $shoot['coordY'] = $shootRow['coordY'];
      $shoot['highLow'] = $shootRow['highLow'];
      $shoot['scoreMiss'] = $shootRow['scoreMiss'];
      array_push($teamMatch['shoots'],$shoot);

    }

    $teamMatch['climbs'] = array();

    $query = "SELECT * FROM matchclimbs WHERE teamMatchID = :teamMatchID";
    $params = array(":teamMatchID" => $teamMatch['id']);
    $result = $helper->queryDB($query,$params,false);

    foreach($result as $climbRow){
      $climb = array();

      $climb['id'] = $climbRow['id'];
      $climb['mode'] = $climbRow['mode'];
      $climb['batterReached'] = $climbRow['batterReached'];
      $climb['duration'] = $climbRow['duration'];
      $climb['offensiveRating'] = $climbRow['offensiveRating'];
      $climb['defensiveRating'] = $climbRow['defensiveRating'];
      $climb['success'] = $climbRow['success'];
      array_push($teamMatch['climbs'],$climb);

    }



    array_push($match['teamMatches'],$teamMatch);

  }

  array_push($finalData['matchData'],$match);

}

$finalData['devices'] = array();

$query = "SELECT * FROM devices";
$params = null;
$result = $helper->queryDB($query,$params,false);

foreach($result as $row){
  array_push($finalData['devices'],$row['deviceID']);
}

echo json_encode($finalData);





?>