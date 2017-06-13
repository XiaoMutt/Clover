<?php

/*
 * This file handle the post request send by ajax.
 * Input data: 
 * "action" should be "add" "edit" "delete";
 * "table" should be the desired table.
 * "id" should be the record id;
 * 
 * 
 */
include_once('_table_users.php');
$cOpt = new OperateUsersTable($_POST["action"] != "register");
$sErr = "";

if($_POST["action"] == "register"){
    $_POST["action"] = "add";
}

function CheckInput(&$aData) {
    global $cOpt, $sErr;
    if (empty($aData['name'])) {
        $sErr.="Please enter your name.<br/>";
    }
    if (!filter_var($aData['email'], FILTER_VALIDATE_EMAIL)) {
        $sErr.="Please enter a validate email.<br/>";
    }
    if (isset($aData["id"])) {//edit user;
        $aUser = $cOpt->readRecord($aData['id'], "edit", false, false);
        if ($aData['email'] != $aUser['email'] && $cOpt->checkIfUserExist($aData['email'])) {
            $sErr.="This email has been used by others.<br/>";
        }
    } else {//new user;
        if ($cOpt->checkIfUserExist($aData['email'])) {
            $sErr.="This email has been used by others.<br/>";
        }
    }
}

function CheckInputPassword(&$aData) {
    global $sErr;
    if ($aData['password'] == "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855") {
        $sErr.="Please enter a password.<br/>";
    }
    if ($aData['password'] != $aData['repeat_password']) {
        $sErr.="Passwords you typed do not match.<br/>";
    }
}

if ($_POST["action"] == "add") {
    $sErr = "";
    $aaData = Array(Array());
    $aaValues = json_decode($_POST["values"], true);
    $aKeys = json_decode($_POST['keys'], true);

    for ($i = 0; $i < count($aaValues); $i++) {
        for ($j = 0; $j < count($aKeys); $j++) {
            $aaData[$i][$aKeys[$j]] = $aaValues[$i][$j];
        }
        CheckInput($aaData[$i]);
        CheckInputPassword($aaData[$i]);
        if (empty($sErr)) {
            if ($cOpt->sSessionUserType != "admin") {
                $aaData[$i]["identity"] = "visitor";
            }
            unset($aaData[$i]["repeat_password"]);
            $aaData[$i]["password"] = sha256($aaData[$i]["password"]);
        } else {
            break;
        }
    }

    if (empty($sErr)) {
        $jResult = $cOpt->AjaxAdd($aaData);
    } else {
        $aRlt["changed"] = 0;
        $aRlt["errors"] = $sErr;
        $jResult = json_encode($aRlt);
    }
} else {
    if ($_POST["action"] == "detail") {
        $jResult = $cOpt->AjaxRead($_POST['iId'], "detail", true);
    } else if ($_POST["action"] == "update") {
        $aaValues = json_decode($_POST["values"], true);
        $aKeys = json_decode($_POST['keys'], true);

        for ($i = 0; $i < count($aaValues); $i++) {
            for ($j = 0; $j < count($aKeys); $j++) {
                $aaData[$i][$aKeys[$j]] = $aaValues[$i][$j];
            }
            if ($cOpt->sSessionUserType != "admin" && $cOpt->iSessionUserId != $aaData[$i]["id"]) {
                $sErr.="You do not have rights to edit this user.<br/>";
            }
            CheckInput($aaData[$i]);
        }
        if (empty($sErr)) {
            $jResult = $cOpt->AjaxUpdate($aaData);
        } else {
            $aRlt["changed"] = 0;
            $aRlt["errors"] = $sErr;
            $jResult = json_encode($aRlt);
        }
    } else if ($_POST["action"] == "edit") {//user for format edit form;
        $jResult = $cOpt->AjaxRead($_POST['iId'], "edit", false, false);
    } else if ($_POST["action"] == "delete") {
        if ($cOpt->sSessionUserType != "admin") {
            $sErr.="You do not have rights to delete users.<br/>";
            $aRlt["changed"] = 0;
            $aRlt["errors"] = $sErr;
            $jResult = json_encode($aRlt);
        } else {
            $aIds = explode(",", $_POST["sIds"]);
            $jResult = $cOpt->AjaxDelete($aIds);
        }
    } else if ($_POST["action"] == "change_password") {
        $aValues = json_decode($_POST["values"], true);
        $aKeys = json_decode($_POST['keys'], true);
        for ($i = 0; $i < count($aKeys); $i++) {
            $aData[$aKeys[$i]] = $aValues[$i];
        }
        if ($cOpt->iSessionUserId != $aData['id']) {
            $sErr.="You do not have rights to change the password of this user.<br/>";
        } else {
            $aUser = $cOpt->readRecord($aData['id']);
            if ($aUser['password'] != sha256($aData['old_password'])) {
                $sErr.="Old password is incorrect.<br/>";
            }
            CheckInputPassword($aData);
        }
        if (empty($sErr)) {
            unset($aData["old_password"]);
            unset($aData["repeat_password"]);
            $aData["password"] = sha256($aData["password"]);
            $aaData[] = $aData;
            $jResult = $cOpt->updateRecords($aaData);
        } else {
            $aRlt["changed"] = 0;
            $aRlt["errors"] = $sErr;
            $jResult = json_encode($aRlt);
        }
    } else if ($_POST["action"] == "emaillist") {
        $jResult = $cOpt->AjaxGenerateEmailList();
    }
}

echo $jResult;
?>
