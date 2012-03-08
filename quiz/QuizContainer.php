<?php

class QuizContainer
{
	private static $db;
	
	private function __construct() {  }
	
	public static function createQuiz()
	{
		if(!isset(self::$db))
			throw new Exception('Database handle must be set!');
		
		$q = new Quiz();
		$q->setdb(self::$db);
		return $q;
	}
	
	public static function setdb(PDO $conn)
	{
		self::$db = $conn;
	}
}