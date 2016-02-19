<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 1/29/2016
 * Time: 8:36 PM
 */
ini_set("display_errors", "1");
error_reporting(E_ALL);

include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();
$currCompetition = $helper->getCurrentCompetition();
$COMP_TEAMS = $helper->getTeamNumbersForCompetition($currCompetition->id);
$LEFT_TEAM = $helper->LEFT_TEAM;
$RIGHT_TEAM = $helper->RIGHT_TEAM;
$CURR_MATCH_NUM = $currCompetition->getLastMatchWithData()->id +1;
$CURR_MATCH;
?>

<!DOCTYPE HTML>
  <html>
    <head>

      <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/client_includes.php"); ?>

      <style>
        .redSide{
          background-color: #CA4132;
        }
        .blueSide{
          background-color: #4C9DCE;
        }
        .middleSide{
           background-color: #FDFF6E;
         }

        .redDefenses div, .blueDefenses div{
          width: 100%;height: 20%;
          box-sizing: border-box;
          overflow: hidden;
          display: flex;
          align-items: center;
          padding: 13px;
        }
        .redDefenses div:not(:first-child), .blueDefenses div:not(:first-child){
          border-top: 1px solid black;;
        }

        .redDefenses h3, .blueDefenses h3{
          margin: 0 auto;
          clear: both;
        }
        .redDefenses img, .blueDefenses img{
          max-width:100%;
          margin: 0 auto;
          height: 100%;
        }

        .redSide div,.blueSide div{
          height: 33.33%;display: flex;align-items: center
        }

        .redSide div div,.blueSide div div{
          margin: 0 auto;
        }

        #defenseModal img{
          max-width: 100%;
        }


      </style>
    </head>
    <body>
    <div style="width: 100%;overflow: hidden">
      <h1 style="text-align: center;clear: both">Match Configuration</h1>

      <div style="width: 75%;position: relative;margin: 0 auto">
        <h2 style="text-align: center">Match
          <select id="matchSelector">
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
          of <?=$currCompetition->name?></h2>

        <a id="saveMap" style="position: absolute;right: 20px;top: 0;" href="#" class="button button-pill button-flat-royal button-large">Save</a>
      </div>

    </div>
      <div class="row center-sm" style="height: 79%;width: 100%;">
        <div class="col-sm-9" style="height: 100%">
          <div class="map" style="height: 100%">

            <div class="row" style="height: 100%">
              <div style="height: 100%" class="col-sm-2">
                <div style="height: 100%" class="<?=$LEFT_TEAM?>Side">
                  <div>
                    <div>
                      <h2 style="color: white">Team 1 <br>
                      <select style="margin-top: 6%">
                        <?php printTeamSelectionOptions() ?>
                      </select></h2>
                    </div>
                  </div>
                  <div>
                    <div>
                      <h2 style="color: white">Team 2 <br>
                        <select style="margin-top: 6%">
                          <?php printTeamSelectionOptions() ?>
                        </select></h2>
                    </div>
                  </div>
                  <div>
                    <div>
                      <h2 style="color: white">Team 3 <br>
                        <select style="margin-top: 6%">
                          <?php printTeamSelectionOptions() ?>
                        </select></h2>
                    </div>
                  </div>
                </div>
              </div>
              <div style="height: 100%" class="col-sm-3">
                <div style="height: 100%" class="<?=$LEFT_TEAM?>Defenses">

                  <?php

                  for($i = 5;$i>=2;$i--){
                    $defense = $CURR_MATCH->getDefenseAt($LEFT_TEAM,$i);

                    if(is_null($defense)){
                      echo "<div><h3>Slot $i</h3></div>";
                    }
                    else{
                      echo '<div defense-id="'.$defense->id.'" category-id="'.$defense->category.'">
                             <img src="/util/img/defenses/' . $defense->img . '" style="max-width:100%;margin: 0 auto">
                            </div>';

                    }

                  }


                  ?>
                  <div><img src="/util/img/defenses/lowbar.jpg" style="max-width:100%;margin: 0 auto"></div>
                </div>
              </div>
              <div style="height: 100%" class="col-sm-2">
                <div style="height: 100%" class="middleSide"></div>
              </div>
              <div style="height: 100%" class="col-sm-3">
                <div style="height: 100%" class="<?=$RIGHT_TEAM?>Defenses">
                  <div><img src="/util/img/defenses/lowbar.jpg" style="max-width:100%;margin: 0 auto"></div>
                  <?php

                  for($i = 2;$i<=5;$i++){
                    $defense = $CURR_MATCH->getDefenseAt($RIGHT_TEAM,$i);

                    if(is_null($defense)){
                      echo "<div><h3>Slot $i</h3></div>";
                    }
                    else{
                      echo '<div defense-id="'.$defense->id.'" category-id="'.$defense->category.'">
                               <img src="/util/img/defenses/'.$defense->img.'" style="max-width:100%;margin: 0 auto">
                            </div>';

                    }

                  }


                  ?>
                </div>
              </div>
              <div style="height: 100%" class="col-sm-2">
                <div style="height: 100%" class="<?=$RIGHT_TEAM?>Side">

                  <div>
                    <div>
                      <h2 style="color: white">Team 1 <br>
                        <select style="margin-top: 6%">
                          <?php printTeamSelectionOptions() ?>
                        </select></h2>
                    </div>
                  </div>
                  <div>
                    <div>
                      <h2 style="color: white">Team 2 <br>
                        <select style="margin-top: 6%">
                          <?php printTeamSelectionOptions() ?>
                        </select></h2>
                    </div>
                  </div>
                  <div>
                    <div>
                      <h2 style="color: white">Team 3 <br>
                        <select style="margin-top: 6%">
                          <?php printTeamSelectionOptions() ?>
                        </select></h2>
                    </div>
                  </div>



                </div>
              </div>
            </div>

          </div>
        </div>
      </div>


    <div class="remodal" data-remodal-id="defenseModal" id="defenseModal">
      <button data-remodal-action="close" class="remodal-close"></button>
      <h1>Select Defense</h1>


      <?php

      foreach($helper->getDefensesByCategory() as $index => $category){
        echo '
      <h2>Category '.$index .'</h2>

      <div category-id="'.$index.'" class="row middle-sm" style="width: 80%;height:50%;margin: 0 auto">';


        foreach($category as $defense){
          echo '<div class="col-sm-6" defense-id="'.$defense['id'].'" style="height: 100%;">
                  <img src="/util/img/defenses/'.$defense['img'].'">
                </div>';
        }

        echo "</div>";
      }

      ?>

      <button data-remodal-action="cancel" class="remodal-cancel">Cancel</button>
      <button data-remodal-action="confirm" class="remodal-confirm">OK</button>
    </div>

    </body>

