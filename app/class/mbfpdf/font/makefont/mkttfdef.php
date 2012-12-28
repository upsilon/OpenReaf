<?php
// ------------------------------------------------------------------------
// Make mbttfdef.php
// ------------------------------------------------------------------------

// Multi Byte TrueType Font Define ----------------------------------------
$ttfdef = "../mbttfdef.php";

// Data File --------------------------------------------------------------
$temp = "mkttfdef.dat";

// Maked file by ttf2pt1.exe  ---------------------------------------------
$work = "work.afm";

// load Data File to Array ------------------------------------------------
$fp=@fopen($temp,"r");
if ($fp != false) {
    while ($line = fgets($fp)) {
        $line = rtrim($line);
        list($key,$ut,$up,$value) = explode(",",$line);
        $font[$key] = "$ut,$up,$value";
    }
    fclose($fp);
}

// Get FontName,UT,UP and Character Width ---------------------------------
$fp=@fopen($work,"r");
if ($fp != false) {
    while ($line = fgets($fp)) {
        $line = rtrim($line);
        if (preg_match('/^FontName ([A-Za-z0-9_-]*)$/',$line,$regs)) $f = $regs[1];
        if (preg_match('/^UnderlineThickness ([A-Za-z0-9_-]*)$/',$line,$regs)) $ut = $regs[1];
        if (preg_match('/^UnderlinePosition ([A-Za-z0-9_-]*)$/',$line,$regs)) $up = $regs[1];
        if (preg_match('/^C ([0-9]+) ; WX ([0-9]+) ; N [A-Za-z.]+ ; B [0-9-]+ [0-9-]+ [0-9-]+ [0-9-]+ ;/',$line,$regs)) {
            $no = $regs[1]; $width = $regs[2];
            if ($no >= 32 and $no <= 126) {
                if ($no != 32) $d .= "/";
                $d .= $width;
            }
        }
    }
    fclose($fp);
    $font[$f] = "$ut,$up,$d";
}

// Save Array to Data File ------------------------------------------------
$fp=fopen($temp,"w");
foreach($font as $key => $value) {
    fputs($fp,"$key,$value\n");
}
fclose($fp);

// Save Array to mbttfdef.php ---------------------------------------------
$fp=fopen($ttfdef,"w");
fputs($fp,"<?php\n\n");
fputs($fp,"// Multi Byte TrueType Font Define ----------------------------------------\n\n");
foreach($font as $key => $value) {
    list($ut,$up,$width) = explode(",",$value);
    $buf  = "\$MBTTFDEF['".$key."'] = array (\n";
    $buf .= "    " . str_pad("'ut'=>$ut",10) . ",";
    $buf .= str_pad("'up'=>$up",10) . ",'cw'=>array (";
    $data = array ( );
    $data = explode("/",$width);
    $acnt = count($data);
    $cnt = 0;
    foreach($data as $i => $w) {
        if ($cnt == 0) { $buf .= "\n    "; }
        $cnt++;
        $buf .= str_pad("'".addslashes(chr($i+32))."'=>$w",10);
        if ($i != $acnt-1)   { $buf .= ','; }
        if ($cnt == 7) { $cnt = 0; }
    }
    $buf .= ")\n);\n\n";
    fputs($fp,$buf);
}
fputs($fp,"?>");
fclose($fp);

?>
