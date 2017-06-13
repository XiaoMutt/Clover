<?php
include_once '_table_catalogs.php';
include_once '_ui_import.php';

class UIImportCatalog extends UIImport{
    public function __construct() {
        $this->cOpt = new OperateCatalogsTable();
        $this->sRecordAjaxProcessing = "_ss_record_processing_catalogs.php";
        $this->sRecordNameTag = $this->cOpt->sTableLabel;
        parent::__construct();
    }
    protected function addDatabaseLabel() {
        echo'<label>'.$this->sRecordNameTag.'</label>';
    }
}

$cPage = new UIImportCatalog();
$cPage->Html();
?>
