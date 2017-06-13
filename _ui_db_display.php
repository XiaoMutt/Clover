<?php
include_once '_ui_display.php';

class UIDBDisplay extends UIDisplay {

    protected $sRecordAjaxProcessing;
    protected $sTableAjaxProcessing;
    protected $sRecordNameTag;
    protected $aFields;
    protected $aFieldNames;
    protected $aFieldLabels;
    protected $cOpt; //table operator;

    public function __construct() {
        $this->cOpt->getFields($this->aFields, "label", "brief");
        $this->cOpt->getFields($this->aFieldNames, "name", "edit");
        $this->cOpt->getFields($this->aFieldLabels, "label", "edit");
    }

    protected function initializeDataTable() {
        echo'
                oTable=$("#data_table").dataTable( {
                    "aoColumnDefs": [
                        { "bSortable": false, "aTargets": [ 0 ] }
                    ],
                    "aaSorting": [[1, "asc"]]     ,
                    "sPaginationType": "full_numbers",
                    "bServerSide": true,
                    "bProcessing": true,
                    "bJQueryUI": true,
                    "sAjaxSource":"' . $this->sTableAjaxProcessing . '"});';
    }

    protected function editDialog() {
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
    }

    protected function addButtons() {
        echo '<img src="icons/add.png" id="add"/>';
    }