<script>

  var LEFT_TEAM = "<?=$LEFT_TEAM?>";
  var RIGHT_TEAM = "<?=$RIGHT_TEAM?>";

  $(document).ready(function(){

    $(".blueSide select").eq(0).val("<?=$CURR_MATCH->blue1?>");
    $(".blueSide select").eq(1).val("<?=$CURR_MATCH->blue2?>");
    $(".blueSide select").eq(2).val("<?=$CURR_MATCH->blue3?>");
    $(".redSide select").eq(0).val("<?=$CURR_MATCH->red1?>");
    $(".redSide select").eq(1).val("<?=$CURR_MATCH->red2?>");
    $(".redSide select").eq(2).val("<?=$CURR_MATCH->red3?>");

//    $('.redDefenses h3,.blueDefenses h3').flexVerticalCenter();

    $("body").on("click",".redDefenses h3,.blueDefenses h3,.redDefenses img,.blueDefenses img",function(e){

        var prefix = "";

        if($(e.target).parent().parent().hasClass("redDefenses")){
          prefix="red";
        }
       else{
          prefix="blue";
        }

        var code = "";
      var num = 0;

        if(LEFT_TEAM == prefix){
          num = (5 -$(e.target).parent().index());
          code = prefix + "-" + num;
        }
      else{
          num = ($(e.target).parent().index() + 1);
          code = prefix + "-" + num;
        }

      if(num == 1){
        return;
      }

     $("[data-remodal-id='defenseModal'").attr("data-slot",code);
      var inst = $("[data-remodal-id='defenseModal'").remodal();
      inst.open();
    });

    $("#defenseModal img").click(function(e){

      var code = $("#defenseModal").attr("data-slot");
      code = code.split("-");
      var index;
      if(code[0] == LEFT_TEAM){
        index = 5 - parseInt(code[1]);
      }
      else{
        index = parseInt(code[1]) -1;
      }

      $("."+code[0]+"Defenses div")
        .eq(index)
        .attr("defense-id",$(e.target).parent().attr("defense-id"))
        .attr("category-id",$(e.target).parent().parent().attr("category-id"))
        .html("<img src='" + $(e.target).attr("src") + "'>");
      var inst = $("[data-remodal-id='defenseModal'").remodal();
      inst.close();
    });

    $("#saveMap").click(function(){

      $(".redDefenses div,.blueDefenses div")
        .css("border","");

      $(".redDefenses div:not(:first-child), .blueDefenses div:not(:first-child)")
        .css("border-top","1px solid black");

      var totals = {};
      totals.A = 0;
      totals.B = 0;
      totals.C = 0;
      totals.D = 0;
      var toastSent = false;
      var stop = false;
      var data = {matchID:$("#matchSelector option:selected").val(),
                  compID: <?=$currCompetition->id?> ,
                  data : []};
      $(".<?=$LEFT_TEAM?>Defenses div[defense-id]").each(function(){
        data.data.push({
           side:"<?=$LEFT_TEAM?>",
           slot:(5 -$(this).index()),
           defenseID:$(this).attr("defense-id")
        });
        switch($(this).attr("category-id")){
          case "A":
            totals.A++;
                break;
          case "B":
            totals.B++;
                break;
          case "C":
            totals.C++;
                break;
          case "D":
            totals.D++;
                break;
          default:
                break;
        }
      });
      $.each(totals,function(index,e){
        if(e > 1){
          $(".<?=$LEFT_TEAM?>Defenses div[category-id='"+index+"']").css("border","2px solid red");
          stop = true;
          if(!toastSent){
            toastr["error"]("You have used defenses from the same category on the same side.", "All Defense Categories Must Be Used")
            toastSent = true;
          }
        }
      });
//RIGHT SIDE
      totals.A = 0;
      totals.B = 0;
      totals.C = 0;
      totals.D = 0;
      $(".<?=$RIGHT_TEAM?>Defenses div[defense-id]").each(function(){

        data.data.push({
          side:"<?=$RIGHT_TEAM?>",
          slot:($(this).index() +1),
          defenseID:$(this).attr("defense-id")
        });

        switch($(this).attr("category-id")){
          case "A":
            totals.A++;
            break;
          case "B":
            totals.B++;
            break;
          case "C":
            totals.C++;
            break;
          case "D":
            totals.D++;
            break;
          default:
            break;
        }

      });
      $.each(totals,function(index,e){
        if(e > 1){
          $(".<?=$RIGHT_TEAM?>Defenses div[category-id='"+index+"']").css("border","2px solid red");
          stop = true;
          if(!toastSent){
            toastr["error"]("You have used defenses from the same category on the same side.", "All Defense Categories Must Be Used")
            toastSent = true;
          }
        }
      });


      var existingTeams = [];
      var duplicateTeams = [];
      toastSent = false;
      $(".blueSide select,.redSide select").each(function(){
        if($.inArray($(this).find("option:selected").val(),existingTeams) != -1){
          if($.inArray($(this).find("option:selected").val(),duplicateTeams) == -1){
            duplicateTeams.push($(this).find("option:selected").val());
          }
          stop = true;
          if(toastSent == false){
            toastr["error"]("A team has been selected more than once.", "Validation Error")
            toastSent = true;
          }
        }
        else{
          existingTeams.push($(this).find("option:selected").val());
        }
      });

      $(".blueSide select,.redSide select").each(function() {

        if($.inArray($(this).find("option:selected").val(),duplicateTeams) != -1){
          $(this).css("background-color","#FF7777");
        }
        else{
          $(this).css("background-color","#FFFFFF");
        }

      });


        if(!stop){
          var redSideSelects = $(".redSide select");
          var blueSideSelects = $(".blueSide select");
          data.red1 = $(redSideSelects).eq(0).val();
          data.red2 = $(redSideSelects).eq(1).val();
          data.red3 = $(redSideSelects).eq(2).val();
          data.blue1 = $(blueSideSelects).eq(0).val();
          data.blue2 = $(blueSideSelects).eq(1).val();
          data.blue3 = $(blueSideSelects).eq(2).val();

        $.ajax({
          type: "POST",
          url: "/util/php/serve/updateMatchConfig.php",
          data: {data:JSON.stringify(data)},
          async: false,
          success: function (data) {
           if(data == "Success"){
             toastr["success"]("The match has been updated successfully", "Success!")
           }
          }
        });
      }

    });

    $("#matchSelector").change(function(){
      var value = $(this).find("option:selected").val();

      $.ajax({
        type: "POST",
        url: "/util/php/serve/getMatch.php",
        data: {matchID:value,compID: <?=$currCompetition->id?>, records:false},
        async: false,
        success: function (data) {
          data = JSON.parse(data);

          $(".<?=$LEFT_TEAM?>Defenses div").each(function(i,e){

            var index = (5 -$(e).index());
            if(index != 1){
              var matchedDefense = null;
              $.each(data.defenses, function(j,defense){
                if(defense.side == "<?=$LEFT_TEAM?>" && defense.slot == index){
                  matchedDefense = defense;
                }
              });

              if(matchedDefense != null){
                $(e).replaceWith('<div defense-id="'+matchedDefense.id+'" category-id="'+matchedDefense.category+'"> <img src="/util/img/defenses/'+matchedDefense.img+'" style="max-width:100%;margin: 0 auto"> </div>' );
              }
              else{
                $(e).replaceWith("<div><h3>Slot " + index + "</h3></div>");
              }
            }

          })

          $(".<?=$RIGHT_TEAM?>Defenses div").each(function(i,e){

            var index = $(e).index() + 1;
            if(index != 1){
              var matchedDefense = null;
              $.each(data.defenses, function(j,defense){
                if(defense.side == "<?=$RIGHT_TEAM?>" && defense.slot == index){
                  matchedDefense = defense;
                }
              });

              if(matchedDefense != null){
                $(e).replaceWith('<div defense-id="'+matchedDefense.id+'" category-id="'+matchedDefense.category+'"> <img src="/util/img/defenses/'+matchedDefense.img+'" style="max-width:100%;margin: 0 auto"> </div>' );
              }
              else{
                $(e).replaceWith("<div><h3>Slot " + index + "</h3></div>");
              }
            }

          })


          $(".blueSide select").eq(0).val(data.blue1);
          $(".blueSide select").eq(1).val(data.blue2);
          $(".blueSide select").eq(2).val(data.blue3);
          $(".redSide select").eq(0).val(data.red1);
          $(".redSide select").eq(1).val(data.red2);
          $(".redSide select").eq(2).val(data.red3);



        }
      });

    })


  });


</script>


  </html>

<?php

function printTeamSelectionOptions(){
  global $COMP_TEAMS;
  foreach($COMP_TEAMS as $team){
    echo "<option value='$team'>$team</option>";
  }

}

?>