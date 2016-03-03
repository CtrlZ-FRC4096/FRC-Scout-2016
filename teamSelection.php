<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 1/27/2016
 * Time: 8:51 PM
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);

$currCompetition = $helper->getCurrentCompetition();

if($WAITING_FOR_CONFIG){
  $CURR_MATCH_NUM = $match->matchNumber;
  $CURR_MATCH = $match;
}
else{
  $CURR_MATCH_NUM = $currCompetition->getLastMatchWithData()->matchNumber;
  if($currCompetition->getLastMatchID() != $CURR_MATCH_NUM){
    $CURR_MATCH_NUM +=1;
  }
$CURR_MATCH = null;
}

?>


<!DOCTYPE HTML>
<html>
<head>

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/client_includes.php"); ?>
</head>
<div id="redirectingNotice" style="
      position: absolute;
      width: 100%;
      height: 100%;
      background-color: white;
      display: none;
      align-items: center;
      z-index: 15;">

  <h2 style="width: 100%;text-align: center;font-size: 50px">Redirecting...</h2>
</div>
<body style="background-image: '/util/img/scouting-home-bg.jpg';">
<div style="width: 100%;overflow: hidden">
  <h1 style="text-align: center;">Ctrl-Z 4096 Scouting 2016</h1>
  <h2 style="text-align: center;"><?=$currCompetition->name?></h2>
</div>
<hr>

<div class="row center-sm" style="width: 100%;">
  <div class="col-sm-2">
    <div style=" margin-top: 3%;">
      <h3 style="text-align: center;">
        Match
        <select id="matchSwitcher" <?=($WAITING_FOR_CONFIG ? "disabled='disabled'" : "")?>>
          <?php
          foreach($currCompetition->matches as $match){

            $num = $match->matchNumber;

            $selected = "";
            if($num == $CURR_MATCH_NUM){
              $selected = "selected";
              $CURR_MATCH = $match;
            }
            else{
              $selected = "";
            }

            echo "<option $selected value='$num'>$num</option>";
          }
          ?>

        </select>
      </h3>

      <hr>
      <h2>
        <select <?=($WAITING_FOR_CONFIG ? "disabled='disabled'" : "")?>
          size="6" id="teamSelection">
          <?php
            $indexes = array("red1","red2","red3","blue1","blue2","blue3");

            foreach($indexes as $index){
              $selected = "";
                if($WAITING_FOR_CONFIG && $CURR_MATCH->teams[$index] == $WAITING_ON_TEAM){
                  $selected = "selected";
                }
              echo  "<option $disabled $selected value='". $CURR_MATCH->teams[$index] . "'>" .
                $CURR_MATCH->teams[$index] . "</option>";

            }

          ?>
        </select></h2>
      <a <?=($WAITING_FOR_CONFIG ? "disabled='disabled'" : "")?>
        id="startMatch"
        class="button button-pill button-flat-primary button-large">Begin</a>

    </div>

  </div>

</div>

<div id="waitingContent" style="display: none">

  <h2 style="text-align: center;">Starting Match <span id="startingMatchNumber">1</span> for <?=$currCompetition->name?></h2>
  <h2 style="text-align: center;">Waiting for configuration to complete...</h2>
  <h2 style="text-align: center;">Last Checked : <span id="lastChecked"></span></h2>

  <a style="margin: 0 auto; display: table;" id="cancelMatch" class="button button-pill button-flat-caution button-large">Cancel</a>



</div>
<h2 style="text-align: center;display:none" id="redirecting">Redirecting...</h2>

</body>

