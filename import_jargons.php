<?php
include_once '_table_jargons.php';
include_once '_ui_import.php';

class UIImportJargon extends UIImport{
    public function __construct() {
        $this->cOpt = new OperateJargonsTable();
        $this->sRecordAjaxProcessing = "_ss_record_processing_jargons.php";
        $this->sRecordNameTag = $this->cOpt->sTableLabel;
        parent::__construct();
    }
    protected function addDatabaseLabel() {
        echo'<label>'.$this->sRecordNameTag.'</label>';
    }
}

$cPage = new UIImportJargon();
$cPage->Html();
?>
