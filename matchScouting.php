<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/9/2016
 * Time: 8:47 PM
 */
if (isset($_COOKIE['matchData'])) {
  $RESUMING_MATCH = true;
} else {
  $RESUMING_MATCH = false;
}

$SCOUTING_TEAM = $match->getClaimedTeamForDevice($_COOKIE['deviceID']);
switch ($SCOUTING_TEAM) {
  case $match->blue1:
  case $match->blue2:
  case $match->blue3:
    $SCOUTING_TEAM_COLOR = "blue";
    break;
  case $match->red1:
  case $match->red2:
  case $match->red3:
    $SCOUTING_TEAM_COLOR = "red";
    break;
}
?>

<!DOCTYPE HTML>
<html>
<head>

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/client_includes.php"); ?>

  <style>
    #taskSwitcher h2, #taskSwitcher h1 {
      font-weight: bold;
      color: white;
      margin: 0;
      padding: 5%;
    }

    #taskSwitcher div {
      padding: 0;
    }

    .orange {
      background-color: #FF7F2A
    }

    .darkBlue {
      background-color: #2A7FFF

    }

    #taskSwitcher div::after {
      width: 0;
      height: 0;
      border-left: 20px solid transparent;
      border-right: 20px solid transparent;
      content: "";
      display: none;
    }

    #taskSwitcher div[data-color='darkBlue']::after {
      border-top: 20px solid #2A7FFF;
    }

    #taskSwitcher div[data-color='orange']::after {
      border-top: 20px solid #FF7F2A;
    }

    #taskSwitcher div.active::after {
      display: inline-block;
    }

    .historyItem {
      width: 100%;
      height: 50px;
      display: flex;
      border-bottom: 1px solid black;
      align-items: center;
      justify-content: center;
    }

    #breachPage .defense {
      position: relative;
      display: flex;
      flex-direction: column;
      padding: 0;
      width: 100%;
      flex: 1 1 20%;
      box-sizing: border-box;
      overflow: hidden;
      align-items: center;
    }

    #breachPage .imageContainer {
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      position: absolute;
      left: 0;
      top: 0;
      right: 0;
      bottom: 0;
      display: flex;
      flex-direction: row;
      width: 100%;
    }

    #breachPage .circleContainer {
      display: flex;
      flex-direction: row;
      width: 100%;
      flex: 1 1 20%;
      box-sizing: border-box;
      overflow: hidden;
      display: flex;
      align-items: center;
    }

    #breachPage .yellowSide .sideCircleContainer .circle.startBreachCircle {
      background-color: orange !important;
    }

    #breachPage .defenseCircleContainer {
      position: absolute;
      left: 0;
      top: 0;
      right: 0;
      bottom: 0
    }

    #breachPage .defenseCircleContainer .circle {
      display: none;
    }

    #breachPage .defenseCircleContainer .circle h4 {
      color: white;
    }

    #breachPage .sideCircleContainer {
      height: 10px;
    }

    #breachPage .circleContainer .circle {
      width: 90%;
      margin: 0 auto;
      display: none;
    }

    #breachPage .circleContainer .circle h4 {
      text-align: center;
    }

    .circle {
      border-radius: 50%;
    }

    .blurred {
      filter: blur(3px);
      -webkit-filter: blur(3px);
    }


  </style>

</head>

<body style="height: 100%;display: flex;flex-direction: column">


<div class="row">
  <div style="display: flex; align-items: center; justify-content: space-between;"
       class="col-sm-12 <?= $SCOUTING_TEAM_COLOR ?>">

    <h2 style="float: left;color: white;margin: 12px;margin-right: 24px; margin-left: 24px;">
      Team <?= $SCOUTING_TEAM ?></h2>

    <h2 style="float: right;color: white;margin: 12px;margin-right: 24px; margin-left: 24px;">Match <?= $match->id ?>
      - <?= $currCompetition->name ?></h2>

  </div>
</div>

<div class="row" style="text-align: center;" id="taskSwitcher">
  <div class="col-sm-3 active" data-color="orange" div-id="feedPage">
    <h1 class="orange">Feed</h1>
  </div>
  <div class="col-sm-3" data-color="darkBlue" div-id="breachPage">
    <h1 class="darkBlue">Breach</h1>
  </div>
  <div class="col-sm-3" data-color="orange" div-id="shootPage">
    <h1 class="orange">Shoot</h1>
  </div>
  <div class="col-sm-3" data-color="darkBlue" div-id="endGamePage">
    <h1 class="darkBlue">End Game</h1>
  </div>