<script>
  $(document).ready(function(){

    var SELECTED_TEAM = 0;

    if (typeof $.cookie('deviceID') === 'undefined'){
      <?php
      $randomID = $helper->getRandomDeviceID();
      ?>

      $.cookie("deviceID","<?=$randomID?>",{ expires: 3650, path: '/' });
      $.ajax({
        type: "POST",
        url: "/util/php/serve/addDevice.php",
        data: {deviceID : "<?=$randomID?>"},
        async: false,
        success: function (data) {}



      });
    }


    <?php

    if($WAITING_FOR_CONFIG){
    echo <<<EOD

    $("#waitingContent").show();
    checkForMatchConfigCompleted();
    SELECTED_TEAM = $WAITING_ON_TEAM

EOD;

    }

    foreach($CURR_MATCH->getClaimedTeams() as $team){
    echo '$("#teamSelection option[value=\'' . $team . ']\'").attr("disabled","");';
    }

    ?>

    (function getClaimedTeams() {
      $.ajax({
        type: "POST",
        data: {matchNumber:$("#matchSwitcher").val(), compID:<?=$currCompetition->id?>},
        url: "/util/php/serve/getClaimedTeams.php",
        success: function (data) {
          data = JSON.parse(data);
          $("#teamSelection option").removeAttr("disabled");
          $.each(data,function(index,e){
            $("#teamSelection option[value='" + e + "']").attr("disabled","");
          })

        }
      });
      setTimeout(getClaimedTeams, 500);
    })();

      var checkMatchInterval;


    function checkForMatchConfigCompleted() {

      checkMatchInterval = setInterval(function(){
          $.ajax({
            type: "POST",
            data: {matchNumber:$("#matchSwitcher").val(), compID:<?=$currCompetition->id?>},
            url: "/util/php/serve/checkMatchConfigCompleted.php",
            success: function (data) {
              if(data == "done"){
                clearInterval(checkMatchInterval)
                $("#redirecting").show();
                $("#redirectingNotice").css("display","flex");
                location.reload();
              }
              $("#lastChecked").text(new Date().toLocaleString())

            }
          });
      },1000)

    }


    $("#startMatch").click(function(){
      if($(this).hasClass("disabled")){
        return;
      }

      if($("#teamSelection").val() == null){
        toastr["error"]("You gotta select a team!", "You forgot something...")
        return;
      }

      $.ajax({
        type: "POST",
        data: {matchNumber:$("#matchSwitcher").val(), compID: <?=$currCompetition->id?>, teamNumber:$("#teamSelection").val()},
        url: "/util/php/serve/claimTeam.php",
        success: function (data) {
          if(data=="success"){
            $("#startMatch").addClass("disabled");
            $("#waitingContent").show();
            checkForMatchConfigCompleted();
            SELECTED_TEAM = $("#teamSelection").val();
            $("#matchSwitcher").attr("disabled","");
            $("#teamSelection").attr("disabled","");
          }
          else{
            toastr["error"]("This team has already been claimed.", "Aw Shucks!")
          }

        }
      });
    });


    $("#matchSwitcher").change(function(){
      $("#teamSelection").html("");

      var ind = $(this).val();

      $.ajax({
        type: "POST",
        url: "/util/php/serve/getMatch.php",
        data: {matchNumber: ind, compID: <?=$currCompetition->id?>, records: false},
        async: false,
        success: function (data) {
          data = JSON.parse(data);
          var html = "";
          html += "<option value='" + data.teams['red1'] + "'>" + data.teams['red1'] + "</option>";
          html += "<option value='" + data.teams['red2'] + "'>" + data.teams['red2'] + "</option>";
          html += "<option value='" + data.teams['red3'] + "'>" + data.teams['red3'] + "</option>";
          html += "<option value='" + data.teams['blue1'] + "'>" + data.teams['blue1']+ "</option>";
          html += "<option value='" + data.teams['blue2'] + "'>" + data.teams['blue2']+ "</option>";
          html += "<option value='" + data.teams['blue3'] + "'>" + data.teams['blue3']+ "</option>";
          $("#teamSelection").html(html);

        }
      });
    })


    $("#cancelMatch").click(function(){

      $.ajax({
        type: "POST",
        data: {matchNumber:$("#matchSwitcher").val(), compID: <?=$currCompetition->id?>,teamNumber: SELECTED_TEAM},
        url: "/util/php/serve/cancelMatch.php",
        success: function (data) {
          if(data=="success"){
            $("#startMatch").removeClass("disabled");
            $("#waitingContent").hide();
            clearInterval(checkMatchInterval);
            $("#matchSwitcher").removeAttr("disabled");
            $("#teamSelection").removeAttr("disabled");
          }
          else{
            toastr["error"]("Could not cancel the match!", "Aw Shucks!");
            console.log(data);
          }

        }
      });


    })



  });



</script>


</html>