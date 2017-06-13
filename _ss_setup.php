<?php
    include_once '_setup.php';
    
    $aKeys=json_decode($_POST["keys"]);
    $aValues=json_decode($_POST["values"]);
    $aData=array();
    for($i=0; $i<count($aKeys); $i++){
        $aData[$aKeys[$i]]=$aValues[$i];
    }
    $cSetup=new SetupClover($aData);
    echo $cSetup->jResult;

    
?>
