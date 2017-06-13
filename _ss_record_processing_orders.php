<?php
include_once '_table_orders.php';
    $cOpt=new operateOrdersTable();

    if($_POST["action"]=="detail"){
        $jResult=$cOpt->AjaxRead($_POST['iId'], "detail", true);
    }
    else if ($_POST["action"]=="update"){
        $aaValues=json_decode($_POST["values"],true);
        $aKeys=json_decode($_POST['keys'], true);

        for ($i=0; $i<count($aaValues); $i++){
            for ($j=0; $j<count($aKeys); $j++){
                if($aKeys[$j]=="ordered_on"||$aKeys[$j]=="received_on"){
                    $aaData[$i][$aKeys[$j]]=$cOpt->CloverDate2mysqlDate($aaValues[$i][$j]);
                }
                else{
                $aaData[$i][$aKeys[$j]]=$aaValues[$i][$j];   
                }
            }
        }
        $jResult=$cOpt->AjaxUpdate($aaData);
    }
    else if ($_POST["action"]=="add"){
        $aaValues=json_decode($_POST["values"],true);
        $aKeys=json_decode($_POST['keys'], true);

        for ($i=0; $i<count($aaValues); $i++){
            for ($j=0; $j<count($aKeys); $j++){
                $aaData[$i][$aKeys[$j]]=$aaValues[$i][$j];     
            }
            $aaData[$i]["requested_by"]=$cOpt->iSessionUserId; 
            $aaData[$i]["requested_on"]=$cOpt->CloverDateTime2mysqlDateTime(date($cOpt->sDateTimeFormat));
            if(empty ($aaData[$i]["ordered_on"])){
                $aaData[$i]["ordered_by"]="0";
                $aaData[$i]["ordered_on"]="";                
            }
            else{
                $aaData[$i]["ordered_by"]=$cOpt->iSessionUserId; 
            }
            if(empty ($aaData[$i]["received_on"])){             
                $aaData[$i]["received_by"]="0";
                $aaData[$i]["received_on"]="";
            }
            else{
                $aaData[$i]["received_by"]=$cOpt->iSessionUserId;
            }
                
        }
        $jResult=$cOpt->AjaxAdd($aaData);
    }
    else if ($_POST["action"]=="edit"){
        $jResult=$cOpt->AjaxRead($_POST['iId'], "edit", false, false);
    }
    else if ($_POST["action"]=="delete") {
        $aIds=explode(",", $_POST["sIds"]);        
        $jResult=$cOpt->AjaxDelete($aIds);
    }
    else if($_POST["action"]=="ordered"){
        $aIds=explode(",", $_POST["sIds"]);
        for ($i=0; $i<count($aIds); $i++){
            $aaData[$i]["id"]=$aIds[$i];
            $aaData[$i]["ordered_on"]=$cOpt->CloverDate2mysqlDate(date($cOpt->sDateFormat));
            $aaData[$i]["ordered_by"]=$cOpt->iSessionUserId;
        }
        $jResult=$cOpt->AjaxUpdate($aaData);        
    }
    else if($_POST["action"]=="received"){
        $aIds=explode(",", $_POST["sIds"]);
        for ($i=0; $i<count($aIds); $i++){
            $aaData[$i]["id"]=$aIds[$i];
            $aaData[$i]["received_on"]=$cOpt->CloverDate2mysqlDate(date($cOpt->sDateFormat));
            $aaData[$i]["received_by"]=$cOpt->iSessionUserId;
        }
        $jResult=$cOpt->AjaxUpdate($aaData);        
    }    
    
    echo $jResult;
?>
