<?php
include_once '_ui_templates.php';

class UISetup extends UITemplate {

    protected function htmlHead() {
        ?>
        <script type="text/javascript">
                            
            function fnRegister()
            {
                
                var aName =new Array();
                var aValue=new Array();
                              
                $('#register_form input').each(function(){
                    aName.push(this.id);
                    if (this.id==="password"||this.id==="repeat_password"){
                        aValue.push(Sha256.hash($(this).val()));
                    }
                    else{
                        aValue.push($(this).val());
                    }
                });
                var sName=array2json(aName);
                var sValue=array2json(aValue);
                $.post(
                "_ss_setup.php",
                                    
                {keys: sName, values: sValue},
                                    
                function (data){
                    if (data["changed"]===0){
                        $('#register_message').html(data["errors"]);
                        fnMessenger("error", "Please check your input.");
                    }        
                    else{
                        window.location.href="home.php";
                    }
                                        
                },
                                    
                "json"
            ).error(function (){fnMessenger("error","Setup failed due to server error!");});                
                                
            }
                            
            function fnFormatRegisterDialog(){
                $('#register_message').html("Please Enter");
                $('#register_form input').val("");
                $('#register_dialog').dialog("open");
            }
                            
            $('document').ready(function(){
                $('#register_dialog').dialog({
                    autoOpen: true,
                    width: 300,
                    modal: false,
                    open: function(event, ui) { $(this).parent().find(".ui-dialog-titlebar-close").hide(); },
                    buttons: {
                        "OK": function() {
                            fnRegister();
                        }
                    }
                }); 
            }
        );
        </script>    
        <?php
    }

    protected function htmlBody() {
        $whitelist = array('localhost', '127.0.0.1');

        if (!in_array(filter_input(INPUT_SERVER,'HTTP_HOST',FILTER_DEFAULT), $whitelist)) {
            echo 'Forbidden! Please visit this page using "localhost".';
        }
        ?>
        <div id="register_dialog" title="Clover Setup">
            <div id="register_message">Create an Administrator Account</div>
            <div id="register_form" action="home.php">
                <fieldset>
                    <label>Name</label><br/><input type="text" name="Name" id="name" class="text ui-widget-content ui-corner-all"/><br/>
                    <label>Email</label><br/><input type="text" name="Email" id="email" class="text ui-widget-content ui-corner-all"/><br/>
                    <label>Password</label><br/><input type="password" name="Password" id="password" class="text ui-widget-content ui-corner-all"/><br/>
                    <label>Repeat Password</label><br/><input type="password" name="Repeat Password" id="repeat_password" class="text ui-widget-content ui-corner-all"/><br/>
                    <label>Description</label><br/><input type="text" name="Description" id="description" class="text ui-widget-content ui-corner-all"/><br/>
                </fieldset>
            </div>            
        </div> 
        <?php
    }

}

$cPage=new UISetup();
$cPage->Html();
?>
