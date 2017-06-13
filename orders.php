<?php
include_once '_table_orders.php';
include_once '_ui_db_display.php';

class Catalog extends UIDBDisplay {

    public function __construct() {
        $this->cOpt = new OperateOrdersTable();
        $this->sRecordAjaxProcessing = "_ss_record_processing_orders.php";
        $this->sTableAjaxProcessing = "_ss_ajax_search_orders.php";
        $this->sRecordNameTag = "Order";
        parent::__construct();
    }

    protected function initializeDataTable() {
        echo'
                oTable=$("#data_table").dataTable( {
                    "aoColumnDefs": [
                        { "bSortable": false, "aTargets": [ 0 ] }
                    ],
                    "aaSorting": [[8, "asc"]]     ,
                    "sPaginationType": "full_numbers",
                    "bServerSide": true,
                    "bProcessing": true,
                    "bJQueryUI": true,
                    "sAjaxSource":"' . $this->sTableAjaxProcessing . '",
                    "fnServerParams": function ( aoData ) {
                         aoData.push( { "name": "from", "value": $("#from").val() }, {"name": "to", "value": $("#to").val()} );
                       }                        

                    });
                        ';
    }

    protected function addButtons() {
        ?>
        <label>From</label>
        <input type="text" id="from" name="from" value="<?php echo date($this->cOpt->sDateFormat, strtotime('-1 month')) ?>"/>
        <label>to</label>
        <input type="text" id="to" name="to" value="<?php echo date($this->cOpt->sDateFormat) ?>"/>
        <?php
        parent::addButtons();
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
                sServerSide,
                                                                                                    
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
                                                            
            //mark as ordered;
            function fnOrdered()
            {
                var aIds=Array();
                aIds[0]=aData[1];
                sIdString=aIds.join(",");
                                                                
                fnMessenger("waiting", "Contacting server...");
                $.post(
                sServerSide,
                                                                    
                {action: "ordered", sIds: sIdString},
                                                                    
                function(data) {
                    if (data["changed"]=="0"){
                        fnMessenger("warning", "Operation failed at server side!");
                    }
                    else{
                        fnMessenger("OK", "Marked as ordered."); 
                        oTable.fnDraw();                        
                    }                    
                },
                                                                
                "json"
            ).error(function (){fnMessenger("error", "Server error!");})
            }            
            //mark as received;
            function fnReceived()
            {
                var aIds=Array();
                aIds[0]=aData[1];
                sIdString=aIds.join(",");
                                                                
                fnMessenger("waiting", "Contacting server...");
                $.post(
                sServerSide,
                                                                    
                {action: "received", sIds: sIdString},
                                                                    
                function(data) {
                    if (data["changed"]=="0"){
                        fnMessenger("warning", "Operation failed at server side!");
                    }
                    else{
                        fnMessenger("OK", "Marked as received."); 
                        oTable.fnDraw();                        
                    }                    
                },
                                                                
                "json"
            ).error(function (){fnMessenger("error", "Server error!");})
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
                    $('#order_form input[name="ordered_on"]').val("");
                    $('#order_form input[name="received_on"]').val("");
                    $("#order_dialog").dialog("open");
                },

                "json"                
            ).error(function(){fnMessenger("error", "Server error!");});
                                                            
                                                               
            }   
                            
            function fnRefreshTable(){
                oTable.fnDraw();
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
                    else if (this.name=="action_ordered")//order icon;
                    {
                        fnOrdered();//mark as ordered;                      
                    }
                    else if (this.name=="action_received")//order icon;
                    {
                        fnReceived();//mark as received;                       
                    }                        
                })
                                                                        
                $('input[name*="_on"]').datepicker({dateFormat: "<?php echo $this->cOpt->sJavaDateFormat ?>"});
                $( "#from" ).datepicker({
                    dateFormat: "<?php echo $this->cOpt->sJavaDateFormat ?>",
                    changeMonth: true,
                    numberOfMonths: 3,
                    onClose: function() {
                        fnRefreshTable();
                    }                    
                });
                $( "#to" ).datepicker({
                    dateFormat: "<?php echo $this->cOpt->sJavaDateFormat ?>",
                    changeMonth: true,
                    numberOfMonths: 3,
                    onClose: function() {
                        fnRefreshTable();
                    }        
                });                
                                                                
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
                        if ($this->aFieldNames[$i]!= "quantity" && $this->aFieldNames[$i]!= "total_price") {
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