</div>

<div style="align-items: center; flex: 1; display: flex;flex-direction: column">


  <div class="row" style="flex:1;width: 100%;display:flex;justify-content: space-around;flex-direction: row">


    <div id="feedPage" style="display:flex;flex: 0 1 75%;flex-direction:column;">
      <div style="margin: 15px;flex: 1; display: flex;">
        <object style="display:flex;flex: 1;" type="image/svg+xml" id="feedSVG"
                data="/util/svg/<?= ($helper->LEFT_TEAM == "blue" ? "leftBlueFeed" : "leftRedFeed") ?>.svg"></object>
      </div>


    </div>
    <div id="breachPage" style="display:flex;flex: 0;width: 0;height: 0;overflow: hidden">
      <div style="display: flex;flex-direction: row;width: 75%;margin: 16px; border: 3px solid black;">

        <div zone-id="<?= ($helper->LEFT_TEAM == "red" ? 4 : 1) ?>"
             zone-name="<?= ($helper->LEFT_TEAM == "red" ? "Red Home" : "Blue Home") ?>"
             class="col-sm-2 <?= $helper->LEFT_TEAM ?>" style=" display: flex; flex-direction: column;">

          <?php

          function makeSideCircleContainers()
          {
            for ($i = 0; $i < 5; $i++) {

              echo <<<EOT

<div class="circleContainer sideCircleContainer">
              <div class="circle startBreachCircle" style="background-color: orange;">
                <h4>Start</h4>
              </div>
              <div class="circle backOutFromBreachCircle" style="background-color: purple;">
                <h4 style="color: white">End</h4>
              </div>
            </div>

EOT;
            }
          }

          makeSideCircleContainers();

          ?>

        </div>

        <div class="col-sm-3"
             style="border: 3px solid black;border-width: 0 3px 0 3px;    display: flex; flex-direction: column;">

          <?php

          for ($i = 5; $i >= 2; $i--) {
            $defense = $match->getDefenseAt($helper->LEFT_TEAM, $i);

            echo '<div class="defense" defense-id="' . $defense->id . '"  defense-name="' . $defense->name . '"  > <div style="background-image: url(\'/util/img/defenses/' . $defense->img . '\');" class="imageContainer"></div> <div class="circleContainer defenseCircleContainer"> <div class="circle defenseCircle" style=\'background-color: green;\'> <h4>Try</h4> </div> </div> </div>';
          }


          ?>

          <div class="defense" defense-id="9" defense-name="Low Bar">
            <div style="background-image: url('/util/img/defenses/lowbar.jpg');" class="imageContainer"></div>
            <div class="circleContainer defenseCircleContainer">
              <div class="circle defenseCircle" style='background-color: green;'><h4>Try</h4></div>
            </div>
          </div>


        </div>


        <div zone-id="7" zone-name="Neutral Territory" class="col-sm-2 yellowSide"
             style="background-color: #fdff6e; display: flex; flex-direction: column;">
          <?php makeSideCircleContainers(); ?>
        </div>

        <div class="col-sm-3"
             style="border: 3px solid black;border-width: 0 3px 0 3px;   display: flex; flex-direction: column;">
          <div class="defense" defense-id="9" defense-name="Low Bar">
            <div style="background-image: url('/util/img/defenses/lowbar.jpg');" class="imageContainer"></div>
            <div class="circleContainer defenseCircleContainer">
              <div class="circle defenseCircle" style='background-color: green;'><h4>Try</h4></div>
            </div>
          </div>


          <?php
          for ($i = 2; $i <= 5; $i++) {
            $defense = $match->getDefenseAt($helper->RIGHT_TEAM, $i);

            echo '<div class="defense" defense-id="' . $defense->id . '"  defense-name="' . $defense->name . '"  > <div style="background-image: url(\'/util/img/defenses/' . $defense->img . '\');" class="imageContainer"></div> <div class="circleContainer defenseCircleContainer"> <div class="circle defenseCircle" style=\'background-color: green;\'> <h4>Try</h4> </div> </div> </div>';
          }

          ?>

        </div>

        <div zone-id="<?= ($helper->RIGHT_TEAM == "red" ? 4 : 1) ?>"
             zone-name="<?= ($helper->RIGHT_TEAM == "red" ? "Red Home" : "Blue Home") ?>"
             class="col-sm-2 <?= $helper->RIGHT_TEAM ?>" style=" display: flex; flex-direction: column;">
          <?php makeSideCircleContainers(); ?>

        </div>


      </div>

      <div style="    display: flex; flex-direction: column; width: 25%; align-items: center; flex-flow: row;">

        <div id="breachStuck" style="    width: 80%; height: 43%; margin: 0 auto; background-color: red; flex: 0 1 85%;">
          <h2 style="text-align: center;color: white;font-weight: bold;margin: 40px 0 40px 0;">Got Stuck!</h2></div>

      </div>


    </div>
    <div id="shootPage" style="display:flex;flex: 0%;width: 0;height: 0;overflow: hidden;flex-direction:column;">

      <div style="margin: 15px;flex: 1; display: flex;">
        <div style="display: flex;flex-direction: row;width: 60%;">
          <object style="cursor:crosshair;display:flex;flex: 1;" type="image/svg+xml"
                  field-direction="<?= ($helper->LEFT_TEAM == $SCOUTING_TEAM_COLOR ? "right" : "left") ?>" id="shootSVG"
                  data="/util/svg/<?= ($SCOUTING_TEAM_COLOR == "red" ? "blue" : "red") . ($helper->LEFT_TEAM == $SCOUTING_TEAM_COLOR ? "Right" : "Left") ?>Shoot.svg"></object>
        </div>

        <div style="display: flex;flex-direction: column;width: 40%;">

          <div style="    display: flex; flex-direction: column; width: 80%; flex-flow: column; height: 10px; margin: 0 auto; flex: 1 0 100%; justify-content: center; align-items: center;">

            <div
              style="display:flex;flex-direction:row;flex: 0 1 80px; width: 100%; margin: 5% auto 5% auto;border: 2px solid black;">
              <div id="shootHigh" style="display:flex;align-items:center;flex: 1;border-right: 2px solid black">
                <h2 style="font-weight:bold;flex: 1;text-align: center">High</h2>
              </div>
              <div id="shootLow" style="display:flex;align-items:center;flex: 1">
                <h2 style="font-weight:bold;flex: 1;text-align: center">Low</h2>
              </div>
            </div>
            <div
              style="display:flex;flex-direction:row;flex: 0 1 80px; width: 100%; margin: 5% auto 5% auto;border: 2px solid black;">
              <div id="shootScore" style="display:flex;align-items:center;flex: 1;border-right: 2px solid black">
                <h2 style="font-weight:bold;flex: 1;text-align: center">Score</h2>
              </div>
              <div id="shootMiss" style="display:flex;align-items:center;flex: 1">
                <h2 style="font-weight:bold;flex: 1;text-align: center">Miss</h2>
              </div>
            </div>

          </div>

        </div>


      </div>


    </div>
    <div id="endGamePage" style="flex-direction: column; display: flex; flex: 0;width: 0;height: 0;overflow: hidden;align-items: center;justify-content: center;">

      <div style="width: 100%">
        <div style="width: 50%;float: left;height: 2px"></div>
        <div style="width: 50%;float: left">
          <h1 style="text-align: center;font-weight: bold">Climb</h1>
        </div>
      </div>

      <div style="flex-direction:row;display: flex;flex: 0 1 300px;width: 100%;">

        <div id="reachedBatter" data="false" style="display:flex;align-items:center;justify-content: center;flex: 1 1 50%; background-color: #BD5A5A;margin: 50px;">
          <h1 style="margin:15px;text-align: center;font-weight: bold;color:white">Did Not Reach Batter</h1>

        </div>

        <div data-started="false" id="climbStartEnd" style="display:flex;flex: 1 1 50%; background-color: #FF7F2A;margin: 50px;">
          <div  style="display:flex;align-items:center;justify-content: center;flex: 1;margin: 50px;">
            <h1 style="margin:15px;text-align: center;font-weight: bold;color:white">Start</h1>

          </div>
        </div>

      </div>
      <div style="width: 100%">
        <div style="width: 50%;float: left;height: 2px"></div>
        <div style="width: 50%;float: left">
          <h1 style="text-align: center;font-weight: bold" id="climbTimer">00:00 mins</h1>
        </div>
      </div>

    </div>
    <div style="min-width:25%;flex:0 1;display: flex;flex-direction: column;">
      <div style="margin: 15px;flex: 1;display: flex;height: 100%;flex-direction: column;">
        <h2
          style="margin:0;width:100%;border: 1px solid black;padding: 20px 0 20px 0;text-align: center;font-weight: bold">
          History</h2>

        <div id="historyList"
             style="border: 1px solid black;border-top:0;width: 100%;border-right:2px solid black;flex: 1;overflow: scroll">

        </div>

      </div>

    </div>
  </div>


