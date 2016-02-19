<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 1/31/2016
 * Time: 5:51 PM
 */

class Helper {

  public $con;

  public $LEFT_TEAM = "blue";
  public $RIGHT_TEAM = "red";
  const HALF_FIELD_LENGTH_INCHES = 325.11;
  const HALF_FIELD_HEIGHT_INCHES = 319.72;



  public function connectToDB()
  {
    $host = 'illiniroboticsorg.netfirmsmysql.com';
    $username = 'scout';
    $password = 'scoutingisfun';
    $dbname = 'scouting2016';
    try {
      $mycon = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
      $mycon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $mycon->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      return $mycon;
    } catch (PDOException $e) {
      return "Failed to connect to MySQL: " . $e->getMessage();
    }
  }

  public function queryDB($query, $params, $update)
  {
    $this->con = $this->connectToDB();

    $stmt = $this->con->prepare($query);
    if ($params !== null) {
      foreach ($params as $key => $param) {
        $stmt->bindValue($key, trim($param));
      }
    }
    try {
      $stmt->execute();
    } catch (PDOException $e) {
      // echo $e->getMessage();
      echo $e->getMessage();
      return $e;
    }

    if (!$update) {
      $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $this->con = null;
      return $r;
    } else {
      $this->con = null;

      return true;
    }
  }

  public function getDefensesByCategory(){

    $query = "SELECT * FROM defenses WHERE category = :cat";
    $params = array(":cat" => "A");
    $ADefenses = $this->queryDB($query,$params,false);
    $params = array(":cat" => "B");
    $BDefenses = $this->queryDB($query,$params,false);
    $params = array(":cat" => "C");
    $CDefenses = $this->queryDB($query,$params,false);
    $params = array(":cat" => "D");
    $DDefenses = $this->queryDB($query,$params,false);

    $arr = array(
      "A" => $ADefenses,
      "B" => $BDefenses,
      "C" => $CDefenses,
      "D" => $DDefenses);

    return $arr;

  }

  public function getCurrentCompetition(){

    $query = "SELECT id FROM competitions WHERE current = 1 LIMIT 0,1";
    $params = null;
    $result = $this->queryDB($query,$params,false);
    return new Competition(intval($result[0]['id']));


  }

  public function getTeamNumbersForCompetition($compID){

    $query = "SELECT teamNumber FROM teamCompetitions WHERE compID = :compID";
    $params = array(":compID" => $compID);
    $result = $this->queryDB($query,$params,false);
    $arr = array();
    foreach($result as $row){
      array_push($arr,$row['teamNumber']);
    }

    return $arr;
  }



  function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  function getRandomDeviceID(){
    $id = $this->generateRandomString();
    $query = "SELECT COUNT(*) as amt FROM devices WHERE deviceID = :id";
    $params = array(":id" => $id);
    $result = $this->queryDB($query,$params,false);
    if(intval($result[0]['amt']) > 0){
      return $this->getRandomDeviceID();
    }
    else{
      return $id;
    }
  }


  public function addDevice($deviceID){
    $query = "INSERT INTO devices(deviceID)
                                   VALUES(:id)";
    $params = array(":id" => $deviceID);
    $result = $this->queryDB($query,$params, true);
    return $result;
  }


  public function getCurrentStatusOfUser($deviceID, $compID){
    $query = "SELECT * FROM teamReservations WHERE deviceID = :deviceID AND collectionEnded = 0 AND compID = :compID";
    $params = array(":deviceID" => $deviceID,":compID" => $compID);
    $result = $this->queryDB($query,$params,false);

    if(count($result) == 0){
      return "teamSelection";
    }
    else{
      return "startMatch-" . $result[0]['matchID'];
    }

  }





} 