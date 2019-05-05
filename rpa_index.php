<?php
	/**
	 * @Project: Rank Promotion Automation - rpa_index.php - VAM Custom
	 * @Author.: Ton Nascoli
	 * @Version: 1.0 - 20190505
	 * - Please, if you can, help improve the code and share! - Thank you!
	 *
	 * @Project: Virtual Airlines Manager (VAM)
	 * @Web http://virtualairlinesmanager.net
	 * Copyright (c) 2019 - 2024 Alejandro Garcia
	 * VAM is licensed under the following license:
	 *   Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)
	 *   View license.txt in the root, or visit http://creativecommons.org/licenses/by-nc-sa/4.0/
	 */
?>
<?php
include('db_login.php');

$db = new mysqli($db_host , $db_username , $db_password , $db_database);
$db->set_charset("utf8");
if ($db->connect_errno > 0) {
	die('Unable to connect to database [' . $db->connect_error . ']');
}

$sql = "SELECT * FROM gvausers ORDER BY callsign ASC";
if (!$result = $db->query($sql)) {
	die('There was an error running the query [' . $db->error . ']');
}

if ($result = $db->query($sql)) {
	$qtde=0;
	while ($row = $result->fetch_assoc())
	{
		
		$pilotId=$row[gvauser_id];
			
		$sql2 = "SELECT ROUND(SUM(flight_duration),2) totalhoras FROM vampireps WHERE gvauser_id = $pilotId";

		if ($result2 = $db->query($sql2)) {

			while ($row2 = $result2->fetch_assoc())
			{		
					if ($row2[totalhoras]<>"") {											
														
						$sql3 = "SELECT rank_id, rank FROM ranks WHERE $row2[totalhoras] BETWEEN minimum_hours AND maximum_hours";
						if (!$result3 = $db->query($sql3)) {
							die('There was an error running the query [' . $db->error . ']');
						}
						$row3 = $result3->fetch_assoc();						

						$sql_upd = "UPDATE gvausers SET rank_id=$row3[rank_id] WHERE gvauser_id=$pilotId";
						if ($db->query($sql_upd) === TRUE) {
 							echo ' *** Record ('.$row[callsign].' - '.$row[name].' '.$row[surname].') verified and updated successfully *** ';
 							$qtde=$qtde+1;
						} else {
							echo " /// Error updating record: /// " . $conn->error;
						}

						echo '<br>';

					}
				
			}
		}


	}
    /* free result set */
    $result->close();
    $result2->close();
    $result3->close();
}
echo '<br>';
echo '<strong>Total Verified Records: '.$qtde.'</strong>';
?>