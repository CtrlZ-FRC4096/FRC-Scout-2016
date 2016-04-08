<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/2/2016
 * Time: 11:50 PM
 */
include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$data = json_decode($_POST['data']);

$matchNumber = $data->matchNumber;
$match = new Match($matchNumber,$data->compID);

$match->teams['red1'] = $data->red1;
$match->teams['red2'] = $data->red2;
$match->teams['red3'] = $data->red3;
$match->teams['blue1']  = $data->blue1;
$match->teams['blue2']  = $data->blue2;
$match->teams['blue3']  = $data->blue3;


//var_dump($_POST);
$match->updateTeams();


$match->deleteAllDefenses();

foreach($data->data as $defense){
  $matchDefense = new MatchDefense();

  $matchDefense->matchID = $match->id;
  $matchDefense->side = $defense->side;
  $matchDefense->slot = $defense->slot;
  $matchDefense->id = $defense->defenseID;

  $matchDefense->insert();

}

$matchDefense = new MatchDefense();

$matchDefense->matchID = $match->id;
$matchDefense->side = "red";
$matchDefense->slot = 1;
$matchDefense->id = 9;

$matchDefense->insert();

$matchDefense->side = "blue";

$matchDefense->insert();



echo "Success";





?>