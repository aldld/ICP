<?php
session_start();

class Auth {
	
	public static function loggedIn() {
		return isset($_SESSION['user']);
	}
	
	public static function verifyUser($username, $password) {
		
	}
	
	public static function passwordHash($username, $password) {
		return sha1(sha1($username) . sha1($password));
	}
	
}
