<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/19/2016
 * Time: 9:39 PM
 */

include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");


$matches = json_decode($_POST['matches']);
$compID = $_POST['compID'];
$helper = new Helper();

$data = array();
$data['data'] = array();

foreach($matches as $matchNumber){
  $matchData = array();

  $query = "SELECT matchData FROM teamReservations WHERE matchID = :matchID AND compID = :compID";
  $params = array(":matchID" => $matchNumber, ":compID" => $compID);
  $teamReservations = $helper->queryDB($query,$params,false);
  $matchData['teamReservations'] = $teamReservations;

  $query= "SELECT * FROM matchDefenses WHERE compID = :compID AND matchId = :matchID";
  $params = array(":compID" => $compID, ":matchID" => $matchNumber);
  $matchDefenses = $helper->queryDB($query,$params,false);
  $matchData['matchDefenses'] = $matchDefenses;

  $data['data'][$matchNumber] = $matchData;

}

$query= "SELECT * FROM devices";
$params = null;
$devices = $helper->queryDB($query,$params,false);
$data['devices'] = $devices;

echo json_encode($data);


?>