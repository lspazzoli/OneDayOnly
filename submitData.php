<?php 
	ini_set('display_errors', true);
	$firstName=$_REQUEST["Fname"];
	$lastName=$_REQUEST["Lname"];
	$email=$_REQUEST["emailAddress"];
	$id=insert_unique('mytable', array(
		'firstName' => $firstName,
		'lastName' => $lastName,
		'email' => $email
		)
	);
	if($id==false)
		die ("<p style='margin-top: -95%;font-size:30px;'>Error That Information Already Exists</p>");
	else
		die ("<p style='margin-top: -95%;font-size:30px;'>The ID Of The Submitted Data Is: ".$id."</p>");
	
	function insert_unique($table, $vars)
		{	//Connect
			$con = mysqli_connect("localhost","root","root");
			if (!$con) {
				die('Could not connect: ' . mysql_error());
				}
			// Make userinfo the current database
			$db_selected = mysqli_select_db($con,'userinfo');

			if (!$db_selected) {
			  // If we couldn't, then it either doesn't exist, or we can't see it.
			  $sql = 'CREATE DATABASE userinfo';
			  //Create the db
			  if (mysqli_query($con,$sql)) {die ("<p style='margin-top: -95%;font-size:30px;'>Error Database Had To Be Created\nPlease Resubmit Data</p>");} 
			  else {die('Error creating database: ' . mysql_error() . "\n");}
			}
			//Create table if it doesnt exist
			$sql = "CREATE TABLE IF NOT EXISTS $table (ID int NOT NULL AUTO_INCREMENT,PRIMARY KEY(ID),
			firstName VARCHAR(40) NOT NULL,lastName VARCHAR(40) NOT NULL,email VARCHAR(40) NOT NULL)";
			mysqli_query($con,$sql);
			//Insert if unique
			if (count($vars)) {
			$table = mysqli_real_escape_string($con,$table);
			$vars = array_map(function ($array_item){return $array_item;}, $vars);

			$req = "INSERT INTO `$table` (`". join('`, `', array_keys($vars)) ."`) ";
			$req .= "SELECT '". join("', '", $vars) ."' FROM DUAL ";
			$req .= "WHERE NOT EXISTS (SELECT 1 FROM `$table` WHERE ";
			
			//only deny if all fields are the same otherwise add data , 
			//I should probably check if email already exists in the table
			foreach ($vars AS $col => $val)
			  $req .= "`$col`='$val' AND ";

			$req = substr($req, 0, -5) . ") LIMIT 1";

			$res = mysqli_query($con,$req) OR die();
			return mysqli_insert_id($con);
		  }
		  else return false;
		}
?>