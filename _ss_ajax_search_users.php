<?php
    include_once '_table_users.php';
    $cUser=new OperateUsersTable();
    echo $cUser->AjaxSearch();
?>
