<?php

abstract class UITemplate {

    protected $cOpt;

    abstract protected function htmlBody();

    abstract protected function htmlHead();

    public function Html() {
        ?>
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
        <html>
            <head>
                <meta http-equiv="content-type" content="text/html; charset=utf-8" />
                <title>Clover</title>
                <link rel="icon" href="icons/clover.ico" />
                <link rel="stylesheet" href="css/jquery-ui-1.10.2.custom.min.css" /> 
                <link rel="stylesheet" href="css/page.css" />
                <script type="text/javascript" src="js/jquery-1.9.1.js"></script>
                <script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
                <script type="text/javascript" src="js/common.js"></script>
                <?php $this->htmlHead(); ?>
                <script type="text/javascript" charset="utf-8">                
                    //Messenger;    
                    function fnMessenger(sImg, sMsg) {
                        if (sImg == "OK") {
                            sImg = "icons/accept.png";
                        } else if (sImg == "waiting") {
                            sImg = "icons/waiting.gif";
                        } else if (sImg == "warning") {
                            sImg = "icons/bullet_error.png";
                        } else if (sImg == "error") {
                            sImg = "icons/cancel.png";
                        }
                        $("#messenger_dialog img") . attr("src", sImg);
                        $("#messenger_dialog label") . text(sMsg);
                        $("#messenger_dialog") . show();
                        if (sImg == "icons/accept.png") {
                            $("#messenger_dialog") . fadeOut(3000);
                        }
                    }                                  

                    $(function() {
                        $("#messenger_dialog").hide();
                                        
                        
                        $('[id*="_dialog"]').on('keyup', function(e){
                            if (e.keyCode == 13) {
                                $(this).parent().find('button:contains("OK")').click();
                            }
                        });                    
                    });                    
                </script>

            </head>
            <body>
                <?php $this->htmlBody(); ?>
                <div id="messenger_dialog"><img/><label></label></div>   
            </body>
            <?php
            ;
        }

    }
    ?>