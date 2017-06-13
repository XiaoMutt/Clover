<?php

include_once '_extends.php';

class Sniper extends Connect2Clover {

    private $array_h = Array();
    private $array_s = Array();
    private $iPrimerConcentration;
    private $iSaltConcentration;
    private $iMgConcentration;

    public function __construct($iPrimerC, $iSaltC, $iMgC) {
        parent::__construct();

        /* read out user information */
        $rResult = $this->queryClover("SHOW TABLES LIKE 'users'");
        if ($rResult && $rResult->num_rows == 1) {
            /* start sesssion */
            if (!isset($_SESSION['user_id'])) {
                session_name($this->sSessionName);
                session_start();
            }
            if (isset($_SESSION["user_id"])) {
                $sQuery = "SELECT * FROM `users` WHERE `id`='" . $_SESSION["user_id"] . "' AND `identity`!='visitor' AND `deleted`='0'";
                $rResult = $this->queryClover($sQuery);
                if ($rResult) {
                    $aRow = $rResult->fetch_assoc();
                    if ($aRow) {
                        $this->sSessionUserEmail = $aRow["email"];
                        $this->sSessionUserName = $aRow["name"];
                        $this->sSessionUserType = $aRow["identity"];
                        $this->iSessionUserId = $_SESSION["user_id"];
                    }
                    $rResult->close();
                } else {
                    $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " Database Error: cannot obtain user information using user ID: " . $_SESSION["user_id"];
                }
            } else {
                header('Location: index.php');
            }
        }

        $this->iPrimerConcentration = $iPrimerC;
        $this->iSaltConcentration = $iSaltC;
        $this->iMgConcentration = $iMgC;

        // enthalpy values
        $this->array_h["AA"] = -7.9;
        $this->array_h["AC"] = -8.4;
        $this->array_h["AG"] = -7.8;
        $this->array_h["AT"] = -7.2;
        $this->array_h["CA"] = -8.5;
        $this->array_h["CC"] = -8.0;
        $this->array_h["CG"] = -10.6;
        $this->array_h["CT"] = -7.8;
        $this->array_h["GA"] = -8.2;
        $this->array_h["GC"] = -10.6;
        $this->array_h["GG"] = -8.0;
        $this->array_h["GT"] = -8.4;
        $this->array_h["TA"] = -7.2;
        $this->array_h["TC"] = -8.2;
        $this->array_h["TG"] = -8.5;
        $this->array_h["TT"] = -7.9;
        // entropy values
        $this->array_s["AA"] = -22.2;
        $this->array_s["AC"] = -22.4;
        $this->array_s["AG"] = -21.0;
        $this->array_s["AT"] = -20.4;
        $this->array_s["CA"] = -22.7;
        $this->array_s["CC"] = -19.9;
        $this->array_s["CG"] = -27.2;
        $this->array_s["CT"] = -21.0;
        $this->array_s["GA"] = -22.2;
        $this->array_s["GC"] = -27.2;
        $this->array_s["GG"] = -19.9;
        $this->array_s["GT"] = -22.4;
        $this->array_s["TA"] = -21.3;
        $this->array_s["TC"] = -22.2;
        $this->array_s["TG"] = -22.7;
        $this->array_s["TT"] = -22.2;
    }

    public function ComplementReversed($DNA) {
        $cNt["A"] = "T";
        $cNt["T"] = "A";
        $cNt["G"] = "C";
        $cNt["C"] = "G";
        $iLength = strlen($DNA);
        $sNew = "";
        for ($i = 0; $i < $iLength; $i++) {
            $sNew.=$cNt[substr($DNA, $i, 1)];
        }
        return strrev($sNew);
    }

