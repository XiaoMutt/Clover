<?php
include_once '_ui_display.php';
include_once '_table_users.php';

class UIRaster extends UIDisplay {

    public function __construct() {
        $this->cOpt = new OperateUsersTable;
    }

    protected function htmlHead() {
        parent::htmlHead();
        ?>
        <script type="text/javascript" charset="utf-8">
            var oTable; //datatable object;
            var aData; //Data;
            var sServerSide="_ss_record_processing_users.php"; //server side processing php file name;
                                                                                            
            // Formating function for row details
            function fnShowDetails ( nTr )
            {
                fnMessenger("waiting", "Contacting server...");
                $.post(
                sServerSide,

                {action: "detail", iId: aData[1]}, //aData[1] contains the id of the row that was clicked;

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
                            
            //function for changing password;
            function fnChangePassword (){
                var aName =['id'];
                var aValue=new Array();
                                
                aValue.push(aData[1]);
                $('#change_password_form input').each(function(){
                    aName.push(this.name);
                    aValue.push(Sha256.hash($(this).val()));
                });
                var sName=array2json(aName);
                var sValue=array2json(aValue);                
                fnMessenger("waiting", "Contacting server...");
                $.post(
                sServerSide,
                                
                {action: "change_password", keys: sName, values: sValue},
                                    
                function (data){
                    if (data["changed"]=="0"){
                        fnMessenger("warning", "Please check your input.");
                        $('#change_password_dialog_message').html(data["errors"]);
                    }        
                    else{
                        fnMessenger("OK", "Password changed.");
                        $('#change_password_dialog').dialog( "close" );
                        oTable.fnDraw();                              
                    }
                                        
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
                                                                                    
                {action: "delete", sIds: sIdString},
                                                                                    
                function(data) {
                    if (data["changed"]=="0"){
                        fnMessenger("warning", "Operation failed at server side!");
                    }
                    else{
                        fnMessenger("OK", "Record deleted.");
                        oTable.fnDraw();                        
                    }

                },
                                                                                
                "json"
            ).error(function(){fnMessenger("error", "Server error!");});
            }

                           
            //function for add records;
            function fnAdd(){
                var aName =new Array();
                var aValue=new Array();
                var aaValue=new Array();
                                
                $('#add_form input').each(function(){
                    aName.push(this.name);
                    if (this.name=="password"||this.name=="repeat_password"){
                        aValue.push(Sha256.hash($(this).val()));
                    }
                    else{
                        aValue.push($(this).val());
                    }
                });
                aName.push("identity");
                aValue.push($('#add_form select[name="identity"]').val());                
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
                        fnMessenger("OK", "New user added.")
                        $('#add_dialog').dialog("close");
                        oTable.fnDraw();                           
                    }
                                        
                },
                                    
                "json"
            ).error(function (){fnMessenger("error", "Server error!");});                
                                
            }            
            function fnFormatEditDialog(){
                $('#edit_message').html("Please Enter");
                fnMessenger("waiting", "Contacting server...");
                                
                $.post(
                sServerSide,
                                
                {action: "edit", iId: aData[1]}, //aData[1] contains the id of the row that was clicked;

                function(data){
                    $('#messenger_dialog').hide();
                    for (var sVal in data){
                        $('#edit_form input[name="'+sVal+'"]').val(data[sVal]);
                    }
                    $('#edit_form select[name="identity"]').val(data["identity"]);
                    $("#edit_dialog").dialog("open");
                },

                "json"                
            ).error(function(){fnMessenger("error", "Server error!");});
                            
                               
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
                        fnMessenger("OK", "Record updated.");
                        $('#edit_dialog').dialog( "close" );
                        oTable.fnClearTable();
                        oTable.fnDraw();                              
                    }
                                        
                },
                                    
