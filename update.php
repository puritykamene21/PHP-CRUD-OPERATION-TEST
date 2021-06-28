<?php
	$title = "Update selected User Account";
	include("header.php");  // load head content	
	include("database_connection.php"); // load config info
	include("functions.php"); // load functions	
?>
<body>
 
 <!--containerfor body components -->
 <div class="container">
 	
	<div class="page-header">
 		<h1>Update User Account</h1>
 	</div>

 	 <!--Form  update inputs validation -->
        <div id="form-processing">
    <?php
     
     $ID = isset($_GET['ID']) ? $_GET['ID'] : ""; //check for selected ID visible on url

     $query = selectQuery('user_accounts', ['ID', 'First_Name', 'Last_Name', 'Email_Address', 'Phone_Number', 'Password'], ['ID'], [':id'], [$ID], null); // table, fields, criteria, placeholders, values, limit

               $First_Name = $First_NameErr = $Last_Name = $Last_NameErr = $Email_Address = $Email_AddressErr = $Phone_Number = $Phone_NumberErr = $Password = $PasswordErr = $Cpassword = $CpasswordErr = $PasswordMatchErr = ""; // define the variables

               if ($_SERVER["REQUEST_METHOD"] == "POST"){

            //data cleaning
               //process First Name
 					if(isset($_POST["First_Name"])){
 						$First_Name = sanitize($_POST["First_Name"]);
 					}else{
 						$First_NameErr = "<p class='text-danger'>*First Name is required*</p>"; 
 					}
                
                //process Last Name
 					if(isset($_POST["Last_Name"])){
 						$Last_Name = sanitize($_POST["Last_Name"]);
 					}else{
 						$Last_NameErr = "<p class='text-danger'>*Last Name is required*</p>"; 
 					}

 			    //process Email Address
                    if (isset($_POST["Email_Address"])) {
                    	$Email_Address = sanitize($_POST["Email_Address"]);
                    }else{
                    	$Email_AddressErr = "<p class='text-danger'>*Email Address is required*</p>";
                    }

 			    //process Phone Number
                    if (isset($_POST["Phone_Number"])) {
                    	$Phone_Number = sanitize($_POST["Phone_Number"]);
                    }else{
                    	$Phone_NumberErr = "<p class = 'text-danger'>* Phone Number is required*</p>";
                    }

                //process Password
                    if (!isset($_POST["Password"])) {
                    	$PasswordErr = "<p class = 'text-danger'>* Password is required*</p>";
                    }else if (!isset($_POST["Cpassword"])) {
                    	$CpasswordErr = "<p class = 'text-danger'>* Confirm Password is required*</p>";
                    }else{
                    	$Password = $_POST["Password"];
                    	$Cpassword = $_POST["Cpassword"];

                    	if ($Password !== $Cpassword) { //process password match
                    		
                    		$PasswordMatchErr = "<p class='text-danger'>Password and Confirm Password Should Match</p>";
	 						$PasswordErr = $CpasswordErr = $Password = $Cpassword = ""; // clear both passwords and password errors	
                    	}else{
                    		$Password = sanitize($_POST["Password"]);
                    		$PasswordErr = checkPassword($Password); // test password strength as stated in functions files
                    		if (!empty($PasswordErr)) {
                    			$Password = $Cpassword = "";
                    		}
                    	}
                    }

                    //check if data cleaning process returned values
                    if (!empty($First_Name) && !empty($Last_Name) && !empty($Email_Address) && !empty($Phone_Number) && empty($PasswordMatchErr)) {
                    	//process form values by entering into database table
                    	$Password = md5($Password); //MD5 encryption

                    	$ID = $ID = isset($_GET['ID']) ? $_GET['ID'] : "";

                    	$query = updateQuery('user_accounts', ['First_Name', 'Last_Name', 'Email_Address', 'Phone_Number', 'Password'], [':First_Name', ':Last_Name', ':Email_Address', ':Phone_Number', ':Password'], ['ID'], [':id'], [$ID], [$First_Name, $Last_Name, $Email_Address, $Phone_Number, $Password]); // table, fields, placeholders, criteria, criteria placeholders, criteria values, valuess

                    	if ($query>0) {

                    		echo '<script>window.location.replace("index.php?id=success");</script>';
                        }else{

                            echo 'Failed to Update Account Details';
                        }      
                    } 
                    else{
                        echo "<p class='text-danger'>Please fill in all required fields appropriately</p>";
                    }

               }
        	?>
        </div>

 	<!-- start of form update input fields -->
        <form method="POST" action="<?=$_SERVER['PHP_SELF'] ."?ID={$ID}" ;?>">

        	<?php
        	if (is_array($query)) {
        		foreach ($query as $option) {

        	?>

			<div class="form-group">
				<input type="hidden" name="ID" value="<?php echo" ".$option["ID"]." "?>">
			</div>

        	<div class="form-group">
        		<label for="First_Name"><?=(isset($First_NameErr) && !empty($First_NameErr))?$First_NameErr:"First Name:";?></label>
        		<input type="text" name="First_Name" id="First_Name" class="form-control" required="" value="<?php echo" ".$option["First_Name"]." "?>">
        	</div>

        	<div class="form-group">
        		<label for="Last_Name"><?=(isset($Last_NameErr) && !empty($Last_NameErr))?$Last_NameErr:"Last Name:";?></label>
        		<input type="text" name="Last_Name" id="Last_Name" class="form-control" required="" value="<?php echo" ".$option["Last_Name"]." "?>">
        	</div>

        	<div class="form-group">
        		<label for="Email_Address"><?=(isset($Email_AddressErr) && !empty($Email_AddressErr))?$Email_AddressErr:"Email Address:";?></label>
        		<input type="email" name="Email_Address" id="Email_Address" class="form-control" required="" value="<?php echo" ".$option["Email_Address"]." "?>">
        	</div>

        	<div class="form-group">
        		<label for="Phone_Number"><?=(isset($Phone_NumberErr) && !empty($Phone_NumberErr))?$Phone_NumberErr: "Phone Number:";?></label>
        		<input type="tel" name="Phone_Number" id="Phone_Number" class="form-control" required="" value="<?php echo" ".$option["Phone_Number"]." "?>">
        	</div>

        	<div class="form-group">
        		<label for="Password"><?=(isset($PasswordErr) && !empty($PasswordErr))?$PasswordErr:"Password:";?></label>
        		<input type="password" name="Password" id="Password" class="form-control" required="" value="<?php echo" ".$option["Password"]." "?>">
        	</div>

	 		<div class="form-group">
	 			<label for="Cpassword"><?=(isset($CpasswordErr) && !empty($CpasswordErr))?$CpasswordErr:"Confirm Password:";?></label>
	 			<input type="password" name="Cpassword" id="Cpassword" class="form-control" required="" value="<?php echo" ".$option["Password"]." "?>">
	 		</div>
            
            <?=isset($PasswordMatchErr)?$PasswordMatchErr:"";?>

            <button type="submit" class="btn btn-success btn-lg">Update Account</button>
            <a href='index.php' class='btn btn-danger btn-lg'>Read Database Records</a> 
	 			<?php  
	                 }
                  } 	
                ?>

        </form> <!-- end of account details form-->

 </div><!--end of body container-->
</body>
</html>