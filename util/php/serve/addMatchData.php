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


$JSONdata = $_COOKIE['matchData'];
$data = json_decode($JSONdata);
$match = new Match($data->matchID, $data->compID);
$matchID = $data->matchID;
$compID = $data->compID;
$teamNumber = $data->teamNumber;
$helper = new Helper();
$helper->con = $helper->connectToDB();
$helper->con->beginTransaction();


foreach($data->actions as $record){

  $query="";
  $params = array();

  switch($record->eventType){
    case "feed":
        $query = "INSERT INTO feeds(zoneID,mode) VALUES ((SELECT id FROM zones WHERE name = :zone),:mode)";
        $stmt = $helper->con->prepare($query);

        $stmt->bindValue(":zone", trim($record->zone));
        $stmt->bindValue(":mode", trim($record->mode));

      try {
        $stmt->execute();
        $insertID = $helper->con->lastInsertId();
        $query = "INSERT INTO matchFeeds(actionID, matchID, compID,orderID,teamNumber,`mode`)
                                    VALUES(:actionID,:matchID,:compID,:orderID,:teamNumber,:mode)";
        $stmt = $helper->con->prepare($query);
        $stmt->bindValue(":actionID", $insertID);
        $stmt->bindValue(":matchID", $matchID);
        $stmt->bindValue(":compID", $compID);
        $stmt->bindValue(":orderID", $record->orderID);
        $stmt->bindValue(":teamNumber", $teamNumber);
        $stmt->bindValue(":mode", $record->mode);
        $stmt->execute();
      } catch (PDOException $e) {
        echo $e->getMessage();
        echo "matchFeeds";
        $helper->con->rollBack();
        echo "fail";
        return;
      }

      break;
    case "breach":


      $query = "INSERT INTO breaches(startZone,defenseID,endZone,fail,mode)
                            VALUES ((SELECT id FROM zones WHERE name = :startZone),
                                    :defenseID,
                                    (SELECT id FROM zones WHERE name = :endZone),
                                    :fail,
                                    :mode)";
      $stmt = $helper->con->prepare($query);

      $stmt->bindValue(":startZone", trim($record->startZone));
      $stmt->bindValue(":defenseID", trim($record->defenseID));
      $stmt->bindValue(":endZone", trim($record->endZone));
      $stmt->bindValue(":fail", ($record->fail == "true" ? 1 : 0));
      $stmt->bindValue(":mode", trim($record->mode));

      try {
        $stmt->execute();
        $insertID = $helper->con->lastInsertId();
        $query = "INSERT INTO matchBreaches(actionID, matchID, compID,orderID,teamNumber,`mode`)
                                    VALUES(:actionID,:matchID,:compID,:orderID,:teamNumber,:mode)";
        $stmt = $helper->con->prepare($query);
        $stmt->bindValue(":actionID", $insertID);
        $stmt->bindValue(":matchID", $matchID);
        $stmt->bindValue(":compID", $compID);
        $stmt->bindValue(":orderID", $record->orderID);
        $stmt->bindValue(":teamNumber", $teamNumber);
        $stmt->bindValue(":mode", $record->mode);
        $stmt->execute();
      } catch (PDOException $e) {
        echo $e->getMessage();
        echo "matchBreaches";
        $helper->con->rollBack();
        echo "fail";
        return;
      }

      break;
    case "shoot":

      $query = "INSERT INTO shoots(coordX,coordY,highLow,scoreMiss,mode)
                            VALUES (:coordX,:coordY,:highLow,:scoreMiss,:mode)";
      $stmt = $helper->con->prepare($query);

      $stmt->bindValue(":coordX", trim($record->coordX));
      $stmt->bindValue(":coordY", trim($record->coordY));
      $stmt->bindValue(":highLow", intval($record->highLow));
      $stmt->bindValue(":scoreMiss", intval($record->scoreMiss));
      $stmt->bindValue(":mode", trim($record->mode));

      try {
        $stmt->execute();
        $insertID = $helper->con->lastInsertId();
        $query = "INSERT INTO matchShoots(actionID, matchID, compID,orderID,teamNumber,`mode`)
                                    VALUES(:actionID,:matchID,:compID,:orderID,:teamNumber,:mode)";
        $stmt = $helper->con->prepare($query);
        $stmt->bindValue(":actionID", $insertID);
        $stmt->bindValue(":matchID", $matchID);
        $stmt->bindValue(":compID", $compID);
        $stmt->bindValue(":orderID", $record->orderID);
        $stmt->bindValue(":teamNumber", $teamNumber);
        $stmt->bindValue(":mode", $record->mode);

        $stmt->execute();
      } catch (PDOException $e) {
        echo $e->getMessage();
        echo "matchSchoots";
        $helper->con->rollBack();
        echo "fail";
        return;
      }

      break;
  }
}

$query = "INSERT INTO climbs(batterReached,duration) VALUES (:batterReached,:duration)";
$stmt = $helper->con->prepare($query);

$stmt->bindValue(":batterReached", ($data->endGame->batterReached == "true" ? 1 : 0));

$time = explode(":",$data->endGame->duration);

$stmt->bindValue(":duration", intval($time[0]) * 60 + intval($time[1]));

try {
  $stmt->execute();
  $insertID = $helper->con->lastInsertId();
  $query = "INSERT INTO matchClimbs(actionID, matchID, compID,teamNumber,`mode`)
                                    VALUES(:actionID,:matchID,:compID,:teamNumber,:mode)";
  $stmt = $helper->con->prepare($query);
  $stmt->bindValue(":actionID", $insertID);
  $stmt->bindValue(":matchID", $matchID);
  $stmt->bindValue(":compID", $compID);
  $stmt->bindValue(":teamNumber", $teamNumber);
  $stmt->bindValue(":mode", $teamNumber);
  $stmt->execute();

  $query = "UPDATE teamReservations SET collectionEnded = 1, matchData = :matchData WHERE compID = :compID AND matchID = :matchID AND teamNumber = :teamNumber";
  $stmt = $helper->con->prepare($query);
  $stmt->bindValue(":matchData", $JSONdata);
  $stmt->bindValue(":compID", $compID);
  $stmt->bindValue(":matchID", $matchID);
  $stmt->bindValue(":teamNumber", $teamNumber);
  $stmt->execute();
} catch (PDOException $e) {
  echo $e->getMessage();
  echo "teamReservations";
  $helper->con->rollBack();
  echo "fail";
  return;
}


$helper->con->commit();
echo "Success";



?>