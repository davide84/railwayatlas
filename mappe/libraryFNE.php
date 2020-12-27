<?php
// LIBRARY of useful functions

// determines a station's icon
function setIcon($visual,$type,$power,$status) {
	$icon = "";
	switch($type) {
		case 1: { $icon.="stat"; } break;
		case 2: { $icon.="stop"; } break;
		}
	if ($visual=="02") { // alimentazione
		switch($power) {
			case 0: { $icon.="_none"; } break;
			case 1: { $icon.="_dies"; } break;
			case 2: { $icon.="_3_kv"; } break;
			case 3: { $icon.="_15kv"; } break;
			}
		switch($status) {
			case 2: { $icon.="_dism"; } break;
			case 3: { $icon.="_work"; } break;
			case 4: { $icon.="_nopax"; } break;
			}
	}
	return $icon;
}

// writes a line's coordinates
function writeCoordinates($track,$link) {
	echo "\tvar trackCoordinates = [\n";
	$query = "SELECT * FROM maps_segments WHERE track=".$track." ORDER BY id;";
	$result = mysql_query($query,$link);
	$row = mysql_fetch_array($result);
	echo "\t\tnew google.maps.LatLng(".$row['lat'].",".$row['lon'].")";
	while($row = mysql_fetch_array($result)) {
		echo ",\n\t\tnew google.maps.LatLng(".$row['lat'].",".$row['lon'].")";
	}
	echo "\n\t];\n\n";
}

// adds a 'work in progress' yellow border
function writeWorkingLine($tracksnumber) {
	echo "\tvar track = new google.maps.Polyline({\n";
	echo "\t\tpath: trackCoordinates,\n";
	echo "\t\tstrokeColor: \"#FFDD00\",\n";
	echo "\t\tstrokeOpacity: 0.75,\n";
	$width = 2*$tracksnumber+6;
	echo "\t\tstrokeWeight: ".$width.",\n";
	echo "\t\tzIndex: 0\n";
	echo "\t});\n";
	echo "\ttrack.setMap(map);\n\n";
}

// sets a line in map
function writeLineDecl($tracks,$class,$visual,$visualType,$visualColor,$alpha) {
	echo "\tvar track = new google.maps.Polyline({\n";
	echo "\t\tpath: trackCoordinates,\n";
	echo "\t\tstrokeColor: \"#";
		if ($class==4) { echo "ffbbdd"; } else { echo $visualColor[$visual][$visualType]; }
	echo "\",\n";
	echo "\t\tstrokeOpacity: ";
	if ($alpha==0) { 
		if ($class==4) { $alpha = 0.75; } else { $alpha = 0.85; }
	}
	echo $alpha.",\n";
	echo "\t\tstrokeWeight: "; echo round(1.4*$tracks);
	if ($class==4) { echo ",\n\t\tzIndex: -1\n"; } else { echo ",\n\t\tzIndex: 1\n"; }
	echo "\t});\n";
	echo "\ttrack.setMap(map);\n\n";
}

function setVisualParams($link) {
	$result1 = mysql_query("SELECT id, internal_name FROM maps_visuals ORDER BY id",$link);
	while ($row1 = mysql_fetch_array($result1)) {
		$id = $row1['id']; if ($id<10) { $id = "0".$id; }
		$visual['color'][$id][0] = 'cccccc';
		$result = mysql_query("SELECT id, color FROM maps_".$row1['internal_name']." ORDER BY id",$link);
		while ($row = mysql_fetch_array($result)) {
			$visual['color'][$id][$row['id']] = "".$row['color'];
			$visual['names'][$id][$row['id']] = "".$row['name'];
		}
	}
	return $visual;
}


function getTracks($line,$country,$region,$visual,$class,$visualParams,$link) {
	if ($line=='') {
	// all lines on map
		$query = "SELECT * FROM maps_tracks WHERE";
		$query .= " country=1";
		$query .= " AND (region=1 OR region=2)";
		if ($class!=0) { $query .= " AND class=".$class; }
		$query .= " ORDER BY class DESC;";
		$result = mysql_query($query,$link);
		while($row = mysql_fetch_array($result)) {
                       switch($visual) {
                                case '01': { $visualType = $row['owner']; } break;      // owner
                                case '02': { $visualType = $row['power']; } break;      // power
                                case '03': { $visualType = $row['gabarit']; } break;    // gabarit
                                case '04': { $visualType = $row['status']; } break;     // status
                        }
 			writeCoordinates($row['id'],$link);
			if ($row['working']==1) { writeWorkingLine($row['tracks']); }
			writeLineDecl($row['tracks'],$class,$visual,$visualType,$visualParams['color'],0);
		}
	} else {
	// single-line visualization
		$query = "SELECT segments FROM maps_lines WHERE id=".$line.";";
		$row = mysql_fetch_array(mysql_query($query,$link));
		preg_match_all("/\d+/", $row['segments'], $segmenti, PREG_SET_ORDER);
		// lines not selected: all in gray
		$query = "SELECT * FROM maps_tracks WHERE"; // WHERE (class!=4 AND class!=3)";
		$query .= " country=1";
		$query .= " AND (region=1 OR region=2)";
                if ($class!=0) { $query .= " AND class=".$class; }
		foreach ($segmenti as &$id) { $query .= " AND (id!=".$id[0].")"; }
		$query .= ";";
		$result = mysql_query($query,$link);
 		while($row = mysql_fetch_array($result)) {
                       switch($visual) {
                                case '01': { $visualType = $row['owner']; } break;      // owner
                                case '02': { $visualType = $row['power']; } break;      // power
                                case '03': { $visualType = $row['gabarit']; } break;    // gabarit
                                case '04': { $visualType = $row['status']; } break;     // status
                        }
 			writeCoordinates($row['id'],$link);
                        writeLineDecl($row['tracks'],$class,$visual,$visualType,$visualParams['color'],0.25);
 		}
		// lines selected: color requested
		$query = "SELECT id,class,gabarit,power,tracks FROM maps_tracks WHERE";
                $query .= " country=1";
                $query .= " AND (region=1 OR region=2)";
                if ($class!=0) { $query .= " AND class=".$class; }
 		$query .= " AND (1=2";
		foreach ($segmenti as &$id) { $query .= " OR id=".$id[0]; }
		$query .= ");";
 		$result = mysql_query($query,$link);
 		while($row = mysql_fetch_array($result)) {
                       switch($visual) {
                                case '01': { $visualType = $row['owner']; } break;      // owner
                                case '02': { $visualType = $row['power']; } break;      // power
                                case '03': { $visualType = $row['gabarit']; } break;    // gabarit
                                case '04': { $visualType = $row['status']; } break;     // status
                        }
 			writeCoordinates($row['id'],$link);
			writeLineDecl($row['tracks'],$class,$visual,$visualType,$visualParams['color'],1);
 		}
	}
}

?>
