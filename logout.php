<?php
include_once '_ui_display.php';
include_once '_extends.php';

class Logout extends UIDisplay {
    public function __construct() {
        $this->cOpt=new OperateTables();
    }

    protected function htmlHead() {
        parent::htmlHead();
        ?>
        <script type="text/javascript" charset="utf-8">
            function fnLogout (){
                $.post(
                "_ss_logout.php",
                        
                {},
                        
                function(data) {
                    if (data=="OK"){
                        window.location.href="index.php";
                    }
                    else{
                        fnMessenger("warning", "Logout failed!");
                                
                    }
                            
                },
                        
                "json"
            ).error(function(){fnMessenger("error", "Server error!");});
                        
            }
                        
            $(function(){
                //prepare logout_dialog;
                $('#logout_dialog').dialog({
                    autoOpen: true,
                    resizable: false,
                    height: 160,
                    modal: false,
                    open: function(event, ui) { $(this).parent().find(".ui-dialog-titlebar-close").hide(); },
                    buttons: {
                        "Yes": function() {
                            fnLogout ();
                        }
                    }
                });                
            });

        </script>
        <?php
    }

    protected function htmlBody() {
        parent::htmlBody();
        ?>
        <div id="logout_dialog" title="Logout Confirmation"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Do you really want to logout?</p></div>

        <?php
    }

}

$cPage=new Logout();
$cPage->Html();
?>
