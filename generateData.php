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
  $numbers = range(1, 9);
  shuffle($numbers);
  $red = array_slice($numbers,0,5);
  shuffle($numbers);
  $blue = array_slice($numbers,0,5);
  $defenses = array_merge($red,$blue);
  $query = "INSERT IGNORE INTO matchdefenses(matchID,side,slot,defenseID) VALUES(:matchID,:side,:slot,:defenseID)";
  foreach($slots as $ind => $slot){

    $params = array(
      ":matchID" => $match['id'],
      ":side" => $slot[0],
      ":slot" => $slot[1],
      ":defenseID" => $defenses[$ind]
    );
    $helper->queryDB($query,$params,true);


  }

  $query = "SELECT * FROM teammatches WHERE matchID = :matchID;";
  $params = array(":matchID" => $match['id']);
  $result2 = $helper->queryDB($query,$params,false);
  $teleAuto = ['tele','auto'];

  $names = $helper->getScouters();


  foreach($result2 as $teamMatch){

  $name = array_rand($names);

  $query = "UPDATE teamMatches SET scouterName = :scouterName WHERE id = :id";
  $params = array(":scouterName" => $names[$name], ":id" => $teamMatch['id']);
  $result = $helper->queryDB($query,$params,true);


    $teamMatchID = $teamMatch['id'];
    for($i = 0;$i<=rand(1,12);$i++){
      $query = "INSERT INTO matchFeeds(teamMatchID,orderID,`mode`,zoneID)
                                    VALUES(:teamMatchID,:orderID,:mode,:zone)";
      $params = array(
        ":teamMatchID" => $teamMatchID,
        ":orderID" => 1,
        ":mode" => $teleAuto[array_rand($teleAuto)],
        ":zone" => array_rand([1,2,3,4,5,6,7]) + 1
      );
      var_dump($params);
      $helper->queryDB($query,$params,false);
    }

    for($i = 0;$i<=rand(1,12);$i++){
      $query = "INSERT INTO matchBreaches(teamMatchID,orderID,`mode`,startZone,defenseID,endZone,fail)
                                    VALUES(:teamMatchID,:orderID,:mode,
                                    :startZone
                                    ,:defenseID,
                                    :endZone,:fail)";
      $params = array(
        ":teamMatchID" => $teamMatchID,
        ":orderID" => 1,
        ":mode" => $teleAuto[array_rand($teleAuto)],
        ":startZone" => rand(1,7),
        ":defenseID" => $defenses[array_rand($defenses)],
        ":endZone" => rand(1,7),
        ":fail" => rand(0,1)
      );

      var_dump($params);
      $helper->queryDB($query,$params,false);
    }

    for($i = 0;$i<=rand(1,12);$i++){
      $query = "INSERT INTO matchShoots(teamMatchID,orderID,`mode`,coordX,coordY,highLow,scoreMiss)
                                    VALUES(:teamMatchID,:orderID,:mode,:coordX,:coordY,:highLow,:scoreMiss)";
      $params = array(
        ":teamMatchID" => $teamMatchID,
        ":orderID" => 1,
        ":mode" => $teleAuto[array_rand($teleAuto)],
        ":coordX" => rand(0,$helper::HALF_FIELD_LENGTH_INCHES),
        ":coordY" => rand(0,$helper::HALF_FIELD_HEIGHT_INCHES),
        ":highLow" => rand(0,1),
        ":scoreMiss" => rand(0,1)
      );
      $helper->queryDB($query,$params,false);
    }

    $query = "INSERT INTO matchClimbs(teamMatchID, `mode`,batterReached,duration,defensiveRating,offensiveRating,success)
                                    VALUES(:teamMatchID,:mode,:batterReached,:duration,:defensiveRating,:offensiveRating,:success)";
    $params = array(
      ":teamMatchID" => $teamMatchID,
      ":mode" => "tele",
      ":batterReached" => rand(0,1),
      ":duration" => rand(0,20),
      ":defensiveRating" => rand(1,10),
      ":offensiveRating" => rand(1,10),
      ":success" => rand(0,1)
    );

    $helper->queryDB($query,$params,false);

  }
  echo "done";

}



