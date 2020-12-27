<?php
        // security pig
        session_start();
        $_SESSION['am_a_map'] = 'yes';
        // database access
        include 'sql_conn.php';
        // parameters
        $izoom = $_GET['izoom'];                if ($izoom=='') { $izoom = 8; }
        $sizeW = $_GET['sizeW'];                if ($sizeW=='') { $sizeW = "100%"; } else { $sizeW .= "px"; }
        $sizeH = $_GET['sizeH'];                if ($sizeH=='') { $sizeH = "100%"; } else { $sizeH .= "px"; }
        $c_lat = $_GET['c_lat'];
        $c_lon = $_GET['c_lon'];
        $type = $_GET['type'];
        $line = $_GET['line'];
        $obj = $_GET['obj'];
        $dc = $_GET['dc'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="it"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">@import url("common.css");</style>
<link rel="SHORTCUT ICON" href="images/favicon.ico">
<meta name="author" content="Davide Cester">
<meta name="keywords" content="mappe, ferrovia, italia, linee, RFI, FS">
<meta name="description" content="Mappe ferroviarie italiane.">
<meta name="abstract" content="Mappe ferroviarie italiane.">
<title>Mappe ferroviarie del NordEst</title>

<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/tags/markermanager/1.0/src/markermanager_packed.js"></script>
<script type="text/javascript" src="build_js_map.php?<?php echo htmlspecialchars(session_id()); ?>&q=<?php echo $codex; ?>&c_lat=<?php echo $c_lat; ?>&c_lon=<?php echo $c_lon; ?>&izoom=<?php echo $izoom; ?><?php
if ($line!='')  { echo "&line=".$line; }
if ($type!='')  { echo "&type=".$type; }
if ($obj!='')   { echo "&obj=".$obj; }
if ($dc!='')    { echo "&dc=".$dc; }
?>"></script>
</head>

<body onload="initialize()">
  <div id="map_canvas" style="width:<?php echo $sizeW; ?>; height:<?php echo $sizeH; ?>"></div>
</body>

</html>