</div>

</body>
<script>


var feedSVGDoc;
var shootSVGDoc;
var HALF_FIELD_LENGTH_INCHES = <?=$helper::HALF_FIELD_LENGTH_INCHES?>;
var HALF_FIELD_HEIGHT_INCHES = <?=$helper::HALF_FIELD_HEIGHT_INCHES?>;

var SHOOT_POS_X = null;
var SHOOT_POS_Y = null;
var SHOOT_LEVEL = null;
var SHOOT_RESULT = null;

$(document).ready(function () {

  var RESUMING_MATCH = <?=($RESUMING_MATCH ? "true" : "false")?>;
  var LEFT_TEAM_COLOR = "<?=($helper->LEFT_TEAM)?>";


  if (!RESUMING_MATCH) {
    $.cookie("matchData", "", {expires: 3650, path: '/'});
  }

  $("#taskSwitcher div").click(function () {
    $("#taskSwitcher div").removeClass("active");
    $(this).addClass("active");

    $("#feedPage,#breachPage,#shootPage,#endGamePage").css("flex", "0").css("width", "0").css("height", "0").css("overflow", "hidden");
    $("#" + $(this).attr("div-id")).css("flex", "0 1 75%").css("width", "").css("height", "").css("overflow", "");

    if ($(this).attr("div-id") == "breachPage") {
      $("#breachPage .circle.startBreachCircle").css("display", "block");
      $("#breachPage .circle.startBreachCircle").each(function (index, e) {
        if ($(this).height() > $(this).width()) {
          $(this).height($(this).width())
        }
        else {
          $(this).width($(this).height())
        }
      });
      $("#breachPage .circle.startBreachCircle").css("display", "none");
      $("#breachPage .circle.backOutFromBreachCircle").css("display", "block");
      $("#breachPage .circle.backOutFromBreachCircle").each(function (index, e) {
        if ($(this).height() > $(this).width()) {
          $(this).height($(this).width())
        }
        else {
          $(this).width($(this).height())
        }
      });
      $("#breachPage .circle.backOutFromBreachCircle").css("display", "none");

      $("#breachPage .circle.defenseCircle").css("display", "block");
      $("#breachPage .circle.defenseCircle").each(function (index, e) {
        if ($(this).height() > $(this).width()) {
          $(this).height($(this).width())
        }
        else {
          $(this).width($(this).height())
        }
      });
      $("#breachPage .circle.defenseCircle").css("display", "none");
    }
  });

  $("#historyList").on("click", ".deleteHistoryItem", function () {
    $(this).parent().remove();
  });


  var DISABLE_NEXT_BREACH_HOVEROUT = false;
  var BREACH_MAP_FILTER = "origin";


  $("#breachPage .sideCircleContainer,#breachStuck").click(function () {
    if (BREACH_MAP_FILTER == "origin" && $(this).hasClass("sideCircleContainer")) {
      DISABLE_NEXT_BREACH_HOVEROUT = true;
      var start = $(this).find(".startBreachCircle");
      var backOut = $(this).find(".backOutFromBreachCircle");

      if ($(start).css("display") == "block") {
        BREACH_MAP_FILTER = "defense";

        $(this).attr("startedHere", "true");

      }
    }
    else if (BREACH_MAP_FILTER == "destination") {

      DISABLE_NEXT_BREACH_HOVEROUT = true;


      var startedZone = $(".sideCircleContainer[startedHere='true']").parent().attr("zone-name");
      var defense = $(".defenseCircle:visible").parent().parent();
      var defenseName = $(".defenseCircle:visible").parent().parent().attr("defense-name");
      var defenseImg = $(".defenseCircle:visible").parent().parent().find(".imageContainer").css("background-image");

      var rightColor = "#FFFFFF",leftColor = "#FFFFFF";
      var img1,img2;

      var startColor = startedZone.split(" ")[0];
      var direction = ($(defense).parent().index() < $(".sideCircleContainer[startedHere='true']").parent().index() ? "left" : "right");

      if(startColor == "Red"){
        startColor = "#FC2C16";
      }
      else  if(startColor == "Blue"){
        startColor = "#4C9DCE";
      }
      else{
        startColor = "#FDFF6E";
      }

      if (direction == "right") {
        leftColor = startColor;
      }
      else {
        rightColor = startColor;
      }


      if($(this).hasClass("sideCircleContainer")){

        var endingZone = $(".backOutFromBreachCircle:visible").parent().parent().attr("zone-name");
        var endColor = endingZone.split(" ")[0];
        if(endColor == "Red"){
          endColor = "#FC2C16";
        }
        else if(endColor == "Blue"){
          endColor = "#4C9DCE";
        }
        else{
          endColor = "#FDFF6E";
        }

        if (direction == "right") {
          rightColor = endColor;
        }
        else {
          leftColor = endColor;
        }

        if(leftColor != rightColor){
          img1 = img2 =  '/util/img/' + direction + 'Arrow.png';
        }
        else{
          if(direction == "right"){
            img1 =  '/util/img/' + direction + 'Arrow.png';
            img2 = '/util/img/leftUTurn.png';
          }
          else{
            img2 =  '/util/img/' + direction + 'Arrow.png';
            img1 = '/util/img/rightUTurn.png';
          }
        }

      }
      else{
        if(direction == "right"){
          img1 =  '/util/img/' + direction + 'Arrow.png';
          img2 = '/util/img/trapped.png';
        }
        else{
          img2 =  '/util/img/' + direction + 'Arrow.png';
          img1 = '/util/img/trapped.png';
        }
      }







      var html = '<div class="historyItem breachHistoryItem" style="display: flex">' +
        '<img class="deleteHistoryItem" src="/util/img/redX.gif" style="flex: 0 0 10%;height: 85%;">' +
        '<div style="display: flex;flex: 1 1 80%;flex-direction: row;height: 100%">' +
        '<div style="flex : 1 0 20%;display: flex;align-items: center;background-color: ' + leftColor + '">' +
        '<img src="'+img1+'" style="width: 80%;margin: 0 auto"/>' +
        '</div>' +
        '<div style="flex : 1 0 60%;background-image: ' + defenseImg.replace(new RegExp('"', 'g'), "'") + ';background-size: contain; background-repeat: no-repeat; background-position: center;"></div>' +
        '<div style="flex : 1 0 20%;display: flex;align-items: center;background-color: ' + rightColor + '">' +
        '<img src="'+img2+'" style="width: 80%;margin: 0 auto"/>' +
        '</div>' +
        '</div>' +
        '<img class="moveHistoryItem" src="/util/img/upDownImage.png" style="flex: 0 0 10%;height: 85%;">' +
        '</div> ';
      $("#historyList").prepend(html);



      $(".sideCircleContainer[startedHere='true']").removeAttr("startedHere").find(".startBreachCircle").hide();
      $(".defenseCircle:visible").parent().siblings().eq(0).removeClass("blurred");
      $(".defenseCircle:visible").hide()
      $(".backOutFromBreachCircle:visible").hide();
      BREACH_MAP_FILTER = "origin";


    }
  });
  $("#breachPage .sideCircleContainer").hover(function () {

      DISABLE_NEXT_BREACH_HOVEROUT = false;

      var start, backOut;

      if (BREACH_MAP_FILTER == "origin") {
        start = $(this).find(".startBreachCircle");
        backOut = $(this).find(".backOutFromBreachCircle");


        if ($(start).css("display") == "none") {
          if ($(backOut).css("display") != "block") {
            $(start).css("display", "block")
          }
          else {
            DISABLE_NEXT_BREACH_HOVEROUT = true;
          }
        }
        else {
          $(start).css("display", "none");
          $(backOut).css("display", "block");

        }
      }
      else if (BREACH_MAP_FILTER == "destination") {

        var tryIndex = $(".defenseCircle:visible").parent().parent().parent().index();
        var finishIndex = $(this).parent().index();

        if (Math.abs(tryIndex - finishIndex) != 1) {
          return;
        }


        var ind = $(".startBreachCircle:visible").parent().index();

        start = $(this).siblings().parent().find(".sideCircleContainer").eq(ind).find(".startBreachCircle");
        backOut = $(this).siblings().parent().find(".sideCircleContainer").eq(ind).find(".backOutFromBreachCircle");


        if ($(start).css("display") == "none") {
          $(backOut).css("display", "block");
        }
        else {
          $(start).css("display", "none");
          $(backOut).css("display", "block");
        }

      }

    },
    function () {

      if (DISABLE_NEXT_BREACH_HOVEROUT) {
        DISABLE_NEXT_BREACH_HOVEROUT = !DISABLE_NEXT_BREACH_HOVEROUT;
        return;
      }

      var start, backOut;

      if (BREACH_MAP_FILTER == "origin") {

        start = $(this).find(".startBreachCircle");
        backOut = $(this).find(".backOutFromBreachCircle");


        if ($(backOut).css("display") == "none") {
          $(start).css("display", "none")
        }
        else {
          $(start).css("display", "block");
          $(backOut).css("display", "none");
        }


      }
      else if (BREACH_MAP_FILTER == "destination") {

        var ind = $(".backOutFromBreachCircle:visible").parent().index();

        start = $(this).siblings().parent().find(".sideCircleContainer").eq(ind).find(".startBreachCircle");
        backOut = $(this).siblings().parent().find(".sideCircleContainer").eq(ind).find(".backOutFromBreachCircle");


        if ($(start).parent().attr("startedHere") == "true") {
          $(start).css("display", "block");
          $(backOut).css("display", "none");
        }
        else {
          $(backOut).css("display", "none");
        }
      }

    })
  $("#breachPage .defenseCircleContainer").hover(function () {

      var originIndex = $(".startBreachCircle:visible").parent().parent().index();
      var defenseIndex = $(this).parent().parent().index();

      if (Math.abs(originIndex - defenseIndex) != 1) {
        return;
      }


      if (BREACH_MAP_FILTER == "defense") {
        DISABLE_NEXT_BREACH_HOVEROUT = false;
        var circle = $(this).find(".defenseCircle");

        $(circle).css("display", "block");
        $(this).parent().find(".imageContainer").addClass("blurred");

      }

    },
    function () {
      if (BREACH_MAP_FILTER == "defense") {
        if (DISABLE_NEXT_BREACH_HOVEROUT) {
          DISABLE_NEXT_BREACH_HOVEROUT = !DISABLE_NEXT_BREACH_HOVEROUT;
          return;
        }
        var circle = $(this).find(".defenseCircle");
        $(circle).css("display", "none");
        $(this).parent().find(".imageContainer").removeClass("blurred");

      }

    })
  $("#breachPage .defenseCircleContainer").click(function () {
    if (BREACH_MAP_FILTER == "defense") {
      DISABLE_NEXT_BREACH_HOVEROUT = true;
      BREACH_MAP_FILTER = "destination"

      var defenseIndex = $(this).parent().index();
      var origin = $(".startBreachCircle:visible");
      var originIndex = $(origin).parent().index();
      if (defenseIndex != originIndex) {
        $(origin).css("display", "none");
        $(origin).parent().removeAttr("startedHere");
        $(origin).parent().parent().find(".startBreachCircle").eq(defenseIndex).css("display", "block").parent().attr("startedHere", "true");
      }


    }
  });

  $("#shootPage #shootHigh, #shootPage #shootLow").click(function () {

    $(this).css("background-color", "#FF7F2A").attr("selected");
    $(this).siblings().eq(0).css("background-color", "").removeAttr("selected");
    if($(this).attr("id") == "shootHigh"){
      SHOOT_LEVEL = 1;
    }
    else{
      SHOOT_LEVEL = 0;
    }

    checkAndAddShootHistoryItem();

  });
  $("#shootPage #shootScore, #shootPage #shootMiss").click(function () {

    $(this).css("background-color", "#2A7FFF").attr("selected");
    $(this).siblings().eq(0).css("background-color", "").removeAttr("selected");
    if($(this).attr("id") == "shootScore"){
      SHOOT_RESULT = 1;
    }
    else{
      SHOOT_RESULT = 0;
    }
    checkAndAddShootHistoryItem();

  });

  $("#endGamePage #reachedBatter").click(function(){
    if($(this).attr("data") == "false"){
      $(this).attr("data","true").css("background-color","#458045").find("h1").text("Reached Batter");
    }
    else{
      $(this).attr("data","false").css("background-color","#BD5A5A").find("h1").text("Did Not Reach Batter");;
    }
  })

  var sec = 0;
  function pad ( val ) { return val > 9 ? val : "0" + val; }
var climbTimer;
  $("#endGamePage #climbStartEnd").click(function(){

    if($(this).attr("data-started") == "false"){
      $("#climbTimer").text( pad(parseInt(sec/60,10)) + ":" + pad(sec%60) + " mins");

      climbTimer = setInterval( function(){
        var seconds = pad(++sec%60);
        var mins = pad(parseInt(sec/60,10));
        $("#climbTimer").text(mins+":"+seconds + " mins");
      }, 1000);
      $(this).attr("data-started","true").find("h1").text("End")
    }
    else{
      clearInterval(climbTimer);
      $(this).attr("data-started","false").find("h1").text("Start")

    }


  });



});

