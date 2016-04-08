<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 3/10/2016
 * Time: 8:20 PM
 */

include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");

$helper = new Helper();

$match = new Match($_GET['matchNumber'],1);

foreach($match->getTeams() as $team){
  $match->deleteData($team);

}

echo "Success";

?>