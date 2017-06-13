<?php
include_once '_extends.php';
include_once '_ui_display.php';

class UIHome extends UIDisplay {

    public function __construct() {
        $this->cOpt = new OperateTables();
    }

    protected function htmlHead() {
        parent::htmlHead();
        ?>

<script type="text/javascript" charset="utf-8">   
    $(function(){
        $("#homeimg").effect("bounce", "slow");
    });
</script>
<?php
    }
    protected function htmlBody() {
        parent::htmlBody();
        ?>
        <div id="homeimg"><img src="css/images/home.png"></div>
        <?php
    }

}

$cPage = new UIHome();
$cPage->Html();
?>