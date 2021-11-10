<?php
	$config = parse_ini_file('../config.ini'); 
	echo $config['username'] . "<br>";
	echo $config['servername'] . "<br>";

	//setup database connection
	$conn = new mysqli($config['servername'], $config['username'], $config['password'], 'baba-is-yall');
	if(!$conn){die('BAD CONNECTION');}

	

?>