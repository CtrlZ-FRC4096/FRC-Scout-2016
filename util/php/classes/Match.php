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
  public $id;
  public $red1;
  public $red2;
  public $red3;
  public $blue1;
  public $blue2;
  public $blue3;
  public $claimedTeams = array();
  public $defenses;

  public $helper;

  public function __construct($id,$compID){

    $this->helper = new Helper();
    $this->defenses = array();


    if(!is_null($id)){
      $this->id = $id;
      $this->compID = $compID;
      $this->getInfo();
    }


}

  public function getInfo(){
    $query = "SELECT * FROM matches WHERE id = :id AND compID = :compID";
    $params = array(":id" => $this->id,":compID" => $this->compID);
    $result = $this->helper->queryDB($query,$params,false);
    $this->compID = $result[0]['compID'];
    $this->red1 = $result[0]['red1'];
    $this->red2 = $result[0]['red2'];
    $this->red3 = $result[0]['red3'];
    $this->blue1 = $result[0]['blue1'];
    $this->blue2 = $result[0]['blue2'];
    $this->blue3 = $result[0]['blue3'];
    $this->getDefenses();
    $this->claimedTeams = $this->getClaimedTeams();
  }

  public function getDefenses(){
    $query = "SELECT mD.side, mD.slot, d.id, d.name, d.category, d.img
              FROM  `matchDefenses` mD
              JOIN defenses d ON mD.defenseID = d.id
              WHERE mD.compId = :compID
              AND mD.matchID = :matchID ";

    $params = array(":compID" => $this->compID, ":matchID" => $this->id);
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
      $defense->compID = $this->compID;

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
    $query = "DELETE FROM matchDefenses WHERE matchID = :matchID AND compID = :compID";
    $params = array(
      ":matchID" => $this->id,
      ":compID" => $this->compID
    );
    $result = $this->helper->queryDB($query,$params,true);
    return $result;
  }

  public function update(){
    $query = "UPDATE matches
              SET red1 = :red1,
                  red2 = :red2,
                  red3 = :red3,
                  blue1 = :blue1,
                  blue2 = :blue2,
                  blue3 = :blue3
              WHERE id = :matchID AND compID = :compID";
    $params = array(
      ":red1" => $this->red1,
      ":red2" => $this->red2,
      ":red3" => $this->red3,
      ":blue1" => $this->blue1,
      ":blue2" => $this->blue2,
      ":blue3" => $this->blue3,
      ":matchID" => $this->id,
      ":compID" => $this->compID
    );

    $result = $this->helper->queryDB($query,$params,true);
    return $result;
  }

  public function getClaimedTeams(){
    $query = "SELECT teamNumber
              FROM teamReservations
              WHERE matchID = :matchID
                 AND compID = :compID";
    $params = array(":matchID" => $this->id, ":compID" => $this->compID);
    $result = $this->helper->queryDB($query,$params, false);
    $arr = array();
    foreach($result as $row){
      array_push($arr, $row['teamNumber']);
    }
    return $arr;
  }

  public function isTeamClaimed($teamNumber){
    $query = "SELECT teamNumber
              FROM teamReservations
              WHERE matchID = :matchID
                 AND compID = :compID
                 AND teamNumber = :team";

    $params = array(":matchID" => $this->id, ":compID" => $this->compID,":team" => $teamNumber);
    $result = $this->helper->queryDB($query,$params, false);

    if(count($result) > 0){
      return true;
    }
    else{
      return false;
    }

  }
  public function claimTeam($teamNumber,$deviceID){
    $query = "INSERT INTO teamReservations(compID, matchID, teamNumber,deviceID)
                                   VALUES(:compID,:matchID,:teamNumber,:deviceID)";
    $params = array(":matchID" => $this->id, ":compID" => $this->compID,":teamNumber" => $teamNumber, ":deviceID" => $deviceID);
    $result = $this->helper->queryDB($query,$params, true);
    return $result;
  }

  public function isConfigured(){
    $query = "SELECT * FROM matchDefenses WHERE compID = :compID AND matchID = :matchID";
    $params = array(":matchID" => $this->id, ":compID" => $this->compID,);
    $result = $this->helper->queryDB($query,$params, false);

//    var_dump($result);

    if(count($result) >= 8){
      return true;
    }
    else{
      return false;
    }


  }

  function deleteData($teamNumber){
    $this->helper->con = $this->helper->connectToDB();
    $this->helper->con->beginTransaction();

    $types = array("feed" => "feeds","breach"=>"breaches","shoot" => "shoots", "climb" => "climbs");
    $params = array(":matchID" => $this->id, ":compID" => $this->compID, ":teamNumber" => $teamNumber);

    foreach($types as $eventType => $tableName){

      $tableName = ucwords($tableName);

      $query = "DELETE
              FROM $tableName
              WHERE id IN(
                  SELECT actionID
                  FROM match$tableName
                  WHERE matchID = :matchID
                  AND compID = :compID
                  AND teamNumber = :teamNumber);
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

      $query = "DELETE
                  FROM match$tableName
                  WHERE matchID = :matchID
                  AND compID = :compID
                  AND teamNumber = :teamNumber;
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


    $query = "DELETE FROM teamReservations WHERE matchID = :matchID AND compID = :compID AND teamNumber = :teamNumber";


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

  public function getClaimedTeamForDevice($deviceID){
    $query = "SELECT teamNumber FROM teamReservations WHERE compID = :compID AND matchID = :matchID AND deviceID = :deviceID";
    $params = array(":compID" => $this->compID,
                    ":matchID" => $this->id,
                    ":deviceID" => $deviceID);

    $result = $this->helper->queryDB($query,$params, false);
    if(count($result) <= 0){
      return false;
    }
    else{
      return $result[0]['teamNumber'];
    }



  }

  public function getNumberOfTeamsScouted(){
    $query = "
SELECT DISTINCT teamNumber FROM
(
  SELECT DISTINCT teamNumber FROM matchFeeds WHERE matchID = :matchID AND compID = :compID UNION
  SELECT DISTINCT teamNumber FROM matchShoots WHERE matchID = :matchID AND compID = :compID UNION
  SELECT DISTINCT teamNumber FROM matchBreaches WHERE matchID = :matchID AND compID = :compID UNION
  SELECT DISTINCT teamNumber FROM matchClimbs WHERE matchID = :matchID AND compID = :compID
  ) as a
";
    $params = array(":matchID" => $this->id, ":compID" => $this->compID);
    $result = $this->helper->queryDB($query,$params,false);
//    var_dump($result);
    return count($result);

  }



}


?>