    protected function htmlHead() {
        parent::htmlHead();
        ?>
        <script type="text/javascript" charset="utf-8">
            var oTable; //datatable object;
            var aData=new Array(); //Data;
            var asInitVals = new Array();//column search filter
            var sServerSide="<?php echo $this->sRecordAjaxProcessing ?>"; //server side processing php file name;
                                                                                            
                                                                                                                                                                                            
            //format add dialog;
            function fnFormatAddDialog(){
                $('#add_message').html("Please Enter");
                $("#add_form input").val("");
                $("#add_dialog").dialog("open");
            }
                                                                                                                            
            //function for add records;
            function fnAdd(){
                var aName =new Array();
                var aValue=new Array();
                var aaValue=new Array();
                                                                                                                                
                if($('#add_form input').length>0){                
                    $('#add_form input').each(function(){
                        aName.push(this.name);
                        aValue.push($(this).val());
                    });
                }
                if($('#add_form select').length>0){
                    $('#add_form select').each(function(){
                        aName.push(this.name);
                        aValue.push($(this).val());                        
                    });
                }     
                aaValue.push(aValue);
                var sName=array2json(aName);
                var sValue=array2json(aaValue);
                $.post(
                sServerSide,
                                                                                                                                    
                {action: "add", keys: sName, values: sValue},
                                                                                                                                    
                function (data){
                    if (data["changed"]==0){
                        fnMessenger("warning", "Please check your input.");
                        $('#add_message').html(data["errors"]);
                    }        
                    else{
                        fnMessenger("OK", "New <?php echo $this->sRecordNameTag ?> added.")
                        $('#add_dialog').dialog("close");
                        oTable.fnDraw();                           
                    }
                                                                                                                                        
                },
                                                                                                                                    
                "json"
            ).error(function (){fnMessenger("error", "Server error!");});                
            }   
                                                                                                                            
            //function for send edited form to server;
            function fnUpdateRecord(){
                var aName =['id'];
                var aValue=new Array();
                var aaValue=new Array();
                aValue.push(aData[1]);
                if($('#edit_form input').length>0){                
                    $('#edit_form input').each(function(){
                        aName.push(this.name);
                        aValue.push($(this).val());
                    });
                }
                if($('#edit_form select').length>0){
                    $('#edit_form select').each(function(){
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
                                                                                                                                
                {action: "update", keys: sName, values: sValue},
                                                                                                                                    
                function (data){
                    if (data["changed"]=="0"){
                        fnMessenger("warning", "Please check your input.");
                        $('#edit_message').html(data["errors"]);
                    }        
                    else{
                        fnMessenger("OK", "<?php echo $this->sRecordNameTag ?> updated.");
                        $('#edit_dialog').dialog( "close" );
                        oTable.fnDraw();                              
                    }
                                                                                                                                        
                },
                                                                                                                                    
                "json"
            ).error(function (){fnMessenger("error", "Server error!");});
            }   
                                                                                                                            
            //format edit dialog
            function fnFormatEditDialog(){
                $('#edit_message').html("Please Enter");
                fnMessenger("waiting", "Contacting server...");
                                                                                                                                
                $.post(
                sServerSide,
                                                                                                                                
                {action: "edit",  iId: aData[1]}, //aData[1] contains the id of the row that was clicked;

                function(data){
                    $('#messenger_dialog').hide();
                    for (var sVal in data){
                        $('#edit_form input[name="'+sVal+'"], select[name="'+sVal+'"]').val(data[sVal]);
                    }
                    $("#edit_dialog").dialog("open");
                },

                "json"                
            ).error(function(){fnMessenger("error", "Server error!");});
            }        
            // Formating function for row details
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


            //function for deleting records;
            function fnDeleteRecords ()
            {
                //make the clicked row id into a array and then in a string in which ids are separated by ",". This is because the deleteRecords functions require an array as argument;
                var aIds=Array();
                aIds[0]=aData[1];
                sIdString=aIds.join(",");
                                                                                                                                                                                               
                fnMessenger("waiting", "Contacting server...");
                $.post(
                sServerSide,
                                                                                                                                                                                    
                {action: "delete",  sIds: sIdString},
                                                                                                                                                                                    
                function(data) {
                    if (data["changed"]=="0"){
                        fnMessenger("warning", "Operation failed at server side!");
                    }
                    else{
                        fnMessenger("OK", "<?php echo $this->sRecordNameTag ?> deleted.");
                        oTable.fnDraw();                        
                    }

                },
                                                                                                                                                                                
                "json"
            ).error(function(){fnMessenger("error", "Server error!");});
            }            
                                                                                                                            
            //
            //document ready function
            //
            $(function(){
                //prepare datatable;
        <?php $this->initializeDataTable(); ?>
                                                                                                                                
                //prepare add_dialog;
                $('#add_dialog').dialog({
                    autoOpen: false,
                    width: 600,
                    modal: true,
                    buttons: {
                        "OK": function() {
                            fnAdd();
                        },
                        "Cancel": function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });                                                                

                //prepare edit_dialog;
                $('#edit_dialog').dialog({
                    autoOpen: false,
                    width: 600,
                    modal: true,
                    buttons: {
                        "OK": function() {
                            fnUpdateRecord ();
                        },
                        "Cancel": function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
                                                                                                                                                                                
                //prepare delete_confirmation_dialog;
                $('#delete_confirmation_dialog').dialog({
                    autoOpen: false,
                    resizable: false,
                    height: 160,
                    modal: true,
                    buttons: {
                        "Delete": function() {
                            fnDeleteRecords ();
                            $( this ).dialog( "close" );
                        },
                        "Cancel": function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });


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
                    else if (this.name=="action_edit")
                    {
                        fnFormatEditDialog();//fill the edit_dialog with existed data;
                    }

                    else if (this.name=="action_delete")//delete icon;
                    {
                        $("#delete_confirmation_dialog").dialog("open");
                    }
                } );
                                                                                                                                
                                                                                                                                
                //open add dialog
                $('#add').on ('click', function (){
                    fnFormatAddDialog();//clean the edit_dialog and show;
                });
                                
                $("tfoot input").keyup( function () {
                    /* Filter on the column (the index) of this element */
                    oTable.fnFilter( this.value, $("tfoot input").index(this) );
                } );
                	
                	
                	
                /*
                 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
                 * the footer
                 */
                $("tfoot input").keyup( function () {
                    /* Filter on the column (the index) of this element */
                    oTable.fnFilter( this.value, $("tfoot input").index(this) );
                } );
        	                
                        
                $("tfoot input").each( function (i) {
                    asInitVals[i] = this.value;
                } );
                	
                $("tfoot input").focus( function () {
                    if ( this.className == "search_init" )
                    {
                        this.className = "search_done";
                        this.value = "";
                    }
                } );
                	
                $("tfoot input").blur( function (i) {
                    if ( this.value == "" )
                    {
                        this.className = "search_init";
                        this.value = asInitVals[$("tfoot input").index(this)];
                    }
                } );                
                                                                                                                                                                                                                        
            })
                                                                                                                                                                                            
                                                                                                                                                                                                         
                                                                                                                                                                                                                        
                                                                                                                                                                                                          
        </script>
        <?php
    }

    protected function htmlBody() {
        parent::htmlBody();
        ?>

        <!--delete dialog-->
        <div id="delete_confirmation_dialog" title="Delete Confirmation"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Do you really want to delete this record?</p></div>

        <div id="container">

            <!--Database Label-->
            <label><?php echo $this->sRecordNameTag ?>s</label>

            <!--Add button-->               
            <?php $this->addButtons(); ?>
            <br/>

            <!--Data Table-->
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="data_table">
                <thead>
                    <tr>
                        <?php
                        echo "<th>Actions</th>";
                        foreach ($this->aFields as &$sColumn) {
                            echo "<th>" . $sColumn . "</th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <?php
                        echo "<th>Filter:</th>";
                        foreach ($this->aFields as &$sColumn) {
                            echo '<th><input type="text" name="' . $sColumn . '" value="' . $sColumn . '" class="search_init"/></th>';
                        }
                        ?>                        
                    </tr>
                </tfoot>                 
              
            </table>
             
        </div>

        <!--add dialog-->
        <div id="add_dialog" title="Add a <?php echo $this->sRecordNameTag ?>">
            <form id="add_form">
                <div id="add_message">Please Enter</div>
                <fieldset>
                    <?php $this->editDialog(); ?>
                </fieldset>
            </form>            
        </div>
        <!--edit dialog-->
        <div id="edit_dialog" title="Edit this <?php echo $this->sRecordNameTag ?>">
            <form id="edit_form">
                <div id="edit_message">Please Enter</div>
                <fieldset>
                    <?php $this->editDialog(); ?>
                </fieldset>
            </form>            
        </div>        

        <?php
        ;
    }

}
?>
