<?php
include_once '_table_jargons.php';
include_once '_ui_db_display.php';

class Jargon extends UIDBDisplay {

    public function __construct() {
        $this->cOpt = new OperateJargonsTable();
        $this->sRecordAjaxProcessing = "_ss_record_processing_jargons.php";
        $this->sTableAjaxProcessing = "_ss_ajax_search_jargons.php";
        $this->sRecordNameTag = $this->cOpt->sTableLabel;
        parent::__construct();
    }

    protected function addButtons() {
        parent::addButtons();
        ?>
        <!--Import button-->
        <img src="icons/basket_put.png" id="import" onclick="window.location.href='import_jargons.php'">

        <?php
    }

}

$cPage = new Jargon();
$cPage->Html();
?>
