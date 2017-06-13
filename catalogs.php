<?php
include_once '_table_catalogs.php';
include_once '_ui_db_display.php';

class Catalog extends UIDBDisplay {

    public function __construct() {
        $this->cOpt = new OperateCatalogsTable();
        $this->sRecordAjaxProcessing = "_ss_record_processing_catalogs.php";
        $this->sTableAjaxProcessing = "_ss_ajax_search_catalogs.php";
        $this->sRecordNameTag = "Item";
        parent::__construct();
    }

    protected function addButtons() {
        parent::addButtons();
?>
        <!--Import button-->
        <img src="icons/basket_put.png" id="import" onclick="window.location.href='import_catalogs.php'">

<?php
    }

    protected function htmlHead() {
        parent::htmlHead();
        ?>
        <script type="text/javascript" charset="utf-8">
            //function for add records;
            function fnSubmitOrder(){
                var aName =new Array();
                var aValue=new Array();
                var aaValue=new Array();

                if($('#order_form input').length>0){                
                    $('#order_form input').each(function(){
                        aName.push(this.name);
                        aValue.push($(this).val());
                    });
                }
                if($('#order_form select').length>0){
                    $('#order_form select').each(function(){
                        aName.push(this.name);
                        aValue.push($(this).val());                        
                    });
                }     
                                                
                aaValue.push(aValue);
                var sName=array2json(aName);
                var sValue=array2json(aaValue);
                                                
                fnMessenger("waiting", "Contacting server...");
                $.post(
                "_ss_record_processing_orders.php",
                                                    
                {action: "add", keys: sName, values: sValue},
                                                    
                function (data){
                    if (data["changed"]=="0"){
                        fnMessenger("warning", "Operation failed at server side!");
                    }        
                    else{
                        fnMessenger("OK", "Order request added");                        
                        oTable.fnDraw();                              
                    }
                                                        
                },
                                                    
                "json"
            ).error(function (){fnMessenger("error", "Server error!");});
                                                
            }     
            function fnFormatOrderDialog(){
                fnMessenger("waiting", "Contacting server...");
                                        
                $.post(
                sServerSide,
                                        
                {action: "edit", iId: aData[1]}, //aData[1] contains the id of the row that was clicked;

                function(data){
                    $('#messenger_dialog').hide();
                    for (var sVal in data){
                        $('#order_form input[name="'+sVal+'"]').val(data[sVal]);
                    }
                                            
                    $('#order_form input[name="quantity"]').val("");
                    $('#order_form input[name="total_price"]').val("");
                    $("#order_dialog").dialog("open");
                },

                "json"                
            ).error(function(){fnMessenger("error", "Server error!");});
            }   
                                    
            $(function(){
                //prepare order_dialog;
                $('#order_dialog').dialog({
                    autoOpen: false,
                    width: 600,
                    modal: true,
                    buttons: {
                        "OK": function() {
                            fnSubmitOrder ();
                            $( this ).dialog( "close" );
                        },
                        "Cancel": function() {
                            $( this ).dialog( "close" );
                        }
                    }                    
                                            
                })    
                                        
                $('#data_table tbody ').on( 'click', 'img', function () {
                    var nTr = this.parentNode.parentNode;
                    aData = oTable.fnGetData( nTr ); //get the data from that row; aData[1] contains the id of that record;
                    if (this.name=="action_order")//order icon;
                    {
                        fnFormatOrderDialog();//clean the edit_dialog and show;                        
                    }      
                })
                        
                $('input[name="quantity"]').keyup(function (){
                    $(this).parent().find('input[name="total_price"]').val($(this).parent().find('input[name="unit_price"]').val()*$(this).val());
                })

                $('input[name="unit_price"]').keyup(function (){
                    $(this).parent().find('input[name="total_price"]').val($(this).parent().find('input[name="quantity"]').val()*$(this).val());
                })
                        
            })
                                    
        </script>
        <?php
    }

    protected function htmlBody() {
        parent::htmlBody();
        ?>
        <!--order dialog-->
        <div id="order_dialog" title="Order an item">
            <form id="order_form">
                <fieldset>
                    <label>Quantity</label>
                    <input type="text" name="quantity" class="text ui-widget-content ui-corner-all" /><br/>
                    <label>Total Price</label>
                    <input type="text" name="total_price" class="text ui-widget-content ui-corner-all" /><br/>                    
                    <?php
                    for ($i = 0; $i < count($this->aFieldNames); $i++) {
                        if (strpos($this->cOpt->aaTableStructure[$this->aFieldNames[$i]]["data_type"], "enum") !== FALSE) {
                            $aaMatches = array();
                            preg_match_all("/enum\((.+)\).+/", $this->cOpt->aaTableStructure[$this->aFieldNames[$i]]["data_type"], $aaMatches, PREG_SET_ORDER);
                            //aaMatches[0][1] is what within the enum()
                            $aOptions = explode(",", $aaMatches[0][1]);
                            echo '<label>' . $this->aFieldLabels[$i] . '</label>';
                            echo '<select name="' . $this->aFieldNames[$i] . '" class="text ui-widget-content ui-corner-all">';
                            foreach ($aOptions as &$sOption) {
                                $sOption = trim($sOption, " '");
                                echo '<option value="' . $sOption . '">' . $sOption . '</option>';
                            }
                            echo '</select><br/>';
                        } else {
                            echo '<label>' . $this->aFieldLabels[$i] . '</label>';
                            echo '<input type="text" name="' . $this->aFieldNames[$i] . '" class="text ui-widget-content ui-corner-all" /><br/>';
                        }
                    }
                    ?>

                </fieldset>
            </form>             
        </div>  

        <?php
    }

}

$cPage = new Catalog();
$cPage->Html();
?>
