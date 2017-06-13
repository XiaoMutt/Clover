<?php
include_once '_table_stocks.php';
include_once '_ui_db_display.php';

abstract class UIImport extends UIDisplay {

    protected abstract function addDatabaseLabel();

    public function __construct() {
        $this->cOpt->getFields($this->aFields, "label", "edit");
        $this->cOpt->getFields($this->aFieldNames, "name", "edit");
        $this->cOpt->getFields($this->aFieldLabels, "label", "edit");
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

    protected function htmlHead() {
        parent::htmlHead();
        ?>
        <script type="text/javascript" charset="utf-8">
            var iColCount =<?php echo count($this->aFieldLabels); ?>;//The number of columns;
            var aColNames = new Array();
            var sName; //column name sent through ajax;
            var aValue = new Array();//values sent through ajax  
            var oTable; //datatable object;
            var aData = new Array(); //Data;
            var sServerSide = "<?php echo $this->sRecordAjaxProcessing ?>"; //server side processing php file name;

        <?php
        foreach ($this->aFieldNames as &$sNames) {
            echo 'aColNames.push("' . $sNames . '");';
        }
        ?>

            //format add dialog;
            function fnFormatAddDialog() {
                $('#add_message').html("Please Enter");
                $("#add_form input").val("");
                $("#add_dialog").dialog("open");
            }


            function fnAddOneRecord() {
                var aData = new Array();
                aData.push('<img src="icons/cross.png" name="action_delete" title="Delete this record">');
                if ($('#add_form input').length > 0) {
                    $('#add_form input').each(function() {
                        aData.push($(this).val());
                    });
                }
                if ($('#add_form select').length > 0) {
                    $('#add_form select').each(function() {
                        aData.push($(this).val());
                    });
                }
                $('#data_table').dataTable().fnAddData(aData);
            }

            function fnAddRecords() {
                var sImport = $('#import_text').val();
                aImport = sImport.split(/\n\r|\n/);
                var aImportData = new Array();
                for (index in aImport) {
                    sLine = aImport[index];
                    sLine = sLine.replace(/^\s+|\s+$/g, '');
                    if (sLine.length) {
                        aImportData = sLine.split("\t");
                        if (aImportData.length > iColCount) {
                            alert("There are errors in the imported data");
                            return false;
                        }
                        else {
                            while (aImportData.length < iColCount) {
                                aImportData.push('');
                            }
                        }
                        aImportData.unshift('<img src="icons/cross.png" name="action_delete" title="Delete this record">');
                        $('#data_table').dataTable().fnAddData(aImportData);
                    }
                }
            }

            function AjaxSubmit(index) {
                $.post(
                        sServerSide,
                        {action: "add", keys: sName, values: aValue[index]},
                function(data) {
                    if (data["changed"] == "0") {
                        fnMessenger("warning", "Operation failed at server side!");
                    }
                    else {
                        index++;
                        if (index < aValue.length) {
                            AjaxSubmit(index);
                        }
                        else {
                            fnMessenger("OK", "Record submited.");
                            oTable.fnClearTable();
                        }
                    }
                },
                        "json"
                        ).error(function() {
                    fnMessenger("error", "Server error!");
                });
            }



            function fnSubmitRecords()
            {

                var aaTableData = oTable.fnGetData();
                var err = false;
                if (aaTableData.length)
                {
                    var aaData = new Array();
                    var iLength = aaTableData.length;
                    var sValue;
                    var iCount = 0;
                    var iBlockNumber = 200;//the maximum record per ajax call.
                    var iBlockSize = 10240;//the maximum length per ajax call.
                    for (index in aaTableData) {//delete the action element;
                        aaTableData[index].shift();
                    }

                    sName = array2json(aColNames);

                    for (var index = 0; index < iLength; index++) {
                        iCount++;
                        aaData.push(aaTableData[index]);
                        if (iCount % iBlockNumber == 0 || index + 1 == iLength) {//divide large data sets to small parts;
                            sValue = array2json(aaData);

                            while (sValue.length > iBlockSize) {
                                index--;
                                if (index < 0) {
                                    alert("The records you entered are two large to send to server!");
                                    err = true;
                                    break;
                                }
                                aaData.pop();
                                sValue = array2json(aaData);
                            }
                            if (!err) {//sValue is small enough for ajax
                                aValue.push(sValue);
                                aaData.length = 0;
                            }
                            else {
                                break;
                            }
                        }
                    }

                    if (!err && aValue.length) {
                        fnMessenger("waiting", "Contacting server...");
                        AjaxSubmit(0);
                    }
                    else {
                        fnMessenger("warning", "The data you entered have problems!");
                    }
                }
            }


            $(function() {
                //prepare datatable;
                oTable = $("#data_table").dataTable({
                    "aoColumnDefs": [
                        {"bSortable": false, "aTargets": [0]}
                    ],
                    "aaSorting": [[1, "asc"]],
                    "sPaginationType": "full_numbers",
                    "bServerSide": false,
                    "bProcessing": true,
                    "bJQueryUI": true
                });

                //prepare add_dialog;
                $('#add_dialog').dialog({
                    autoOpen: false,
                    width: 300,
                    modal: true,
                    buttons: {
                        "OK": function() {
                            fnAddOneRecord();
                            $(this).dialog("close");
                        },
                        "Cancel": function() {
                            $(this).dialog("close");
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
                            fnUpdateRecord();
                        },
                        "Cancel": function() {
                            $(this).dialog("close");
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
                            fnDeleteRecords();
                            $(this).dialog("close");
                        },
                        "Cancel": function() {
                            $(this).dialog("close");
                        }
                    }
                });
                //prepare import_dialog;
                $('#import_dialog').dialog({
                    autoOpen: false,
                    width: 700,
                    modal: true,
                    buttons: {
                        "OK": function() {
                            fnAddRecords();
                            $(this).dialog("close");

                        },
                        "Cancel": function() {
                            $(this).dialog("close");
                        }
                    }
                });

                $('#data_table tbody td img').on('click', function() {
                    nTr = this.parentNode.parentNode;
                    if (this.name == "action_delete")//delete icon;
                    {
                        $("#delete_confirmation_dialog").dialog("open");
                    }
                });

                //open add dialog
                $('#add').on('click', function() {
                    fnFormatAddDialog();//clean the edit_dialog and show;
                });

                $('#import_button').on('click', function() {
                    $('#import_text').val("");
                    $('#import_dialog').dialog("open");
                });
                $('#submit_button').click(function() {
                    fnSubmitRecords();
                });

            })

        </script>                           
        <?php
    }

    protected function addButtons() {
        ?>
        <img src="icons/add.png" id="add"/>
        <!--Import button-->
        <img src="icons/basket_put.png" id="import_button" title="Import tab divided table">

        <button id="submit_button">Import the following data to</button>
        <?php
        $this->addDatabaseLabel();
    }

    protected function htmlBody() {
        parent::htmlBody();
        ?>

        <!--delete dialog-->
        <div id="delete_confirmation_dialog" title="Delete Confirmation"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Do you really want to delete this record?</p></div>

        <div id="container">
            <div class="table_jui">
                <!--Add button-->
                <div style="float: left; width: min-content">
                    <?php $this->addButtons(); ?>
                </div>
                <div style="float: right; width: min-content">
                    <a href="CloverDataImportTemplate.zip" download="CloverDataImportTemplate.zip">Download Data Import Template</a>
                </div>
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
                </table>
            </div> 
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

        <div id="import_dialog" title="Import Text">
            <label>Please paste the tab spaced content below. <br/>NOTE: Long processing time is needed for large text.</label><br/>
            <textarea id="import_text"></textarea>
        </div>         
        <?php
    }

}
?>
