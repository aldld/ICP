<?php

require_once 'include/database.php';
require_once 'include/user.php';

session_start();

class Auth {
	
	private static $db;
	
	public static function initialize() {
		self::$db = Database::getInstance();
	}
	
	public static function loggedIn() {
		return isset($_SESSION['user']);
	}
	
	/*
	 * Returns the ID of a user with the given username. Returns null if user not found.
	 */
	public static function getUserID($username) {
		$data = array('username' => strtolower($username));
		
		$stmt = self::$db->prepare('SELECT id FROM user WHERE username=:username');
		$stmt->execute($data);
		
		if ($stmt->rowCount() > 0) {
			$stmt->setFetchMode(PDO::FETCH_OBJ);
			$row = $stmt->fetch();
			
			return $row->id;
		}
		
		return null;
	}
	
	/*
	 * Returns an instance of User based on the ID given. Returns null if user not found.
	 */
	public static function getUser($id) {
		$data = array('id' => (int) $id);
		
		$stmt = self::$db->prepare(
			'SELECT id, username, email, fullName, instructor, cas FROM user
			WHERE id=:id LIMIT 1'
		);
		$stmt->execute($data);
		
		if ($stmt->rowCount() > 0) {
			$stmt->setFetchMode(PDO::FETCH_OBJ);
			$row = $stmt->fetch();
			
			$user = new User();
			
			$user->id           = $row->id;
			$user->username     = $row->username;
			$user->email        = $row->email;
			$user->fullName     = $row->fullName;
			$user->isInstructor = (bool) $row->instructor;
			$user->cas          = (bool) $row->cas;
			
			return $user;
		}
		
		return null;
	}
	
	/*
	 * Checks the given username and password against the database.
	 * Returns true on success, false on failure.
	 */
	public static function verifyUser($username, $password) {
		$data = array(
			'username' => htmlentities(strtolower($username)),
			'password' => self::passwordHash($username, $password)
		);
		
		$stmt = self::$db->prepare('SELECT 1 FROM user WHERE username=:username AND password=:password LIMIT 1');
		$stmt->execute($data);
		
		return $stmt->rowCount() == 1;
	}
	
	/*
	 * Hashes a password for storage in the database, using the
	 * given username as a hash.
	 */
	public static function passwordHash($username, $password) {
		return sha1(sha1(strtolower($username)) . sha1($password));
	}
	
	/*
	 * Creates a new user.
	 * Returns true on success, false on failure.
	 * Assumes that the user *can* be created in the first place.
	 */
	public static function createUser($username, $password, $email, $fullName, $cas) {
		$data = array(
			'username' => htmlentities(strtolower($username)), // Sanitize username
			'password' => self::passwordHash($username, $password), // Hash password
			'email'    => $email, // Emails SHOULD be case-sensitive
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
		$data = array('username' => strtolower($username));
		
		$stmt = self::$db->prepare('SELECT 1 FROM user WHERE username=:username LIMIT 1');
		$stmt->execute($data);
		
		return $stmt->rowCount() >= 1;
	}
	
	/*
	 * Checks if a given email address is already in use.
	 */
	public static function emailInUse($email) {
		$data = array('email' => $email);
		
		$stmt = self::$db->prepare('SELECT 1 FROM user WHERE email=:email LIMIT 1');
		$stmt->execute($data);
		
		return $stmt->rowCount() >= 1;
	}
	
}
Auth::initialize();
