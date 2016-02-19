<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/2/2016
 * Time: 11:50 PM
 */
include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$data = json_decode($_POST['data']);

$matchID = $data->matchID;
$match = new Match($matchID,$_POST['compID']);

$match->red1 = $data->red1;
$match->red2 = $data->red2;
$match->red3 = $data->red3;
$match->blue1 = $data->blue1;
$match->blue2 = $data->blue2;
$match->blue3 = $data->blue3;
//var_dump($_POST);
$match->update();


$match->deleteAllDefenses();

$comp = $data->compID;

foreach($data->data as $defense){
  $matchDefense = new MatchDefense();

  $matchDefense->matchID = $matchID;
  $matchDefense->compID = $comp;
  $matchDefense->side = $defense->side;
  $matchDefense->slot = $defense->slot;
  $matchDefense->id = $defense->defenseID;

  $matchDefense->insert();

}

echo "Success";





?>