<?php
    include_once '_table_stocks.php';
    $cOpt=new OperateStocksTable(base64_decode($_GET["table"]));
    echo $cOpt->AjaxSearch();

?>
