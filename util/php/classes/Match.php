<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/2/2016
 * Time: 6:08 PM
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);

class Match {

  public $compID;
  public $matchNumber;
  public $id;
  public $claimedTeams = array();
  public $defenses;
  public $teams = array();

  public $helper;

  public function __construct($matchNumber,$compID){

    $this->helper = new Helper();
    $this->defenses = array();



    if(!is_null($matchNumber)){
      $this->matchNumber = $matchNumber;
      $this->compID = $compID;

      $this->getInfo();
    }


}

  public function getInfo(){
    $query = "SELECT * FROM matches WHERE matchNumber = :matchNumber AND compID = :compID";
    $params = array(":matchNumber" => $this->matchNumber,":compID" => $this->compID);
    $result = $this->helper->queryDB($query,$params,false);
    $this->id = $result[0]['id'];
    $this->getDefenses();
    $this->claimedTeams = $this->getClaimedTeams();
    $this->teams = $this->getTeams();
  }

  public function getDefenses(){
    $query = "SELECT mD.side, mD.slot, d.id, d.name, d.category, d.img
              FROM  `matchDefenses` mD
              JOIN defenses d ON mD.defenseID = d.id
              WHERE mD.matchID = :id ";

    $params = array(":id" => $this->id);
    $result = $this->helper->queryDB($query,$params,false);

    foreach($result as $row){

      $defense = new MatchDefense();
      $defense->side = $row['side'];
      $defense->slot = $row['slot'];
      $defense->id = $row['id'];
      $defense->name = $row['name'];
      $defense->category = $row['category'];
      $defense->img = $row['img'];
      $defense->matchID = $this->id;

      array_push($this->defenses, $defense);

    }

  }

  public function getDefenseAt($side,$slot){

    foreach($this->defenses as $defense){

      if($defense->side == $side && $defense->slot == $slot){
        return $defense;
      }
    }
    return null;
  }

  public function deleteAllDefenses(){
    $query = "DELETE FROM matchDefenses WHERE matchID = :id";
    $params = array(
      ":id" => $this->id
    );
    $result = $this->helper->queryDB($query,$params,true);
    return $result;
  }

  public function getClaimedTeams(){
    $query = "SELECT teamNumber
              FROM teammatches
              WHERE matchID = :matchID
                 AND deviceID != ''";
    $params = array(":matchID" => $this->id);
    $result = $this->helper->queryDB($query,$params, false);
    $arr = array();
    foreach($result as $row){
      array_push($arr, $row['teamNumber']);
    }
    return $arr;
  }

  public function getTeams(){
    $query = "SELECT teamNumber,side,position
              FROM teammatches
              WHERE matchID = :matchID";
    $params = array(":matchID" => $this->id);
    $result = $this->helper->queryDB($query,$params, false);
    $arr = array();
    foreach($result as $row){
      $arr[$row['side'] . $row['position']] = $row['teamNumber'];
    }
    return $arr;
  }

  public function isTeamClaimed($teamNumber){
    $query = "SELECT teamNumber
              FROM teammatches
              WHERE matchID = :matchID
                 AND teamNumber = :team
                 AND deviceID != ''";

    $params = array(":matchID" => $this->id,":team" => $teamNumber);
    $result = $this->helper->queryDB($query,$params, false);

    if(count($result) > 0){
      return true;
    }
    else{
      return false;
    }

  }

  public function claimTeam($teamNumber,$deviceID,$scouterName){
    $query = "UPDATE teammatches SET deviceID = :deviceID, scouterName = :scouterName WHERE matchID = :id AND teamNumber = :teamNumber";
    $params = array(":id" => $this->id,":teamNumber" => $teamNumber, ":deviceID" => $deviceID,":scouterName" => $scouterName);
    $result = $this->helper->queryDB($query,$params, true);
    return $result;
  }

  public function isConfigured(){
    $query = "SELECT * FROM matchDefenses WHERE matchID = :matchID";
    $params = array(":matchID" => $this->id);
    $result1 = $this->helper->queryDB($query,$params, false);


    $query = "SELECT * FROM teammatches WHERE matchID = :matchID";
    $params = array(":matchID" => $this->id);
    $result2 = $this->helper->queryDB($query,$params, false);

    if(count($result1) >= 8 && count($result2) >= 6){
      return true;
    }
    else{
      return false;
    }


  }

