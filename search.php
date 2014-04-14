<?php
/************************************************
	The Search PHP File
************************************************/


/************************************************
	MySQL Connect
************************************************/

// Credentials
require_once("db_cred.php");

//	Connection
global $tutorial_db;

$tutorial_db = new mysqli();
$tutorial_db->connect($dbhost, $dbuser, $dbpass, $dbname);
$tutorial_db->set_charset("utf8");

//	Check Connection
if ($tutorial_db->connect_errno) {
    printf("Connect failed: %s\n", $tutorial_db->connect_error);
    exit();
}

/************************************************
	Search Functionality
************************************************/

// Define Output HTML Formating
$html = '';
$html .= '<li class="result">';
$html .= '<a href="urlString">';
$html .= '<h3>nameString</h3>';
$html .= '<h4>nameEngString</h4>';
$html .= '<h4>functionString</h4>';
$html .= '</a>';
$html .= '</li>';

// Get Search
$search_string = preg_replace("/[^A-Za-z0-9]/", " ", $_POST['query']);
$search_string = $tutorial_db->real_escape_string($search_string);

// Check Length More Than One Character
if (strlen($search_string) >= 1 && $search_string !== ' ') {
	// Build Query
	$query = 'SELECT * FROM tbl_muscles WHERE lat_name LIKE "%'.$search_string.'%" OR name LIKE "%'.$search_string.'%" OR origo LIKE "%'.$search_string.'%" OR insertio LIKE "%'.$search_string.'%" OR functio LIKE "%'.$search_string.'%"';

	// Do Search
	$result = $tutorial_db->query($query);
	while($results = $result->fetch_array()) {
		$result_array[] = $results;
	}

	// Check If We Have Results
	if (isset($result_array)) {
		foreach ($result_array as $result) {

			// Format Output Strings And Hightlight Matches
			$display_function = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['functio']);
			$display_name = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['lat_name']);
			$display_engname = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['name']);
			
			// This must be changed if file name for the muscle detail file is changed
			$display_url = 'muscle.php?m='.urlencode($result['id']);

			// Insert Name
			$output = str_replace('nameString', $display_name, $html);
			
			// Insert English Name
			$output = str_replace('nameEngString', $display_engname, $output);

			// Insert Function
			$output = str_replace('functionString', $display_function, $output);

			// Insert URL
			$output = str_replace('urlString', $display_url, $output);

			// Output
			echo($output);
		}
	} else {

		// Format No Results Output
		$output = str_replace('urlString', 'index.php', $html);
		$output = str_replace('nameString', '<b>No Results Found.</b>', $output);
		$output = str_replace('<h4>nameEngString</h4>', '', $output);
		$output = str_replace('functionString', 'Sorry :(', $output);

		// Output
		echo($output);
	}
}

?>