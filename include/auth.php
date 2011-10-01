<?php

require_once 'include/database.php';

class Auth {
	
	private static $db;
	
	public static function initialize() {
		self::$db = Database::getInstance();
	}
	
	public static function loggedIn() {
		return isset($_SESSION['user']);
	}
	
	public static function verifyUser($username, $password) {
		
	}
	
	public static function passwordHash($username, $password) {
		return sha1(sha1($username) . sha1($password));
	}
	
	/*
	 * Creates a new user.
	 * Returns true on success, false on failure.
	 * Assumes that the user *can* be created in the first place.
	 */
	public static function createUser($username, $password, $email, $fullName, $cas) {
		$data = array(
			'username' => $username,
			'password' => self::passwordHash($username, $password), // Hash password
			'email'    => $email,
			'fullName' => $fullName,
			'cas'      => $cas
		);
		
		$stmt = self::$db->prepare(
			'INSERT INTO user (username, password, email, fullName, instructor, cas)
			VALUES (:username, :password, :email, :fullName, 0, :cas)');
		
		return $stmt->execute($data);
	}
	
	/*
	 * Checks if a given username exists or not.
	 */
	public static function userExists($username) {
		
	}
	
	/*
	 * Checks if a given email address is already in use.
	 */
	public static function emailInUse($email) {
		
	}
	
}
Auth::initialize();
