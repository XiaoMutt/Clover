<?php
include_once '_table_stocks.php';
include_once '_ui_import.php';

class UIImportStock extends UIImport {

    public function __construct($sTableName) {
        $this->cOpt = new OperateStocksTable($sTableName);
        $this->sRecordAjaxProcessing = "_ss_record_processing_stocks.php?table=" . base64_encode($this->cOpt->sTableName);
        $this->sRecordNameTag = $this->cOpt->sTableLabel;
        parent::__construct();
    }

    protected function addDatabaseLabel() {
        ?>
        <select class="text ui-widget-content ui-corner-all" id="stock_select" onchange="window.location.href=$('#stock_select option:selected').attr('value')">
            <?php
            foreach ($this->cOpt->aStockLabels as $sKey => &$sValue) {

                echo '<option ' . ($sKey == $this->cOpt->sTableName ? 'selected="selected"' : '') . ' value="' . $_SERVER['PHP_SELF'] . '?table=' . base64_encode($sKey) . '">' . $sValue . '</option>';
            }
            ?>
        </select>  
        <?php
    }

}

if (isset($_GET["table"])) {
    $sTable = base64_decode($_GET["table"]);
} else {
    $sTable = "";
}
$cPage = new UIImportStock($sTable);
$cPage->Html();
?>
