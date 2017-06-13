<?php
include_once '_ui_templates.php';

class UILogin extends UITemplate {

    protected function htmlHead() {
        ?>
        <script type="text/javascript" charset="utf-8">
            function fnLogin(){
                var sEmail=$('#login_email').val();
                var sPassword=Sha256.hash($('#login_password').val());
                $.post(
                "_ss_login.php",

                {email: sEmail, password: sPassword},

                function(msg){
                    if(msg=="OK"){
                        $('#messenger_dialog').hide();
                        $("#login_password").val("");
                        $("#login_form").submit(); //pseudo submit form
                    }
                    else{
                        $('#login_message').text(msg);
                        fnMessenger("error",msg);
                    }

                },

                "json"
            ).error(function (){fnMessenger("error","Login failed due to server connection error!")});                
            }
                            
            function fnRegister(){
                var aName =new Array();
                var aValue=new Array();
                var aaValue=new Array();
                                
                $('#register_form input').each(function(){
                    aName.push(this.id);
                    if (this.id=="password"||this.id=="repeat_password"){
                        aValue.push(Sha256.hash($(this).val()));
                    }
                    else{
                        aValue.push($(this).val());
                    }
                });
                aaValue.push(aValue);
                var sName=array2json(aName);
                var sValue=array2json(aaValue);
                $.post(
                "_ss_record_processing_users.php",
                                    
                {action: "register", keys: sName, values: sValue},
                                    
                function (data){
                    if (data["changed"]==0){
                        $('#register_message').html(data["errors"]);
                        fnMessenger("warning","Please check your input.");
                    }        
                    else{
                        $('#register_dialog').dialog("close");
                        fnMessenger("waiting", "New account added. Please contact administrators for activation.");
                    }
                                        
                },
                                    
                "json"
            ).error(function (){fnMessenger("error","Register failed due to server connection error!")});                
                                
            }
                            
            function fnFormatRegisterDialog(){
                $('#register_message').html("Please Enter");
                $('#register_form input').val("");
                $('#register_dialog').dialog("open");
            }
                            
            $(function(){
                $('#login_dialog').dialog({
                    autoOpen: true,
                    width: 300,
                    modal: false,
                    open: function(event, ui) { $(this).parent().find(".ui-dialog-titlebar-close").hide(); },
                    buttons: {
                        "OK": function() {
                            fnLogin();
                        },
                        "New User": function() {
                            fnFormatRegisterDialog();
                        }
                    }
                });
                                
                $('#register_dialog').dialog({
                    autoOpen: false,
                    width: 300,
                    modal: true,
                    buttons: {
                        "OK": function() {
                            fnRegister();
                        },
                        "Cancel": function() {
                            fnMessenger("OK", "Please Login");
                            $( this ).dialog( "close" );
                            
                        }
                    }
                }); 
                              
            }
                           
                            
        );
        </script>

        <?php
    }

    protected function htmlBody() {
        ?>
        <div id="login_dialog" title="Clover Login">
            <label id="login_message">Please Enter</label>
            <form id="login_form" action="home.php" method="post">
                <fieldset>
                    <label>Email</label><br/><input type="text" autocomplete="on" name="Email" id="login_email" class="text ui-widget-content ui-corner-all"/><br/>
                    <label>Password</label><br/><input type="password" name="Password" id="login_password" class="text ui-widget-content ui-corner-all"/><br/>
                </fieldset>
            </form>            
        </div> 
        <div id="register_dialog" title="Register New User">
            <div id="register_message">Please Enter</div>
            <form id="register_form" >
                <fieldset>
                    <label>Name</label><br/><input type="text" name="Name" id="name" class="text ui-widget-content ui-corner-all"/><br/>
                    <label>Email</label><br/><input type="text" name="Email" id="email" class="text ui-widget-content ui-corner-all"/><br/>
                    <label>Password</label><br/><input type="password" name="Password" id="password" class="text ui-widget-content ui-corner-all"/><br/>
                    <label>Repeat Password</label><br/><input type="password" name="Repeat Password" id="repeat_password" class="text ui-widget-content ui-corner-all"/><br/>
                    <label>Description</label><br/><input type="text" name="Description" id="description" class="text ui-widget-content ui-corner-all"/><br/>
                </fieldset>
            </form>            
        </div>           

        <?php
    }

}

$cPage=new UILogin();
$cPage->Html();
?>
