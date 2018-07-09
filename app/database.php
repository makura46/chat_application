<?php

require("./define.php");

function DB_connection() {
	try {
		$pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
	} catch (PDOException $e) {
		echo '<p>' . $e->getMessage() . '</p>';
		exit();
	}
	return $pdo;
}

function DB_kill(&$pdo) {
	$pdo = NULL;
}
