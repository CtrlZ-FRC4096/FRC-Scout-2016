<?php
/**
 * Created by PhpStorm.
 * User: Jayasurya
 * Date: 2/4/2016
 * Time: 10:11 PM
 */


include($_SERVER['DOCUMENT_ROOT']."/util/php/include_classes.php");
$match = new Match($_POST['matchNumber'],$_POST['compID']);

echo json_encode($match->claimedTeams);



?>