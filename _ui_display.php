<?php
include_once '_ui_templates.php';

class UIDisplay extends UITemplate {

    protected $cOpt;

    protected function htmlHead() {
        ?>
        <link rel="stylesheet" href="css/data_table_jui.css" />
        <script type="text/javascript" src="js/jquery.dataTables.min.js"></script> 
        <script type="text/javascript" charset="utf-8">   
            $(function(){
                //Remove outline from links
                $("#menu a").click(function(){
                    $(this).blur();
                });
                                                	
                //When mouse rolls over
                $("#menu ul li").mouseover(function(){
                    $(this).stop().animate({height:'150px'},{queue:false, duration:600, easing: 'easeOutBounce'})
                });
                                                	
                //When mouse is removed
                $("#menu ul li").mouseout(function(){
                    $(this).stop().animate({height:'50px'},{queue:false, duration:600, easing: 'easeOutBounce'})
                });            
                                    
                                    
            })
                            
        </script>
        <?php
    }

    protected function htmlBody() {
        ?>
        <div id="menubar">
            <div id="menu">
                <ul>
                    <li class="clover">
                        <p><a href="about.php" title="About Clover"><img style="border-width: 0" src="css/images/transparent-bar.png"/></a></p>
                        <p class="cloversubtext"><a href="mailto:zhou.210@buckeyemail.osu.edu" title="Contact Xiao Zhou">Developed by<br/>Xiao Zhou</a></p>
                    </li>                
                    <li class="green">
                        <p><a href="home.php">Home</a></p>
                        <p class="subtext">The front edge</p>
                    </li>
                    <li class="orange">
                        <p><a href="stocks.php">Stocks</a></p>
                        <p class="subtext">What do we have</p>
                    </li>
                    <li class="red">
                        <p><a href="jargons.php">Jargons</a></p>
                        <p class="subtext">Keep names short</p>
                    </li>
                    <li class="blue">
                        <p><a href="catalogs.php">Catalogs</a></p>
                        <p class="subtext">Find what you need</p>
                    </li>
                    <li class="purple">
                        <p><a href="orders.php">Orders</a></p>
                        <p class="subtext">Get what you want</p>
                    </li>
                    <li class="cyan">
                        <p><a href="sniper.php">Sniper</a></p>
                        <p class="subtext">Search primers in a haystack</p>
                    </li>
                    <li class="yellow">
                        <p><a href="roster.php">Roster</a></p>
                        <p class="subtext">Meet your friends</p>
                    </li>
                    <li class="pink">
                        <p><?php 
                        if (strlen($this->cOpt->sSessionUserName)>16){
                            echo substr($this->cOpt->sSessionUserName, 0, 16).".";
                        }
                        else{
                            echo $this->cOpt->sSessionUserName;
                        }
                        ; ?></p>
                        <p class="subtext"><a href="logout.php">Logout<br/>May I see you again</a></p>
                    </li>

                </ul>
            </div>
            <div id="menuright"></div>
        </div>
        <?php
    }

}
?>
