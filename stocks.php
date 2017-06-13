<?php
include_once '_table_stocks.php';
include_once '_ui_db_display.php';

class UIStock extends UIDBDisplay {

    public function __construct($sTableName) {
        $this->cOpt = new OperateStocksTable($sTableName);
        $this->sRecordAjaxProcessing = "_ss_record_processing_stocks.php?table=" . base64_encode($this->cOpt->sTableName);
        $this->sTableAjaxProcessing = "_ss_ajax_search_stocks.php?table=" . base64_encode($this->cOpt->sTableName);
        $this->sRecordNameTag = $this->cOpt->sTableLabel;
        parent::__construct();
    }

    protected function addButtons() {
        parent::addButtons();
        ?>
        <!--Import button-->
        <img src="icons/basket_put.png" id="import" onclick="window.location.href='import_stocks.php?table=<?php echo base64_encode($this->cOpt->sTableName);?>'">
        <select class="text ui-widget-content ui-corner-all" id="stock_select" onchange="window.location.href=$('#stock_select option:selected').attr('value')">
            <?php
            foreach ($this->cOpt->aStockLabels as $sKey => &$sValue) {

                echo '<option ' . ($sKey == $this->cOpt->sTableName ? 'selected="selected"' : '') . ' value="' . $_SERVER['PHP_SELF'] . '?table=' . base64_encode($sKey) . '">' . $sValue . '</option>';
            }
            ?>
        </select> 
        <input type="checkbox" id="checkjargon" title="first 10 keywords separated by space, comma, semicolon, colon, slash, backslash, and vertical bar will be checked for jargons (first 10 jargons will be used)."/>Lookup Jargons during search
        <?php
    }

    protected function initializeDataTable() {
        echo'
                //prepare datatable;
                oTable = $("#data_table").dataTable( {
                    "aoColumnDefs": [
                        { "bSortable": false, "aTargets": [ 0 ] }
                    ],
                    "aaSorting": [[1, "asc"]],
                    "sPaginationType": "full_numbers",
                    "bServerSide": true,
                    "bProcessing": true,
                    "bJQueryUI": true,
                    "sAjaxSource":"' . $this->sTableAjaxProcessing . '",
                    "fnServerParams": function ( aoData ) {
                         aoData.push( { "name": "checkjargon", "value": $("#checkjargon").is(":checked") });
                       },
                } );
                        ';
    }

    protected function htmlHead() {
        parent::htmlHead();
        ?>
        <script type="text/javascript" charset="utf-8">
            $(function(){
                $("#checkjargon").on("click", function (){
                    oTable.fnDraw();
                })
                                        
            })
                                    
                                    
        </script>    

        <?php
    }

}

if (isset($_GET["table"])) {
    $sTable = base64_decode($_GET["table"]);
} else {
    $sTable = "";
}
$cPage = new UIStock($sTable);
$cPage->Html();
?>
