<?php 
    //reusable functions

	// perform data cleaning using sanitize function
	function sanitize($data){
		if(is_array($data)){
			foreach ($data as $item){
				$item = trim($item); // filter out unnecessary spaces
				$item = stripslashes($item); // filter out backslashes
				$item = htmlspecialchars($item); //replacement of special characters
			}
		}else{
			$data = trim($data); // filter out  unnecessary spaces
			$data = stripslashes($data); // filter out  backslashes
			$data = htmlspecialchars($data); // replacement of special characters
		}		
		return $data;
	}

	// test password strength
	function checkPassword($password){
		if(strlen($password)<8){
			$passwordErr = "<p class='text-danger'>** Password is too short. Must be at least 8 characters **</p>";			
		}else if(!preg_match("#[0-9]+#", $password)){
			$passwordErr = "<p class='text-danger'>** Password must include at least one number **</p>";			
		}else if(!preg_match("#[a-zA-Z]+#", $password)){
			$passwordErr = "<p class='text-danger'>** Password must include at least one letter **</p>";					
		}else{
			$passwordErr = "";
		}
		return $passwordErr;
	}

	// database connection
	function connectDB(){
		try{
 			$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'")); // allow special characters to be stored and retrived from records
 			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // enable error reporting and throw exceptions
     		$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // allow the use of native prepare statements syntax	
			return $conn;
 		}catch(PDOException $e){
 			echo "Connection failed " . $e->getMessage(); // return error message also accesible in error log
 		}
	}

	// insert data to table
	function insertQuery($table, $fields = [], $placeHolders = [], $values = []){ // table, fields, placeholders and values	
		$fieldString = ''; // store the field names in a string
		foreach ($fields as $field) {
			$fieldString .= $field . ', ';
		}		
		$fieldString = rtrim($fieldString, ', '); // remove trailing ','		

		$placeHolderString = ''; // store the placeholder names in a string
		foreach ($placeHolders as $placeHolder) {
			$placeHolderString .= $placeHolder . ', '; // remove trailing ','
		}
		$placeHolderString = rtrim($placeHolderString, ', '); // remove trailing ','	

		$query = "INSERT INTO {$table} ({$fieldString}) VALUES({$placeHolderString})";	// query to run	- curly braces allow varaiables within a string

		$con = connectDB(); // connect to database

		$stmt = $con->prepare($query); // prepare query	

		$length = count($values);

		for ($i=0; $i < $length ; $i++) { // loop through the values and placeholders			
			$stmt->bindParam($placeHolders[$i], $values[$i]);
		}

		$stmt->execute(); // execute the query
		$result = $stmt->rowCount(); // return 1 if row affected

		$con = null; // end connection
		return $result;			
	}

	// checks if the parameter is an array and returns count
	function findArray($values = []){
		
	}

	// selects records from the table
	function selectQuery($table, $fields = [], $criteria = [], $placeHolders = [], $values = [], $limit = 0, $order = ''){ // table, fields, criteria, placeholders, values, limit, order  

		$fieldString = ''; // store the field names in a string
		foreach ($fields as $field) {
			$fieldString .= $field . ', ';
		}
		$fieldString = rtrim($fieldString, ', '); // remove trailing ','

		$length = count($values); // store the array length		

		$criteriaString = ''; // store the crieria in a string
		for ($i=0; $i < $length ; $i++) { // loop through the placeholders and criteria
			$criteriaString .= $criteria[$i] . ' = ' .$placeHolders[$i] . ' AND ';  // criteria string
		}
		$criteriaString = rtrim($criteriaString, ' AND '); // remove trailing ' AND'

		$con = connectDB(); // connect to database

		// check value of limit to determine select statement to run

		$query = "SELECT {$fieldString} FROM {$table} WHERE {$criteriaString}"; // preliminary query		
		$query = ($limit == null) ? $query : $query . " LIMIT $limit"; // add limit if required				
		$query = ($order == null) ? $query : $query . " ORDER BY {$order}"; // add order if required


		$stmt = $con->prepare($query); // prepare query

		for ($i=0; $i < $length ; $i++) { 
			$stmt->bindParam($placeHolders[$i], $values[$i]); // bind parameter
		}
		$stmt->execute(); // execute array
		$result = null;

		$result = ($limit === 1) ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC); // retrieves one associative array or many as required 	

		$con = null; // end connection
		return $result;
	}

		function updateQuery($table, $fields = [], $placeHolders = [], $criteria = [], $criteriaPlaceHolders = [], $criteriaValues = [], $values = []){ // table, fields, placeholders, criteria, criteria placeholders, criteria values, values
		
		$length = count($fields); // the number of fields to affect
		$fieldString = ''; // stores the fields to set
		for ($i=0; $i < $length ; $i++) { // loops through the fields and placeholders
			$fieldString .= $fields[$i].' = '.$placeHolders[$i].', ';
		}
		$fieldString = rtrim($fieldString, ', '); // remove trailing ', '

		$criteriaLength = count($criteria); // stores the number of criteria items
		$criteriaString = ''; // stores the criteria
		for ($i=0; $i < $criteriaLength; $i++) { 
			$criteriaString .= $criteria[$i].' = '.$criteriaPlaceHolders[$i]. ' AND ';
		}
		$criteriaString = rtrim($criteriaString, ' AND '); // remove trailing ' AND'

		$con = connectDB(); // connect to database

		$query = "UPDATE $table SET $fieldString WHERE $criteriaString";


		$stmt = $con->prepare($query); // prepare query 

		// bind the values
		for ($i=0; $i < $length ; $i++) { // loops through the fields and placeholders
			$stmt->bindParam($placeHolders[$i], $values[$i]); // bind parameter
		}

		for ($i=0; $i < $criteriaLength ; $i++) { // loops through the fields and placeholders
			$stmt->bindParam($criteriaPlaceHolders[$i], $criteriaValues[$i]); // bind parameter
		}

		$stmt->execute(); // execute array
		$result = $stmt->rowCount(); // return 1 if row affected

		$con = null; // end connection
		return $result;			
	}
	
	function deleteQuery($table, $fields = [], $criteria = [], $placeHolders = [], $values = []){ // table, fields, criteria,  placeholders, values

		$length = count($values); // store the array length		

		$criteriaString = ''; // store the crieria in a string
		for ($i=0; $i < $length ; $i++) { // loop through the placeholders and criteria
			$criteriaString .= $criteria[$i] . ' = ' .$placeHolders[$i] . ' AND ';  // criteria string
		}
		$criteriaString = rtrim($criteriaString, ' AND '); // remove trailing ' AND'

		$con = connectDB(); // connect to database

		// check value of limit to determine select statement to run

		$query = "DELETE FROM {$table} WHERE {$criteriaString}"; // preliminary query		
		$stmt = $con->prepare($query); // prepare query

		for ($i=0; $i < $length ; $i++) { 
			$stmt->bindParam($placeHolders[$i], $values[$i]); // bind parameter
		}

		$stmt->execute(); // execute array
		$result = $stmt->rowCount(); // return 1 if row affected

		$con = null; // end connection
		return $result;	
	}

?>