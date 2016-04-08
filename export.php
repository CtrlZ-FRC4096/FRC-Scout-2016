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
?>




<!DOCTYPE HTML>
<html>
<head>
  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/client_includes.php"); ?>

  <style>
    #allMatches,#exportMatches {
      list-style-type: none;
      padding-left:0;
      margin: 0 auto;
      text-align: center;
    }

    #allMatches li,#exportMatches li{
      display: inline-block;
      vertical-align: top;
    }

    .availableButton {
      font-size: 18px;
    }

    h3{
      text-align: center;
    }

  </style>

</head>
<body style="background-image: '/util/img/scouting-home-bg.jpg';">
<div style="width: 100%;overflow: hidden">
  <h1 style="text-align: center;">Export Data</h1>
  <h2 style="text-align: center;"><?=$currCompetition->name?></h2>
</div>
<hr>

<div class="row" style="width: 100%;">
  <div class="col-sm-6" >
    <h3>Available Matches</h3>
    <div style=" margin-top: 3%;">
      <ul id="allMatches">
        <?php
          $count = 0;
        foreach($currCompetition->matches as $match){
          $count++;
          echo "<li><div style='margin: 10px'>
                    <a data-matchNumber='$match->matchNumber'
                    class='availableButton button button-rounded button-flat-primary button-large'>$match->matchNumber</a>
                </div></li>";
          if($count == 5){
            echo "<br>";
            $count = 0;
          }
        }

        ?>
      </ul>
    </div>
    </div>
  <div class="col-sm-6" >
    <h3>Selected Matches</h3>
    <div style=" margin-top: 3%;">
      <ul id="exportMatches">

      </ul>
    </div>
  </div>
  <a id="exportSelected" style="margin: 0 auto; margin-top: 40px;"
    class='button button-rounded button-flat-action button-large'>
    Export Data
  </a>
</div>
<script>

$(document).ready(function(){
  var maxWidth = Math.max.apply(null, $(".availableButton").map(function ()
  {
    return $(this).width();
  }).get());

  $(".availableButton").width(maxWidth);

  $(".availableButton").click(function(){

    var matchNumber = $(this).text();

    if($(this).hasClass("disabled")){
      $(this).removeClass("disabled");
      $(".exportButton[data-matchNumber='"+matchNumber+"']").parent().parent().remove();
      $("#exportMatches br").remove();
      $("#exportMatches li:nth-child(5n)").after("<br>");
      return;
    }

    var count = $(".exportButton").length;
    var br = ""
    if((count) % 5 == 0){
      br = "<br />"
    }

    var html = "<li><div style='margin: 10px'> <a data-matchNumber='"+matchNumber+"' class='exportButton button button-rounded button-flat-primary button-large'>"+matchNumber+"</a> </div></li>"
    $("#exportMatches").html(html + br + $("#exportMatches").html())

    var maxWidth = Math.max.apply(null, $(".exportButton").add(".availableButton").map(function ()
    {
      return $(this).width();
    }).get());

    $(".exportButton").width(maxWidth);

    $(this).addClass("disabled");

    $("#exportMatches li").sort(
        function (a, b){
          return parseInt(($(b).text())) < parseInt(($(a).text())) ? 1 : -1;
      }).appendTo('#exportMatches');

    $("#exportMatches br").remove();
    $("#exportMatches li:nth-child(5n)").after("<br>");
  });


  $("#exportMatches").on("click",".exportButton",function() {

    var num = $(this).text();
    $(this).parent().parent().remove();
    $(".availableButton[data-matchNumber='"+num+"']").removeClass("disabled");
    $("#exportMatches br").remove();
    $("#exportMatches li:nth-child(5n)").after("<br>");

  });

  $("#exportSelected").click(function(){

    var arr = [];

    $("#exportMatches a").each(function(index,e){
      arr.push(parseInt($(e).text()));
    });

    var min = Math.min.apply( Math, arr);
    var max = Math.max.apply( Math, arr);

    $.ajax({
      type: "POST",
      url: "/util/php/serve/generateExportData.php",
      data: {matchNumbers:JSON.stringify(arr)},
      async: false,
      success: function (data) {
        download(data,"matches-" + min + "-" + max + ".txt");
      }
    });

  });


  })



</script>
</body>
</html>