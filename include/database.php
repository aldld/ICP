<?php
if (!defined('BASEPATH')) exit('<h1>403 Forbidden</h1>');

require_once 'config.php';

/**
 * Singleton database class
 * uses MySQL via PDO
 */
class Database {
	
	// Stores the instance of the database connection
	private static $dbInstance;
	
	/*
	 * Returns only one instance of the PDO object.
	 */
	public static function getInstance() {
		if (!self::$dbInstance) {
			try {
				$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
				self::$dbInstance = new PDO($dsn, DB_USER, DB_PASS);
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
		}
		
		return self::$dbInstance;
	}
	
}
