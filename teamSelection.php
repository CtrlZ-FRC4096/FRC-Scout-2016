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
$CURR_MATCH_NUM = $currCompetition->getLastMatchWithData()->id +1;
$CURR_MATCH;

?>


<!DOCTYPE HTML>
<html>
<head>

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/client_includes.php"); ?>
</head>
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
        <select id="matchSwitcher">
          <?php
          foreach($currCompetition->matches as $match){
            $num = $match->id;

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
      <h2><select
          size="6" id="teamSelection">
          <?php

          echo  "<option value='" . $CURR_MATCH->red1 . "'>" . $CURR_MATCH->red1 . "</option>";
          echo  "<option value='" . $CURR_MATCH->red2 . "'>" . $CURR_MATCH->red2 . "</option>";
          echo  "<option value='" . $CURR_MATCH->red3 . "'>" . $CURR_MATCH->red3 . "</option>";
          echo "<option value='" . $CURR_MATCH->blue1 . "'>" . $CURR_MATCH->blue1 . "</option>";
          echo "<option value='" . $CURR_MATCH->blue2 . "'>" . $CURR_MATCH->blue2 . "</option>";
          echo "<option value='" . $CURR_MATCH->blue3 . "'>" . $CURR_MATCH->blue3 . "</option>";

          ?>
        </select></h2>
      <a id="startMatch" class="button button-pill button-flat-primary button-large">Begin</a>

    </div>

  </div>

</div>

<div id="waitingContent" style="display: none">

  <h2 style="text-align: center;">Starting Match <span id="startingMatchNumber">1</span> for <?=$currCompetition->name?></h2>
  <h2 style="text-align: center;">Waiting for configuration to complete...</h2>
  <h2 style="text-align: center;">Last Checked : <span id="lastChecked"></span></h2>

  <a style="margin: 0 auto; display: table;" id="cancelMatch" class="button button-pill button-flat-caution button-large">Cancel</a>



</div>

</body>

<script>
  $(document).ready(function(){

    var CHECK_FOR_MATCH_CONFIG = false;
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

    foreach($CURR_MATCH->getClaimedTeams() as $team){
    echo '$("#teamSelection option[value=\'' . $team . ']\'").attr("disabled","");';
    }

    ?>

    (function getClaimedTeams() {
      $.ajax({
        type: "POST",
        data: {matchID:$("#matchSwitcher").val(), compID:<?=$currCompetition->id?>},
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


    (function checkForMatchConfigCompleted() {

      if(CHECK_FOR_MATCH_CONFIG){
        $.ajax({
          type: "POST",
          data: {matchID:$("#matchSwitcher").val(), compID:<?=$currCompetition->id?>},
          url: "/util/php/serve/checkMatchConfigCompleted.php",
          success: function (data) {
            if(data == "done"){
              location.reload();
            }
            $("#lastChecked").text(new Date().toLocaleString())

          }
        });
      }
      setTimeout(checkForMatchConfigCompleted, 1000);
    })();


    $("#startMatch").click(function(){
      if($(this).hasClass("disabled")){
        return;
      }
      $.ajax({
        type: "POST",
        data: {matchID:$("#matchSwitcher").val(), compID: <?=$currCompetition->id?>, teamNumber:$("#teamSelection").val()},
        url: "/util/php/serve/claimTeam.php",
        success: function (data) {
          if(data=="success"){
            $("#startMatch").addClass("disabled");
            $("#waitingContent").show();
            CHECK_FOR_MATCH_CONFIG = true;
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
        data: {matchID: ind, compID: <?=$currCompetition->id?>, records: false},
        async: false,
        success: function (data) {
          data = JSON.parse(data);
          var html = "";
          html += "<option value='" + data.red1 + "'>" + data.red1 + "</option>";
          html += "<option value='" + data.red2 + "'>" + data.red2 + "</option>";
          html += "<option value='" + data.red3 + "'>" + data.red3 + "</option>";
          html += "<option value='" + data.blue1 + "'>" + data.blue1 + "</option>";
          html += "<option value='" + data.blue2 + "'>" + data.blue2 + "</option>";
          html += "<option value='" + data.blue3 + "'>" + data.blue3 + "</option>";
          $("#teamSelection").html(html);

        }
      });
    })


    $("#cancelMatch").click(function(){

      $.ajax({
        type: "POST",
        data: {matchID:$("#matchSwitcher").val(), compID: <?=$currCompetition->id?>,teamNumber: SELECTED_TEAM},
        url: "/util/php/serve/cancelMatch.php",
        success: function (data) {
          if(data=="success"){
            $("#startMatch").removeClass("disabled");
            $("#waitingContent").hide();
            CHECK_FOR_MATCH_CONFIG = false;
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