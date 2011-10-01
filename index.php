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
require_once 'include/page.php';

if (!Auth::loggedIn()) {
	// User is not logged in
	
	if (isset($_GET['action'])) {
		if ($_GET['action'] == 'login')
			loadPage('login');
		else
			// Redirect to signup page
			header('Location: ' . BASE_URL . 'index.php');
	} else
		loadPage('signup');
	
} else {
	// User is logged in
	echo 'You are logged in!';
	
	if (isset($_GET['action']))
		loadPage($_GET['action']);
	else
		loadPage('dashboard');
	
}
