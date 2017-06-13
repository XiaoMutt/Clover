<?php
include_once '_extends.php';
include_once '_ui_display.php';

class UIAbout extends UIDisplay {

    public function __construct() {
        $this->cOpt = new OperateTables();
    }

    protected function htmlBody() {
        parent::htmlBody();
        ?>
        <div><img src="css/images/about.png"></div>
        <?php
    }

}

$cPage = new UIAbout();
$cPage->Html();
?>