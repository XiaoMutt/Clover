<?php
include_once('_table_jargons.php');

$cOpt = new OperateJargonsTable();

if ($_POST["action"] == "detail") {
    $jResult = $cOpt->AjaxRead($_POST['iId'], "detail", true);
} else if ($_POST["action"] == "update") {
    $aaValues = json_decode($_POST["values"], true);
    $aKeys = json_decode($_POST['keys'], true);

    for ($i = 0; $i < count($aaValues); $i++) {
        for ($j = 0; $j < count($aKeys); $j++) {
            $aaData[$i][$aKeys[$j]] = $aaValues[$i][$j];
        }
    }
    $jResult = $cOpt->AjaxUpdate($aaData);
} else if ($_POST["action"] == "add") {
    $aaValues = json_decode($_POST["values"], true);
    $aKeys = json_decode($_POST['keys'], true);
    for ($i = 0; $i < count($aaValues); $i++) {
        for ($j = 0; $j < count($aKeys); $j++) {
            $aaData[$i][$aKeys[$j]] = $aaValues[$i][$j];
        }
    }
        $jResult = $cOpt->AjaxAdd($aaData);

} else if ($_POST["action"] == "edit") {
    $jResult = $cOpt->AjaxRead($_POST['iId'], "edit");
} else if ($_POST["action"] == "delete") {
    $aIds = explode(",", $_POST["sIds"]);
    $jResult = $cOpt->AjaxDelete($aIds);
}


echo $jResult;
?>
