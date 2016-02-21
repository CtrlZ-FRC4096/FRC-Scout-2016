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
      $match->compID = $row['compID'];
      $match->red1 = $row['red1'];
      $match->red2 = $row['red2'];
      $match->red3 = $row['red3'];
      $match->blue1 = $row['blue1'];
      $match->blue2 = $row['blue2'];
      $match->blue3 = $row['blue3'];
      $match->getDefenses();
      array_push($this->matches,$match);
    }

  }

  public function getLastMatchWithData(){

    $query = "SELECT MAX(matchID) as matchID FROM
          (
            SELECT MAX(matchID) as matchID FROM matchFeeds WHERE compID = :compID UNION
            SELECT MAX(matchID) as matchID FROM matchBreaches WHERE compID = :compID1 UNION
            SELECT MAX(matchID) as matchID FROM matchShoots WHERE compID = :compID2 UNION
            SELECT MAX(matchID) as matchID FROM matchClimbs WHERE compID = :compID3) as a";

    $params = array(
      ":compID" => $this->id,
      ":compID1" => $this->id,
      ":compID2" => $this->id,
      ":compID3" => $this->id
    );
    $result = $this->helper->queryDB($query,$params,false);
//    var_dump($result);
    if(intval($result[0]['matchID']) > 0){
      return new Match($result[0]['matchID'],$this->id);

    }
    else{
      $match = new Match(null,null);
      $match->id = 0;
      return $match;
    }


}

  public function getLastExportedMatchID(){
    $query = "SELECT MAX(id) as matchID FROM matches WHERE compID = :compID";
    $params = array(":compID" => $this->id);
    $result = $this->helper->queryDB($query,$params,false);
    if(count($result) == 0){
      return 0;

    }
    else{
      return $result[0]["matchID"];
    }
  }



} 