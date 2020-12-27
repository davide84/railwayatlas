<?php

	// security pig
	session_start();

	// database access
	include 'sql_conn.php';

	// useful functions
	include 'libraryFNE.php';

	// codex
	//$codex	= $_GET['q'];
	$country	= '01'; //"".substr($codex, 0, 2);
	$region		= '01'; //"".substr($codex, 2, 2);
	$visual		= '02'; //"".substr($codex, 4, 2);
	$izoom		= $_GET['izoom']; if ($izoom=='') { $izoom=10; }
	$line           = $_GET['line'];
        $c_lat          = $_GET['c_lat'];
        $c_lon          = $_GET['c_lon'];
        $type           = $_GET['type']; if ($type=='') { $type='sat'; }

	// centering coordinates
	if ($c_lat=='' || $c_lon=='') {
		// priority to manual centering
		if ($obj!='') {
			// centering map on an object
			$coordinates = getCoordinates('object',$obj,$conn);
			$c_lat = $coordinates['lat'];
			$c_lon = $coordinates['lon'];
		} else {
			if ($line!='') {
				// centering map on the center of a line
				$coordinates = getCoordinates('line',$line,$conn);
				$c_lat = $coordinates['lat'];
				$c_lon = $coordinates['lon'];
			} else {
				if ($region!='00') {
					$coordinates = getCoordinates('region',$region,$conn);
					$c_lat = $coordinates['lat'];
					$c_lon = $coordinates['lon'];
				} else {
					$c_lat = 45.876;
					$c_lon = 12.304;
				}
			}
		}
	}

function getCoordinates($type,$id,$link) {
	$query = "SELECT lat,lon FROM maps_".$type."s WHERE id=".$id.";"; 
	$row = mysql_fetch_array(mysql_query($query,$link));
	$ll['lat'] = $row['lat'];
	$ll['lon'] = $row['lon'];
	return $ll;
}

function getStations($line,$country,$region,$visual,$viewlevel,$link) {
	if ($line=='') {
		$query = "SELECT * FROM maps_objects WHERE (type=1 OR type=2) AND view=".$viewlevel;
		$query .= " AND country=".$country;
		$query .= " AND (region=1 OR region=2)";
		//if ($visual!="04") { $query .= " AND value1!=0"; }
		$query.= ";";
	} else {
		$query = "SELECT stations FROM maps_lines WHERE id=".$line.";";
		$row = mysql_fetch_array(mysql_query($query,$link));
		preg_match_all("/\d+/", $row['stations'], $stazioni, PREG_SET_ORDER);
		$query = "SELECT * FROM maps_objects WHERE (type=1 OR type=2) AND view=".$viewlevel." AND (1=2";
		foreach ($stazioni as &$id) { $query .= " OR id=".$id[0]; }
		$query .= ");";
	}
	$result = mysql_query($query,$link);
	while ($row = mysql_fetch_array($result)) {
		echo "\tvar image = new google.maps.MarkerImage('icons/".setIcon($visual,$row['type'],$row['value1'],$row['value2']).".png', new google.maps.Size(17, 17), new google.maps.Point(0,0), new google.maps.Point(9, 9));\n";
		echo "\tvar obj_ll = new google.maps.LatLng(".$row['lat'].",".$row['lon'].");\n";
		echo "\tbatch.push(new google.maps.Marker({ position: obj_ll, icon: image,";
		echo " title: \"".$row['name'];
		if ($row['value1']!=0) {
			switch($row['value2']) {
				case 2: { echo " (dismessa/incompiuta)"; } break;
				case 3: { echo " (lavori in corso)"; } break;
				case 4: { echo " (nessun servizio viaggiatori)"; } break;
				}
			} else { echo " (dismessa/incompiuta)"; }
		echo "\", zIndex: ";
		switch($row['value2']) {
				case 1: { echo "3"; } break;
				case 2: { echo "0"; } break;
				case 3: { echo "1"; } break;
				case 4: { echo "2"; } break;
			}
		echo " }));\n";
	}  // end while
}
?>

<?php
	// security pig
	if($_SESSION['am_a_map']=='yes') {
//	if(1==1) {
		$_SESSION['am_a_map']='no';
?>

function getSmallStationMarkers() {
	var batch = [];
<?php getStations($line,$country,$region,$visual,4,$conn); ?>
	return batch;
}

function getMediumStationMarkers() {
	var batch = [];
<?php getStations($line,$country,$region,$visual,3,$conn); ?>
	return batch;
}

function getBigStationMarkers() {
	var batch = [];
<?php getStations($line,$country,$region,$visual,2,$conn); ?>
	return batch;
}

function getHugeStationMarkers() {
	var batch = [];
<?php getStations($line,$country,$region,$visual,1,$conn); ?>
	return batch;
}

function setupStationMarkers() {
	mgr = new MarkerManager(map);
	google.maps.event.addListener(mgr, 'loaded', function(){
		mgr.addMarkers(getSmallStationMarkers(), 11);
		mgr.addMarkers(getMediumStationMarkers(), 9);
		mgr.addMarkers(getBigStationMarkers(), 8);
		mgr.addMarkers(getHugeStationMarkers(), 2);
		mgr.refresh();
	});
}

function initialize() {
	var latlng_center = new google.maps.LatLng(<?php echo $c_lat; ?>, <?php echo $c_lon; ?>);
	var myOptions = {
		zoom: <?php echo $izoom; ?>,
		center: latlng_center,
		<?php if ($dc==1) { echo "disableDefaultUI: true,"; } ?>
                mapTypeId: google.maps.MapTypeId.<?php
                switch ($type) {
                        case 'nav': { echo "ROADMAP"; } break;
                        case 'ter': { echo "TERRAIN"; } break;
                        case 'hyb': { echo "HYBRID"; } break;
                        default: { echo "SATELLITE"; }
                }
                ?>
                };
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

<?php
	$visualParams = setVisualParams($conn);
	getTracks($line,$country,$region,$visual,4,$visualParams,$conn); // dismesse/incompiute
	getTracks($line,$country,$region,$visual,3,$visualParams,$conn); // raccordi industriali
	getTracks($line,$country,$region,$visual,2,$visualParams,$conn); // rete complementare
	getTracks($line,$country,$region,$visual,1,$visualParams,$conn); // rete fondamentale
?>

	var listener = google.maps.event.addListener(map, 'bounds_changed', function(){
		setupStationMarkers();
		google.maps.event.removeListener(listener);
	});

}

<?php

} // end security pig

?>