$(window).on('load', function () {


  // Get the Object by ID
  var a = document.getElementById("feedSVG");
  // Get the SVG document inside the Object tag
  feedSVGDoc = a.contentDocument;
  // Get one of the SVG items by ID;
  feedSVGDoc.addEventListener("mouseover", function (e) {
    // svgItem.setAttribute("fill", "#50ce4c");
    feedSVGDocMouseOver(e);
  });
  feedSVGDoc.addEventListener("mouseout", function (e) {
    // svgItem.setAttribute("fill", "#4c9dce");
    feedSVGDocMouseOut(e);

  });
  feedSVGDoc.addEventListener("click", function (e) {
    // svgItem.setAttribute("fill", "#4c9dce");
    feedSVGDocClick(e);

  });

  a = document.getElementById("shootSVG");
  // Get the SVG document inside the Object tag
  shootSVGDoc = a.contentDocument;
  shootSVGDoc.addEventListener("click", function (e) {
    // svgItem.setAttribute("fill", "#4c9dce");
    shootSVGDocClick(e);

  });


  dragula([document.getElementById("historyList")],
    {
      moves: function (el, container, handle) {
        return handle.className === 'moveHistoryItem';
      }
    }
  );

});


function feedSVGDocMouseOver(e) {
  if (e.target.getAttribute("allow-hover") == "true") {
    e.target.setAttribute("fill", "#50ce4c");

  }

}


