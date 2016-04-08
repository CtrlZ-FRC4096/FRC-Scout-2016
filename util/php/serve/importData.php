<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 3/8/2016
 * Time: 7:27 PM
 */
include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();

$helper->autoCycleDBConnection = false;
$helper->con = $helper->connectToDB();

$json = file_get_contents($_FILES['file']['tmp_name']);
$data = json_decode($json,true);

foreach($data['devices'] as $deviceID){
  $query = "INSERT IGNORE INTO devices (deviceID) VALUES (:deviceID)";
//
//  WHERE NOT EXISTS (
//    SELECT deviceID FROM devices WHERE deviceID = :deviceID2
//            ) LIMIT 1;

  $params = array(":deviceID" => $deviceID);
  $result = $helper->queryDB($query,$params,false);
}

foreach($data['matchData'] as $match){
  $query = "SELECT id
            FROM matches
            WHERE matchNumber = :matchNumber
              AND compID = :compID";
  $params = array(":matchNumber" => $match['matchNumber'],":compID"=> $match['compID']);
  $result = $helper->queryDB($query,$params,false);
  $matchID = $result[0]['id'];


  foreach($match['matchDefenses'] as $defense){
    $query = "INSERT INTO matchdefenses(matchID, side, slot, defenseID) VALUES (:matchID,:side,:slot,:defenseID)
              ON DUPLICATE KEY UPDATE matchID = VALUES(matchID),side = VALUES(side),slot = VALUES(slot),defenseID = VALUES(defenseID)";


    $params = array(
      ":matchID" => $matchID,
      ":side" => $defense['side'],
      ":slot" => $defense['slot'],
      ":defenseID" => $defense['defenseID']
    );
    $helper->queryDB($query,$params,false);
  }



  $query = "SELECT COALESCE(sum(a), 0)  as ct FROM (SELECT COUNT(*) as a FROM matchBreaches WHERE teamMatchID IN (SELECT id FROM teamMatches WHERE matchID = :matchID1)
            UNION
            SELECT COUNT(*) as a FROM matchClimbs WHERE teamMatchID IN (SELECT id FROM teamMatches WHERE matchID = :matchID2)
            UNION
            SELECT COUNT(*) as a FROM matchFeeds WHERE teamMatchID IN (SELECT id FROM teamMatches WHERE matchID = :matchID3)
            UNION
            SELECT COUNT(*) as a FROM matchShoots WHERE teamMatchID IN (SELECT id FROM teamMatches WHERE matchID = :matchID4)) as b