    public function BaseStackingTm($sPrimer) {

        $h = $s = 0;
        // effect on entropy by salt correction; von Ahsen et al 1999
        // Increase of stability due to presence of Mg;
        $salt_effect = ($this->iSaltConcentration / 1000) + (($this->iMgConcentration / 1000) * 140);
        // effect on entropy
        $s+=0.368 * (strlen($sPrimer) - 1) * log($salt_effect);
        // terminal corrections. Santalucia 1998
        $firstnucleotide = substr($sPrimer, 0, 1);
        if ($firstnucleotide == "G" or $firstnucleotide == "C") {
            $h+=0.1;
            $s+=-2.8;
        }
        if ($firstnucleotide == "A" or $firstnucleotide == "T") {
            $h+=2.3;
            $s+=4.1;
        }

        $lastnucleotide = substr($sPrimer, strlen($sPrimer) - 1, 1);
        if ($lastnucleotide == "G" or $lastnucleotide == "C") {
            $h+=0.1;
            $s+=-2.8;
        }
        if ($lastnucleotide == "A" or $lastnucleotide == "T") {
            $h+=2.3;
            $s+=4.1;
        }

        // compute new H and s based on sequence. Santalucia 1998
        for ($i = 0; $i < strlen($sPrimer) - 1; $i++) {
            $subc = substr($sPrimer, $i, 2);
            $h+=$this->array_h[$subc];
            $s+=$this->array_s[$subc];
        }
        return ((1000 * $h) / ($s + (1.987 * log($this->iPrimerConcentration / 2000000000)))) - 273.15;
    }

    public function jsonSniper(&$sDNA, $iMinTm) {
        $aaMatchs = Array();
        $iMaxFoundNum = 1000; //maxium number returns;
        $iFoundNum = 0; //used to count matchs;
        $iMaxPrimerLength = 100;
        $iMinPrimerLength = 15;
        $sQuery = "SELECT `id`, `name`, `sequence` FROM `primer_stock` WHERE `deleted`=0";
        $rResult = $this->queryClover($sQuery);
        while ($aRow = $rResult->fetch_array(MYSQLI_ASSOC)) {
            $sPrimer = preg_replace("/\W|[^ATGC]|\d/", "", strtoupper($aRow["sequence"])); //the DNA sequence of primer;
            $iPrimerLength = strlen($sPrimer);
            if ($iPrimerLength <= $iMaxPrimerLength && $iPrimerLength >= $iMinPrimerLength) {//length restriction;
                for ($i = $iMinPrimerLength; $i <= $iMaxPrimerLength; $i++) {
                    $sTrunc = substr($sPrimer, -$i);
                    if ($this->BaseStackingTm($sTrunc) >= $iMinTm) {
                        $offset = 0;
                        while (($offset = strpos($sDNA, $sTrunc, $offset)) !== false && $iFoundNum <= $iMaxFoundNum) {
                            $offset+=strlen($sTrunc); //set next search offset;
                            $aaMatchs[$iFoundNum][] = $aRow["id"];
                            $aaMatchs[$iFoundNum][] = $aRow["name"];
                            $aaMatchs[$iFoundNum][] = $aRow["sequence"];
                            $aaMatchs[$iFoundNum][] = $offset;
                            $aaMatchs[$iFoundNum][] = "Forward";
                            $iFoundNum++;
                        }

                        $sTrunc = $this->ComplementReversed($sTrunc); //use reverse complement to find again;
                        $offset = 0;
                        while (($offset = strpos($sDNA, $sTrunc, $offset)) !== false && $iFoundNum <= $iMaxFoundNum) {
                            $aaMatchs[$iFoundNum][] = $aRow["id"];
                            $aaMatchs[$iFoundNum][] = $aRow["name"];
                            $aaMatchs[$iFoundNum][] = $aRow["sequence"];
                            $aaMatchs[$iFoundNum][] = $offset + 1;
                            $aaMatchs[$iFoundNum][] = "Reverse";
                            $offset+=strlen($sTrunc); //set next search offset;
                            $iFoundNum++;
                        }
                        break;
                    }
                }
            }
        }
        return json_encode($aaMatchs);
    }

}

$sDNA = preg_replace("/\W|[^ATGC]|\d/", "", strtoupper($_POST["DNA"]));
$cSniper = new Sniper($_POST["primerCon"], $_POST["saltCon"], $_POST["MgCon"]);
echo $cSniper->jsonSniper($sDNA, $_POST["minTm"]);
?>
