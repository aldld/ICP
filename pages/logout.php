<?php
// Logout handler

if (Auth::loggedIn()) {
	unset($_SESSION['user']);
	session_destroy();
}

header('Location: ' . BASE_URL);