";

  $params = array(":matchID1" => $matchID,":matchID2" => $matchID,":matchID3" => $matchID,":matchID4" => $matchID);

  $result = $helper->queryDB($query,$params,false);
  if($result[0]['ct'] > 0){
    $matchAction = "Replaced";
  }
  else{
    $matchAction = "Added";
  }
  $query = "DELETE FROM matchBreaches WHERE teamMatchID IN (
              SELECT id FROM teammatches WHERE matchID = :matchID)";

  $params = array(":matchID" => $matchID);
  $result = $helper->queryDB($query,$params,false);

  $query = "DELETE FROM matchFeeds WHERE teamMatchID IN (
              SELECT id FROM teammatches WHERE matchID = :matchID)";
  $result = $helper->queryDB($query,$params,false);

  $query = "DELETE FROM matchShoots WHERE teamMatchID IN (
              SELECT id FROM teammatches WHERE matchID = :matchID)";
  $result = $helper->queryDB($query,$params,false);

  $query = "DELETE FROM matchClimbs WHERE teamMatchID IN (
              SELECT id FROM teammatches WHERE matchID = :matchID)";

  $result = $helper->queryDB($query,$params,false);


  $teams = array();
  foreach($match['teamMatches'] as $teamMatch){
    array_push($teams,$teamMatch['teamNumber']);
  }

  $query = "DELETE FROM teammatches WHERE matchID = :matchID
            AND teamNumber NOT IN(" . implode(', ', $teams) . ")";

  $query = "SELECT * FROM teamMatches WHERE matchID = :matchID";
  $params = array(":matchID" => $matchID);
  $result = $helper->queryDB($query,$params,false);
  foreach($match['teamMatches'] as $teamMatch){
    $matchFound = false;
    foreach($result as $row){
      if($row['teamNumber'] == $teamMatch['teamNumber']){
        $teamMatch['id'] = $row['id'];
        $matchFound = true;
      }
    }
    if(!$matchFound){
      $query = "INSERT INTO teammatches(
                    matchID,
                    side,
                    position,
                    teamNumber,
                    deviceID,
                    collectionStarted,
                    collectionEnded)
                    VALUES(
                    :matchID,
                    :side,
                    :position,
                    :teamNumber,
                    :deviceID,
                    :collectionStarted,
                    :collectionEnded
                    )";
      $params = array(
        ":matchID" => $teamMatch['matchID'],
        ":side" => $teamMatch['side'],
        ":position" => $teamMatch['position'],
        ":teamNumber" => $teamMatch['teamNumber'],
        ":deviceID" => $teamMatch['deviceID'],
        ":collectionStarted" => $teamMatch['collectionStarted'],
        ":collectionEnded" => $teamMatch['collectionEnded'],
      );
      $helper->queryDB($query,$params,true);

      $teamMatch['id'] = $helper->con->lastInsertId();
    }


    $query = "UPDATE teamMatches
              SET matchID = :matchID,
              side = :side,
              position = :position,
              teamNumber = :teamNumber,
              deviceID = :deviceID,
              collectionEnded = :collectionEnded,
              collectionStarted = :collectionStarted
              WHERE id = :id";

    $params = array(
      ":matchID" => $teamMatch['matchID'],
      ":side" => $teamMatch['side'],
      ":position" => $teamMatch['position'],
      ":teamNumber" => $teamMatch['teamNumber'],
      ":deviceID" => $teamMatch['deviceID'],
      ":collectionStarted" => $teamMatch['collectionStarted'],
      ":collectionEnded" => $teamMatch['collectionEnded'],
      ":id" => $teamMatch['id']
    );
    $helper->queryDB($query,$params,true);

    foreach($teamMatch['breaches'] as $breach){
      $query = "INSERT INTO matchbreaches(teamMatchID, orderID, `mode`, startZone, defenseID, endZone, fail) VALUES(:teamMatchID, :orderID, :mode, :startZone, :defenseID, :endZone, :fail)";
      $params = array(":teamMatchID" => $teamMatch['id'],
                      ":orderID" => $breach['orderID'],
                      ":mode" => $breach['mode'],
                      ":startZone" => $breach['startZone'],
                      ":defenseID" => $breach['defenseID'],
                      ":endZone" => $breach['endZone'],
                      ":fail" => $breach['fail']);
      $helper->queryDB($query,$params,false);
    }
    foreach($teamMatch['feeds'] as $feed){
      $query = "INSERT INTO matchfeeds(teamMatchID, orderID, `mode`, zoneID) VALUES (:teamMatchID, :orderID, :mode, :zoneID)";
      $params = array(":teamMatchID" => $teamMatch['id'],
                      ":orderID" => $feed['orderID'],
                      ":mode" => $feed['mode'],
                      ":zoneID" => $feed['zoneID']);
      $helper->queryDB($query,$params,false);

    }
    foreach($teamMatch['shoots'] as $shoot){

      $query = "INSERT INTO matchshoots(teamMatchID, orderID, `mode`,coordX , coordY, highLow, scoreMiss) VALUES (:teamMatchID, :orderID, :mode, :coordX, :coordY, :highLow, :scoreMiss)";
      $params = array(":teamMatchID" => $teamMatch['id'],
        ":orderID" => $shoot['orderID'],
        ":mode" => $shoot['mode'],
        ":coordX" => $shoot['coordX'],
        ":coordY" => $shoot['coordY'],
        ":highLow" => $shoot['highLow'],
        ":scoreMiss" => $shoot['scoreMiss'],
      );
      $helper->queryDB($query,$params,false);


    }
    foreach($teamMatch['climbs'] as $climb){
      $query = "INSERT INTO matchclimbs(teamMatchID, `mode`, batterReached, duration, offensiveRating, defensiveRating,success)
                    VALUES(:teamMatchID, :mode, :batterReached, :duration, :offensiveRating, :defensiveRating,:success)";

      $params = array(":teamMatchID" => $teamMatch['id'],
      ":mode" => $climb['mode'],
      ":batterReached" => $climb['batterReached'],
      ":duration" => $climb['duration'],
      ":offensiveRating" => $climb['offensiveRating'],
      ":defensiveRating" => $climb['defensiveRating'],
      ":success" => $climb['success']);
      $helper->queryDB($query,$params,false);

    }
  }
}

$helper->con = null;
$helper->autoCycleDBConnection = true;
echo "Success";

?>