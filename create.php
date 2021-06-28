<?php
	$title = "Create New Account";
	include("header.php");  // load head content	
	include("database_connection.php"); // load config info
	include("functions.php"); // load functions	
?>
<body>

	<!--body elements container -->
	<div class="container">
		
		<div class="page header">
			<h3>Create New Account</h3>		
		</div>
    <!-- form & validation for the account details -->
    
    <!--Form inputs validation -->
        <div id="form-processing">
        	<?php
               $Email_Address = $Email_AddressErr = $First_Name = $First_NameErr = $Last_Name = $Last_NameErr = $Phone_Number = $Phone_NumberErr = $Password = $PasswordErr = $Cpassword = $CpasswordErr = $PasswordMatchErr = ""; // define the variables

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
                    	$query = insertquery('user_accounts', ['First_Name', 'Last_Name', 'Email_Address', 'Phone_Number', 'Password'], [':First_Name', ':Last_Name', ':Email_Address', ':Phone_Number', ':Password'], [$First_Name, $Last_Name, $Email_Address, $Phone_Number, $Password] ); //Table namme, Fields, Placeholders, Values

                    	if ($query>0) {
                           
                            echo '<script>window.location.replace("create.php?id=success");</script>';

                        }else{

                            echo 'Failed to Create New Record';
                        }      
                    } 
                    else{
                        echo "<p class='text-danger'>Please fill in all required fields appropriately</p>";
                    }

               }
        	?>
        </div>
    <!-- start form inputt fields -->
        <form action="<?=$_SERVER['PHP_SELF'];?>" method="POST">
        	<div class="form-group">
        		<label for="First_Name"><?=(isset($First_NameErr) && !empty($First_NameErr))?$First_NameErr:"First Name:";?></label>
        		<input type="text" name="First_Name" id="First_Name" class="form-control" required="" value="<?=(isset($First_Name) && !empty($First_Name))?$First_Name:"";?>">
        	</div>

        	<div class="form-group">
        		<label for="Last_Name"><?=(isset($Last_NameErr) && !empty($Last_NameErr))?$Last_NameErr:"Last Name:";?></label>
        		<input type="text" name="Last_Name" id="Last_Name" class="form-control" required="" value="<?=(isset($Last_Name) && !empty($Last_Name))?$Last_Name:"";?>">
        	</div>

        	<div class="form-group">
        		<label for="Email_Address"><?=(isset($Email_AddressErr) && !empty($Email_AddressErr))?$Email_AddressErr:"Email Address:";?></label>
        		<input type="email" name="Email_Address" id="Email_Address" class="form-control" required="" value="<?=(isset($Email_Address) && !empty($Email_Address))?$Email_Address:"";?>">
        	</div>

        	<div class="form-group">
        		<label for="Phone_Number"><?=(isset($Phone_NumberErr) && !empty($Phone_NumberErr))?$Phone_NumberErr: "Phone Number:";?></label>
        		<input type="tel" name="Phone_Number" id="Phone_Number" class="form-control" required="" value="<?=(isset($Phone_Number) && !empty($Phone_Number))?$Phone_Number:"";?>">
        	</div>

        	<div class="form-group">
        		<label for="Password"><?=(isset($PasswordErr) && !empty($PasswordErr))?$PasswordErr:"Password:";?></label>
        		<input type="password" name="Password" id="Password" class="form-control" required="" value="<?=(isset($Password) && !empty($Password))?$Password:"";?>">
        	</div>

	 		<div class="form-group">
	 			<label for="Cpassword"><?=(isset($CpasswordErr) && !empty($CpasswordErr))?$CpasswordErr:"Confirm Password:";?></label>
	 			<input type="password" name="Cpassword" id="Cpassword" class="form-control" required="" value="<?=(isset($Cpassword) && !empty($Cpassword))?$Cpassword:"";?>">
	 		</div>
            
            <?=isset($PasswordMatchErr)?$PasswordMatchErr:"";?>

            <button type="submit" class="btn btn-success btn-lg">Save Record</button>
            <a href='index.php' class='btn btn-danger btn-lg'>Read Database Records</a> 
            
        </form> <!-- end of account details form-->

	</div> <!--end og body elements container -->

</body>
</html>