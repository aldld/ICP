<?php

/*
 * Loads the page in pages/ directory specified by $page
 */
function loadPage($page) {
	// To prevent users from accessing files outside of pages/
	if (strpos($page, '/') !== false) {
		throw new Exception('Invalid characters in URL.');
		
		return false;
	}
	
	$filename = BASE_PATH . 'pages/' . $page . '.php';
	if (file_exists($filename)) {
		require_once $filename;
		
		return true;
	} else {
		// Error 404: File not found
		header('HTTP/1.0 404 Not Found');
	}
}
