<?php
	include("header.php");  // load head content	
	include("database_connection.php"); // load config info
	include("functions.php"); // load functions	

    $ID = isset($_GET['ID']) ? $_GET['ID'] : '';

    $Status = 0;
	$query = updateQuery('user_accounts', ['Status'], [':status'], ['ID'], [':id'], [$ID], [$Status] ); // table, fields, placeholders, criteria, criteria placeholders, criteria values, values
	var_dump($query);
    if ($query > 0) {

	header('Location:index.php?action=deleted');

    }
  else{

  die('Unable to delete record.');
}
        
?>
