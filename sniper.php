<?php
include_once '_ui_display.php';
include_once'_table_stocks.php';

class Sniper extends UIDisplay {

    protected $sRecordAjaxProcessing;
    protected $cOpt; //table operator;

    public function __construct() {
        $this->cOpt = new OperateStocksTable('primer_stock');
        $this->sRecordAjaxProcessing = "_ss_record_processing_stocks.php?table=" . base64_encode('primer_stock');
    }

    protected function htmlHead() {
        parent::htmlHead();
        ?>
        <script type="text/javascript" charset="utf-8">
            var oTable; //datatable object;
            var aData=Array(); //Data;
            var sServerSide="<?php echo $this->sRecordAjaxProcessing?>";
            function fnSniper (){
                var seq=$('#dna_text').val();
                if(seq!=""){
                    var tm=$('#tm').val();
                    var primerC=$('#primerC').val();
                    var saltC=$('#saltC').val();
                    var MgC=$('#MgC').val();
                                    
                    tm=(tm>90||tm<40?45:tm);
                    primerC=(primerC>1000||primerC<10?200:primerC);
                    saltC=(saltC>500||saltC<1?200:saltC);
                    MgC=(MgC>10||MgC<0.1?200:MgC);
                                    
                    fnMessenger("waiting", "Contact server...");
                    $.post(
                    "_ss_sniper.php",

                    {DNA:seq, minTm: tm, primerCon: primerC, saltCon:saltC, MgCon:MgC},

                    function (aaMatchs){
                        if (aaMatchs.length){
                                                                        
                            oTable.fnClearTable();
                            for (index in aaMatchs){
                                aaMatchs[index].unshift('<img src="icons/page_white_text_width.png" name="action_close" title="Show/Hide details">');
                            }
                            oTable.fnAddData(aaMatchs);
                            fnMessenger("OK", "Sniper found: "+ aaMatchs.length+ " primers.");
                        }
                        else{
                            oTable.fnDraw();   
                            fnMessenger("OK", "Sniper did find any primer work for your DNA sequence.");                            
                        }
                    },

                    "json"
                ).error(function () {fnMessenger("error", "Server error!");});                     
                                    
                }
            }            
            function fnShowDetails ( nTr )
            {
                fnMessenger("waiting", "Contacting server...");
                $.post(
                sServerSide,

                {action: "detail",  iId: aData[1]}, //aData[1] contains the id of the row that was clicked;

                function(data){
                    $('#messenger_dialog').hide();
                    var sOut='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
                    for (var sVal in data){
                        sOut+="<tr><td>"+sVal+": </td><td>"+data[sVal]+"</td></tr>";
                    }
                    sOut += '</table>';

                    oTable.fnOpen( nTr, sOut, 'details' );
                },

                "json"
            ).error(function (){fnMessenger("error", "Server error!");});
            }
                                                                                                            
            //
            //document ready function
            //
            $(function(){
                //prepare datatable;
                oTable=$('#data_table').dataTable( {
                    "aoColumnDefs": [
                        { "bSortable": false, "aTargets": [ 0 ] }
                    ],
                    "aaSorting": [[1, 'asc']]     ,
                    "sPaginationType": "full_numbers",
                    "bServerSide": false,
                    "bProcessing": true,
                    "bJQueryUI": true
                } );          
                                                                                                                
                $('#data_table tbody ').on( 'click', 'img', function () {
                    var nTr = this.parentNode.parentNode;
                    aData = oTable.fnGetData( nTr ); //get the data from that row; aData[1] contains the id of that record;
                    if (this.name=="action_open")//detail icon close;
                    {
                        // This row is already open - close it
                        this.name="action_close";
                        oTable.fnClose( nTr );
                    }
                    else if(this.name=="action_close")//detail icon open;
                    {
                        // Open this row
                        this.name="action_open";
                        fnShowDetails(nTr);
                    }
                });                    
                //prepare dna_dialog;
                $('#dna_dialog').dialog({
                    autoOpen: false,
                    width: 700,
                    modal: true,
                    buttons: {
                        "OK": function() {
                            fnSniper ();
                            $( this ).dialog( "close" );
                        },
                        "Cancel": function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });                 
                $('#submit_button').click(function(){
                    $('#dna_dialog').dialog("open");                   
                });      
                
                $('#submit_button').button();
            })
                                                                                                                                                                            
                                                                                                                                                                                         
                                                                                                                                                                                                        
                                                                                                                                                                                          
        </script>
        <?php
    }

    protected function htmlBody() {
        parent::htmlBody();
        ?>
        <div id="container">
            <div class="table_jui">
                <label>Sniper </label><button id="submit_button">Sniper a DNA sequence</button>
                <!--Data Table-->
                <table cellpadding="0" cellspacing="0" border="0" class="display" id="data_table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Sequence</th>
                            <th>3' aligned at</th>
                            <th>Aligned Direction</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div> 
            <div id="dna_dialog" title="Sniper">
                <label>Please paste the DNA sequence below. <br/>NOTE: Characters other than A, T, G, C will be ignored.</label><br/>
                <textarea id="dna_text"></textarea><br/>
                <label>Search primers aligned to the DNA at a Tm above (between 40 and 90)</label><input id="tm" width="4" value="45"/><label>°C <br/>under the following condition:</label><br/>
                <label>Primer concentration (10-1000): </label><input id="primerC" width="4" value="200"/><label>nM</label><br/>
                <label>Salt concentration (1-500): </label><input id="saltC" width="4" value="50"/><label>mM</label><br/>
                <label>Mg++ concentration: (0.1-10)</label><input id="MgC" width="4" value="1.5"/><label>nM</label><br/>
            </div>             
        </div>
        <?php
        ;
    }

}

$cPage = new Sniper;
$cPage->Html();
?>
