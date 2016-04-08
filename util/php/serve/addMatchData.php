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
$teamMatchID = $data->teamMatch->id;
$helper = new Helper();
$helper->con = $helper->connectToDB();
$helper->con->beginTransaction();


foreach($data->actions as $record){

  $query="";
  $params = array();

  switch($record->eventType){
    case "feed":

      try {
        $query = "INSERT INTO matchFeeds(teamMatchID,orderID,`mode`,zoneID)
                                    VALUES(:teamMatchID,:orderID,:mode,(SELECT id FROM zones WHERE name = :zone))";
        $stmt = $helper->con->prepare($query);
        $stmt->bindValue(":teamMatchID", $teamMatchID);
        $stmt->bindValue(":orderID", $record->orderID);
        $stmt->bindValue(":mode", $record->mode);
        $stmt->bindValue(":zone", trim($record->zone));

        $stmt->execute();
      }
      catch (PDOException $e) {
        echo $e->getMessage();
        echo "matchFeeds";
        $helper->con->rollBack();
        echo "fail";
        return;
      }

      break;
    case "breach":

      try {
        $query = "INSERT INTO matchBreaches(teamMatchID,orderID,`mode`,startZone,defenseID,endZone,fail)
                                    VALUES(:teamMatchID,:orderID,:mode,
                                    (SELECT id FROM zones WHERE name = :startZone)
                                    ,:defenseID,
                                    (SELECT id FROM zones WHERE name = :endZone),:fail)";
        $stmt = $helper->con->prepare($query);
        $stmt->bindValue(":teamMatchID", $teamMatchID);
        $stmt->bindValue(":orderID", $record->orderID);
        $stmt->bindValue(":mode", $record->mode);
        $stmt->bindValue(":startZone", trim($record->startZone));
        $stmt->bindValue(":defenseID", trim($record->defenseID));
        $stmt->bindValue(":endZone", trim($record->endZone));
        $stmt->bindValue(":fail", ($record->fail == "true" ? 1 : 0));

        $stmt->execute();
      }
      catch (PDOException $e) {
        echo $e->getMessage();
        echo "matchBreaches";
        $helper->con->rollBack();
        echo "fail";
        return;
      }

      break;
    case "shoot":

      try {
        $query = "INSERT INTO matchShoots(teamMatchID,orderID,`mode`,coordX,coordY,highLow,scoreMiss)
                                    VALUES(:teamMatchID,:orderID,:mode,:coordX,:coordY,:highLow,:scoreMiss)";
        $stmt = $helper->con->prepare($query);
        $stmt->bindValue(":teamMatchID", $teamMatchID);
        $stmt->bindValue(":orderID", $record->orderID);
        $stmt->bindValue(":mode", $record->mode);
        $stmt->bindValue(":coordX", trim($record->coordX));
        $stmt->bindValue(":coordY", trim($record->coordY));
        $stmt->bindValue(":highLow", intval($record->highLow));
        $stmt->bindValue(":scoreMiss", intval($record->scoreMiss));

        $stmt->execute();
      }
      catch (PDOException $e) {
        echo $e->getMessage();
        echo "matchSchoots";
        $helper->con->rollBack();
        echo "fail";
        return;
      }

      break;
  }
}

try {

  $query = "INSERT INTO matchClimbs(teamMatchID, `mode`,batterReached,duration,defensiveRating,offensiveRating,success)
                                    VALUES(:teamMatchID,:mode,:batterReached,:duration,:defensiveRating,:offensiveRating,:success)";
  $stmt = $helper->con->prepare($query);
  $stmt->bindValue(":teamMatchID", $teamMatchID);
  $stmt->bindValue(":mode", 'tele');
  $stmt->bindValue(":batterReached", ($data->endGame->batterReached == "true" ? 1 : 0));
  $stmt->bindValue(":defensiveRating", $data->endGame->defensiveRating );
  $stmt->bindValue(":offensiveRating", $data->endGame->offensiveRating );
  $stmt->bindValue(":success", ($data->endGame->success == "true" ? 1 : 0) );

  $time = explode(":",$data->endGame->duration);

  $stmt->bindValue(":duration", intval($time[0]) * 60 + intval($time[1]));

  $stmt->execute();

  $query = "UPDATE teammatches SET collectionEnded = 1 WHERE id = :teamMatchID";
  $stmt = $helper->con->prepare($query);
  $stmt->bindValue(":teamMatchID", $teamMatchID);

  $stmt->execute();
}
catch (PDOException $e) {
  echo $e->getMessage();
  echo "teamReservations";
  $helper->con->rollBack();
  echo "fail";
  return;
}

try{
  $query = "UPDATE matches SET lastUpdated = :now WHERE id =
                (SELECT matchID FROM teammatches WHERE id = :teamMatchID)";
  $stmt = $helper->con->prepare($query);
  $stmt->bindValue(":teamMatchID", $teamMatchID);
  $stmt->bindValue(":now", time());
  $stmt->execute();

}
catch(PDOException $e){
  echo $e->getMessage();
  echo "setMatchUpdated";
  $helper->con->rollBack();
  echo "fail";
  return;
}



$helper->con->commit();
echo "Success";



?>