  function deleteData($teamNumber){

    $query = "SELECT id FROM teamMatches WHERE matchID = :matchID AND teamNumber = :teamNumber";
    $params = array(":matchID" => $this->id, ":teamNumber" =>$teamNumber);
    $result = $this->helper->queryDB($query,$params,false);
    $teamMatchID = $result[0]['id'];


    $this->helper->con = $this->helper->connectToDB();
    $this->helper->con->beginTransaction();

    $types = array("feed" => "feeds","breach"=>"breaches","shoot" => "shoots", "climb" => "climbs");
    $params = array(":teamMatchID" => $teamMatchID);

    foreach($types as $eventType => $tableName){

      $query = "DELETE
                  FROM match$tableName
                  WHERE teamMatchID = :teamMatchID;
                  ";


      $stmt = $this->helper->con->prepare($query);
      if ($params !== null) {
        foreach ($params as $key => $param) {
          $stmt->bindValue($key, trim($param));
        }
      }
      try {
        $stmt->execute();
      } catch (PDOException $e) {
        $this->helper->con->rollBack();
        return false;
      }


    }


    $query = "UPDATE teamMatches
              SET deviceID = '',
                  collectionEnded = 0,
                  collectionStarted = 0,
                  scouterName = ''
              WHERE matchID = :matchID
              AND teamNumber = :teamNumber";
    $params = array(":matchID" => $this->id, ":teamNumber" =>$teamNumber);
    $stmt = $this->helper->con->prepare($query);
    if ($params !== null) {
      foreach ($params as $key => $param) {
        $stmt->bindValue($key, trim($param));
      }
    }
    try {
      $stmt->execute();
    } catch (PDOException $e) {
      $this->helper->con->rollBack();
      return false;
    }


    $this->helper->con->commit();
    return true;


  }

  public function getClaimedTeamMatchForDevice($deviceID){
    $query = "SELECT * FROM teamMatches WHERE matchID = :matchID AND deviceID = :deviceID";
    $params = array(":matchID" => $this->id,
                    ":deviceID" => $deviceID);

    $result = $this->helper->queryDB($query,$params, false);
    if(count($result) <= 0){
      return false;
    }
    else{
      return $result[0];
    }



  }

  public function getNumberOfTeamsScouted(){
    $query = "
      SELECT teamNumber FROM teamMatches

              JOIN (
                SELECT teamMatchID FROM matchBreaches UNION
                SELECT teamMatchID FROM matchClimbs UNION
                SELECT teamMatchID FROM matchFeeds UNION
                SELECT teamMatchID FROM matchShoots
              ) as a

              ON teamMatches.id = a.teamMatchID
              JOIN matches ON teamMatches.matchID = matches.id
              WHERE matches.id = :matchID
";
    $params = array(":matchID" => $this->id);
    $result = $this->helper->queryDB($query,$params,false);
//    var_dump($result);
    return count($result);

  }

  public function updateTeams(){

    foreach($this->teams as $key=>$teamNumber){
      $side = substr($key,0,-1);
      $position = substr($key,-1);
      if($teamNumber != "" & $teamNumber != null){



        $query = "INSERT INTO teammatches (matchID,side,position,teamNumber)
                                  VALUES (:matchID,:side,:position,:teamNumber)
                    ON DUPLICATE KEY UPDATE matchID = :matchID2,
                                            side = :side2,
                                            position = :position2,
                                            teamNumber = :teamNumber2";
        $params = array(
          "matchID" => $this->id,
          "matchID2" => $this->id,
          ":side" => $side,
          ":side2" => $side,
          ":position" => $position,
          ":position2" => $position,
          ":teamNumber" => $teamNumber,
          ":teamNumber2" => $teamNumber
        );

        $this->helper->queryDB($query,$params,true);
      }
      else{
        $query = "DELETE FROM teamMatches WHERE matchID = :matchID AND side = :side AND position = :position";
        $params = array(
          ":matchID" => $this->id,
          ":side" => $side,
          ":position" => $position
        );
        $this->helper->queryDB($query,$params,true);
      }


    }

    $this->setLastUpdated();


  }

  public function setLastUpdated(){

    $query = "UPDATE matches SET lastUpdated = :lastUpdated
              WHERE id = :matchID";

    $params = array(
      ":lastUpdated" => time(),
      ":matchID" => $this->id);

    $this->helper->queryDB($query,$params,true);
  }

  public function setLastExported(){

    $query = "UPDATE matches SET lastExported = :lastExported
              WHERE id = :matchID";

    $params = array(
      ":lastExported" => time(),
      ":matchID" => $this->id);

    $this->helper->queryDB($query,$params,true);
  }

}


?>