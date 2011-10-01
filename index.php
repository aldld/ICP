<?php 
/**
 * ICP website main file. All content is to be routed through index.php.
 * Pages are accessed via /index.php?action=<page>
 * 
 * Code is licensed under the MIT License. See file LICENSE for more information.
 */

// Set the include path here, to make them behave in a sane(r) manner
set_include_path(dirname(__FILE__));

require_once 'config.php';
require_once 'include/auth.php';

if (!Auth::loggedIn()) {
	// User is not logged in
	
	if (isset($_GET['action'])) {
		if ($_GET['action'] == 'login') {
			// TODO: Display login page
		} else {
			header('Location: index.php');
		}
	} else {
		// TODO: Display signup page
	}
} else {
	// User is logged in
	
	if (isset($_GET['action'])) {
		$page = $_GET['action'];
		
		// To prevent users from accessing files outside of pages/
		if (strpos($action, '/') !== false) exit();
		
		require_once 'pages/' . $page . '.php';
		echo $action;
	} else {
		// TODO: Display dashboard
	}
}