                "json"
            ).error(function (){fnMessenger("error", "Server error!");});
                                
            }            
                            
            function fnFormatAddDialog(){
                $('#add_message').html("Please Enter");
                $("#add_form input").val("");
                $("#add_dialog").dialog("open");
            }            
            function fnFormatChangePasswordDialog(){
                $('#change_password_dialog_message').html("Please Enter");
                $('#change_password_dialog input').val("");
                $("#change_password_dialog").dialog("open");
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
                    "bServerSide": true,
                    "bProcessing": true,
                    "bJQueryUI": true,
                    "sAjaxSource": "_ss_ajax_search_users.php"
                } );          
                                
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
                //prepare change_password_dialog;
                $('#change_password_dialog').dialog({
                    autoOpen: false,
                    width: 400,
                    modal: true,
                    buttons: {
                        "OK": function() {
                            fnChangePassword ();
                        },
                        "Cancel": function() {
                            $( this ).dialog( "close" );
                        }
                    }                
                                
                                
                })                                                                
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
                    else if (this.name=="action_password")
                    {
                        fnFormatChangePasswordDialog();
                    }
                } );
                                
                                
                //open add dialog
                $('#add').on ('click', function (){
                    fnFormatAddDialog();//clean the edit_dialog and show;
                });
                                                                                                                        
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
            <div class="table_jui">

                <!--Database Label-->
                <label>Roster</label>

                <!--Add button-->               
                <?php
                if ($this->cOpt->sSessionUserType == "admin") {
                    echo '<img src="icons/add.png" id="add"/>';
                    echo '<img src="icons/email.png" id="generateemaillist" title="Generate email list of the users in the Cloveriver"/>';
                }
                ?>
                <br/>

                <!--Data Table-->
                <table cellpadding="0" cellspacing="0" border="0" class="display" id="data_table">
                    <thead>
                        <tr>
                            <?php
                            $aFields = array();

                            $this->cOpt->getFields($aFields, "label", "brief");
                            echo "<th>Actions</th>";
                            foreach ($aFields as &$sColumn) {
                                echo "<th>" . $sColumn . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div> 
        </div>

        <!--add dialog-->
        <div id="add_dialog" title="Add an account">
            <form id="add_form">
                <div id="add_message">Please Enter</div>
                <fieldset>
                    <?php
                    $aFieldNames = Array();
                    $aFieldLabels = Array();
                    $this->cOpt->getFields($aFieldNames, "name", "edit");
                    $this->cOpt->getFields($aFieldLabels, "label", "edit");
                    for ($i = 0; $i < count($aFieldNames); $i++) {


                        if (strpos($this->cOpt->aaTableStructure[$aFieldNames[$i]]["data_type"], "enum") !== FALSE) {

                            $aaMatches = array();

                            preg_match_all("/enum\((.+)\).+/", $this->cOpt->aaTableStructure[$aFieldNames[$i]]["data_type"], $aaMatches, PREG_SET_ORDER);
                            //aaMatches[0][1] is what within the enum()

                            $aOptions = explode(",", $aaMatches[0][1]);
                            echo '<label>' . $aFieldLabels[$i] . '</label>';
                            echo '<select name="' . $aFieldNames[$i] . '" class="text ui-widget-content ui-corner-all">';
                            foreach ($aOptions as &$sOption) {
                                $sOption = trim($sOption, " '");
                                echo '<option value="' . $sOption . '">' . $sOption . '</option>';
                            }
                            echo '</select><br/>';
                        } elseif ($aFieldNames[$i] == "password") {
                            echo '<label>Password</label>';
                            echo '<input type="password" name="password" class="text ui-widget-content ui-corner-all" /><br/>';
                            echo '<label>Repeat Password</label>';
                            echo '<input type="password" name="repeat_password" class="text ui-widget-content ui-corner-all" /><br/>';
                        } else {
                            echo '<label>' . $aFieldLabels[$i] . '</label>';
                            echo '<input type="text" name="' . $aFieldNames[$i] . '" class="text ui-widget-content ui-corner-all" /><br/>';
                        }
                    }
                    ?>
                </fieldset>
            </form>            
        </div>

        <!--edit dialog-->
        <div id="edit_dialog" title="Edit an account">
            <form id="edit_form">
                <div id="edit_message">Please Enter</div>
                <fieldset>
                    <?php
                    for ($i = 0; $i < count($aFieldNames); $i++) {
                        if ($aFieldNames[$i] != "identity" || $this->cOpt->sSessionUserType == "admin") {
                            if (strpos($this->cOpt->aaTableStructure[$aFieldNames[$i]]["data_type"], "enum") !== FALSE) {

                                $aaMatches = array();

                                preg_match_all("/enum\((.+)\).+/", $this->cOpt->aaTableStructure[$aFieldNames[$i]]["data_type"], $aaMatches, PREG_SET_ORDER);
                                //aaMatches[0][1] is what within the enum()

                                $aOptions = explode(",", $aaMatches[0][1]);
                                echo '<label>' . $aFieldLabels[$i] . '</label>';
                                echo '<select name="' . $aFieldNames[$i] . '" class="text ui-widget-content ui-corner-all">';
                                foreach ($aOptions as &$sOption) {
                                    $sOption = trim($sOption, " '");
                                    echo '<option value="' . $sOption . '">' . $sOption . '</option>';
                                }
                                echo '</select><br/>';
                            } elseif ($aFieldNames[$i] != "password") {
                                echo '<label>' . $aFieldLabels[$i] . '</label>';
                                echo '<input type="text" name="' . $aFieldNames[$i] . '" class="text ui-widget-content ui-corner-all" /><br/>';
                            }
                        }
                    }
                    ?>
                </fieldset>
            </form>            
        </div>        
        <div id="change_password_dialog" title="Change Password">
            <form id="change_password_form">
                <div id="change_password_dialog_message">Please Enter</div>
                <fieldset>
                    <label>Old Password</label><input type="password" name="old_password"/><br/>
                    <label>New Password</label><input type="password" name="password"/><br/>
                    <label>Repeat New Password</label><input type="password" name="repeat_password"/><br/>                    
                </fieldset>    

            </form>
        </div>        

        <?php
        ;
    }

}

$cPage = new UIRaster();
$cPage->Html();
?>
