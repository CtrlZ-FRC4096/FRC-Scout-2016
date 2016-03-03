<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/2/2016
 * Time: 6:12 PM
 */

class Competition {

  public $id;
  public $name;

  public $helper;

  public $matches;


  public function __construct($id){


    $this->helper = new Helper();
    $this->matches = array();
    $this->id = $id;


    if(!is_null($id)){
      $this->getInfo();

    }


}

  public function getInfo(){
    $query = "SELECT m. * , c.name
              FROM matches m
              JOIN competitions c ON m.compID = c.id
              WHERE c.id = :compID";
    $params = array(":compID" => $this->id);
    $result = $this->helper->queryDB($query,$params,false);

    $this->name = $result[0]['name'];

    foreach($result as $row){
      $match = new Match(null,null);
      $match->id = $row['id'];
      $match->matchNumber = $row['matchNumber'];
      $match->compID = $row['compID'];
      $match->getInfo();
      array_push($this->matches,$match);
    }

  }

  public function getLastMatchWithData(){

    $query = "  SELECT MAX(matches.matchNumber) as matchNumber
                FROM(
                    SELECT teamMatchID FROM matchBreaches UNION
                    SELECT teamMatchID FROM matchClimbs UNION
                    SELECT teamMatchID FROM matchFeeds UNION
                    SELECT teamMatchID FROM matchShoots
                ) as a
                JOIN teammatches ON teammatches.id = a.teamMatchID
                JOIN matches ON matches.id = teammatches.matchID
                WHERE matches.compID = :compID";

    $params = array(
      ":compID" => $this->id
    );
    $result = $this->helper->queryDB($query,$params,false);
//    var_dump($result);
    if(intval($result[0]['matchNumber']) > 0){
      return new Match($result[0]['matchNumber'],$this->id);

    }
    else{
      $match = new Match(null,null);
      $match->id = 0;
      return $match;
    }


}

  public function getLastMatchID(){
    $query = "SELECT MAX(matchNumber) as matchNumber FROM matches WHERE compID = :compID";
    $params = array(":compID" => $this->id);
    $result = $this->helper->queryDB($query,$params,false);
    return $result[0]['matchNumber'];
  }


  public function getLastExportedMatchNumber(){
    $query = "SELECT MAX(matchNumber) as matchNumber FROM matches WHERE compID = :compID AND exported = 1";
    $params = array(":compID" => $this->id);
    $result = $this->helper->queryDB($query,$params,false);
    if(count($result) == 0){
      return 0;

    }
    else{
      return $result[0]["matchNumber"];
    }
  }



} 