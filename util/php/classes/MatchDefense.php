<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/2/2016
 * Time: 9:55 PM
 */

class MatchDefense {


  public $id;
  public $name;
  public $category;
  public $img;
  public $compID;
  public $matchID;
  public $side;
  public $slot;

  public $helper;

  public function __construct(){
    $this->helper = new Helper();
  }

  public function insert(){
    $query = "INSERT INTO matchDefenses( compID, matchID, side, slot, defenseID)
                                VALUES (:compID,:matchID,:side,:slot,:defenseID)";
    $params = array(
      ":compID" => $this->compID,
      ":matchID" => $this->matchID,
      ":side" => $this->side,
      ":slot" => $this->slot,
      ":defenseID" => $this->id
    );

    $result = $this->helper->queryDB($query,$params,true);
    return $result;

  }


} 