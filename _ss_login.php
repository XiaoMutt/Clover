<?php

include_once '_extends.php';

class UserLogin extends Connect2Clover {

    public function Login($sEmail, $sPassword) {
        //login the user;
        //return "OK" if loged in, return error message if there is an error.
        $aRecord["email"] = $sEmail;
        $aRecord["password"] = sha256($sPassword);
        $aRecord["deleted"] = "0";
        $sQuery = "SELECT * FROM `users` WHERE ";
        $aTemp = array();
        foreach ($aRecord as $key => $value) {
            $aTemp[] = "`" . $key . "`='" . $this->real_escape_string($value) . "'";
        }
        $sQuery.=implode(" AND ", $aTemp);
        $rResult = $this->queryClover($sQuery);
        if ($rResult && $rResult->num_rows < 2) {
            if ($rResult->num_rows == 1) {
                $aRow = $rResult->fetch_assoc();
                $iId = $aRow["id"];
                $sIdentity = $aRow["identity"];
                if ($sIdentity == "visitor") {
                    return "You have a visitor account. Please contact administrators for activation.";
                } else {
                    session_name($this->sSessionName); //login
                    session_start(); //login
                    $_SESSION["user_id"] = $iId; //login;
                    return "OK";
                }
            } elseif ($rResult->num_rows == 0) {
                return "User information does not match. Please try again.";
            }
        } else {
            return "Database error. Please contact administrators.";
        }
    }

}
$cUser = new UserLogin();
$sResult = $cUser->Login($_POST["email"], $_POST["password"]);
echo json_encode($sResult);
?>