function feedSVGDocMouseOut(e) {
  if (e.target.getAttribute("allow-hover") == "true") {
    e.target.setAttribute("fill", e.target.getAttribute("orig-color"));
  }

}

function feedSVGDocClick(e) {
  if (e.target.getAttribute("allow-hover") == "true") {

    var zone = e.target.getAttribute("zone-name");

    $("#historyList").prepend("<div class=\"historyItem feedHistoryItem\" style='display: flex'> " +
    "<img class='deleteHistoryItem' src='/util/img/redX.gif' style='flex: 0 0 10%;height: 85%;'/>" +
    "<h3 style='flex: 1 1 80%;text-align: center;line-height: 21px'><b>Feed</b> - " + zone + "</h3>" +
    "<img class='moveHistoryItem' src='/util/img/upDownImage.png' style='flex: 0 0 10%;height: 85%;'/>" +
    "</div>");

    $("#historyList h3").each(function (index, e) {
      var enteredLoop = false;
      while ($(this).height() != 21) {
        enteredLoop = true
        $(this).css("font-size", (parseFloat($(this).css("font-size").substring(0, $(this).css("font-size").length - 2)) - 0.1) + "px");
      }

      if (enteredLoop) {
        $(this).css("font-size", (parseFloat($(this).css("font-size").substring(0, $(this).css("font-size").length - 2)) - 0.5) + "px");
      }

    })


  }

}

