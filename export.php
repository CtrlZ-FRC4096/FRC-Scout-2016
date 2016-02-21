<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/19/2016
 * Time: 10:00 PM
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);
include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();
$currCompetition = $helper->getCurrentCompetition();
$CURR_MATCH_NUM = $currCompetition->getLastExportedMatchID() +1;
?>




<!DOCTYPE HTML>
<html>
<head>
  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/client_includes.php"); ?>


</head>
<body style="background-image: '/util/img/scouting-home-bg.jpg';">
<div style="width: 100%;overflow: hidden">
  <h1 style="text-align: center;">Export Data</h1>
  <h2 style="text-align: center;"><?=$currCompetition->name?></h2>
</div>
<hr>

<div class="row center-sm" style="width: 100%;">
  <div class="col-sm-2">
    <div style=" margin-top: 3%;">
      <h3 style="text-align: center;">
        Match
        <select id="matchSwitcher">

          <?php
          $CURR_MATCH_SCOUTED = 0;
          foreach($currCompetition->matches as $match){
            $num = $match->id;
            $teamsScouted  = $match->getNumberOfTeamsScouted();
            $selected = "";
            if($num == $CURR_MATCH_NUM){
              $selected = "selected";
              $CURR_MATCH = $match;
              $CURR_MATCH_SCOUTED = $teamsScouted;
            }
            else{
              $selected = "";
            }

            echo "<option id='teamsScouted' data-numScouted='$CURR_MATCH_SCOUTED' $selected value='$num'>$num</option>";
          }
          ?>


        </select>
      </h3>
      <h3><?=$CURR_MATCH_SCOUTED?> teams scouted</h3>

      <hr>
</div>
    </div>
  </div>
<script>

  $("#matchSwitcher").change(function(){


    $("#teamsScouted").text($(this).attr("data-numScouted"));


  })



</script>
</body>
</html>