function shootSVGDocClick(e) {
  var layer1 = shootSVGDoc.getElementById("layer1").getBoundingClientRect();

  var minWidth = layer1.left;
  var maxWidth = minWidth + layer1.width;
  var minHeight = layer1.top;
  var maxHeight = minHeight + layer1.height;

  if (e.clientX >= minWidth && e.clientX <= maxWidth && e.clientY >= minHeight && e.clientY <= maxHeight) {

    var clickX = e.clientX - minWidth;
    var clickY = e.clientY - minHeight;

    var clickXInches = (clickX / layer1.width) * HALF_FIELD_LENGTH_INCHES;
    var clickYInches = (clickY / layer1.height) * HALF_FIELD_HEIGHT_INCHES;

    var direction = $("#shootSVG").attr("field-direction");

    var actualClickXInches, actualClickYInches;

    if (direction == "left") {
      actualClickXInches = clickXInches;
      actualClickYInches = clickYInches;
    }
    else {
      actualClickXInches = HALF_FIELD_LENGTH_INCHES - clickXInches;
      actualClickYInches = HALF_FIELD_HEIGHT_INCHES - clickYInches;
    }
    if (shootSVGDoc.getElementById("shootPosition")) {
      d3.select(shootSVGDoc.getElementById("shootPosition"))
        .attr("cx", (clickX / layer1.width) * 498.90457)
        .attr("cy", (clickY / layer1.height) * 489.37781)
    }
    else {

      d3.select(shootSVGDoc.rootElement).append("svg:circle")
        .attr("cx", (clickX / layer1.width) * 498.90457)
        .attr("cy", (clickY / layer1.height) * 489.37781)
        .attr("r", 10)
        .attr("id", "shootPosition")
        .attr("style", "cursor:crosshair;fill: #ff6600; fill-opacity: 1; fill-rule: nonzero; stroke: #000000; stroke-width: 6.58412218; stroke-linecap: round; stroke-linejoin: bevel; stroke-miterlimit: 4; stroke-opacity: 1; stroke-dasharray: none; stroke-dashoffset: 0; stroke-width: 3px;");

    }


    SHOOT_POS_X = actualClickXInches;
    SHOOT_POS_Y = actualClickYInches;
    checkAndAddShootHistoryItem();
//    console.log("(" + (clickX / layer1.width) * 498.90457 + "," + (clickY / layer1.height) * 489.37781 + ")");

  }
}

function checkAndAddShootHistoryItem(){

  if(SHOOT_LEVEL != null && SHOOT_RESULT != null && SHOOT_POS_X != null && SHOOT_POS_Y != null){

    var result,level,bg;

    if(SHOOT_RESULT == 1){
      result = "Scored";
      bg = "#A1FFA1"
    }
    else{
      result = "Missed";
      bg = "#FFA1A1"
    }

    if(SHOOT_LEVEL == 1){
      level = "High";
    }
    else{
      level = "Low";
    }

    $("#historyList").prepend("<div class=\"historyItem shootHistoryItem\" style='background: "+bg+"; display: flex'> " +
    "<img class='deleteHistoryItem' src='/util/img/redX.gif' style='flex: 0 0 10%;height: 85%;'/>" +
    "<h3 style='flex: 1 1 80%;text-align: center;line-height: 21px'><b>" + result +"</b> " + level + " Goal</h3>" +
    "<img class='moveHistoryItem' src='/util/img/upDownImage.png' style='flex: 0 0 10%;height: 85%;'/>" +
    "</div>");
    d3.select(shootSVGDoc.getElementById("shootPosition")).transition().duration(400).style("opacity", 0).each("end", function(){
      d3.select(shootSVGDoc.getElementById("shootPosition")).remove();
    });
    $("#shootPage #shootScore, #shootPage #shootMiss").animate({ backgroundColor: "transparent"}, 'slow').removeAttr("selected");
    $("#shootPage #shootHigh, #shootPage #shootLow").animate({ backgroundColor: "transparent"}, 'slow').removeAttr("selected");

    SHOOT_LEVEL = SHOOT_POS_X = SHOOT_POS_Y = SHOOT_RESULT = null;

  }
}

</script>
</html>