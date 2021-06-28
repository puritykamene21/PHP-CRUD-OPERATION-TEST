<?php
	$title = "View All Accounts";
	include("header.php");  // load head content	
	include("database_connection.php"); // load config info
	include("functions.php"); // load functions	
?>
<body>

<!--body elements container -->
<div class="container-fluid">
	 
	<?php
	//Prompt for entry deletion
    $action = isset($_GET['action']) ? $_GET['action'] : "";
 
    // on successful redirection from delete.php
    if($action=='deleted'){
          echo "<div class='alert alert-success'>Record was deleted.</div>";
          
    }

   //php select query using predefined function
   
    $status = 1;
    $query = selectQuery('user_accounts', ['ID', 'First_Name', 'Last_Name', 'Email_Address', 'Phone_Number', 'Password'], ['status'], [':status'], [$status], null, 'ID');//table, Fields, criteria, placeholder, value, limit,order

    ?>
	<div class="page-header">
	    <h3>Read All Accounts</h3>	
	</div>
    
    <div>
        <a href='create.php' class='btn btn-primary btn-lg'>Create New Account</a>
    </div>
	<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<td>User ID</td>
				<td>First Name</td>
				<td>Last Name</td>
				<td>Email Address</td>
				<td>Phone Number</td>
				<td>Password (MD5 encryption)</td>
				<td>Action</td>
			</tr>
		</thead>
		<tbody>
			<?php 

			if (is_array($query)) {

            //validating if selection is an array
			foreach ($query as $option) {
				echo 
				"<tr>
				    <td>".$option["ID"]."</td>
				    <td>".$option["First_Name"]."</td>
				    <td>".$option["Last_Name"]."</td>
				    <td>".$option["Email_Address"]."</td>
				    <td>".$option["Phone_Number"]."</td>
				    <td>".$option["Password"]."</td>
				    <td>
                    <a href='update.php?ID=".$option["ID"]."' class='btn btn-info'>Edit</a>
                    <a  href='#' onclick='delete_user(".$option["ID"].");' class='btn btn-danger'>Delete</a>
				    </td>
				</tr>";
			}

		    }
			?>
		</tbody>
	</table>
</div> <!--end of body container -->

<script type="text/javascript">
  //confirm entry deletion
  function delete_user( ID ){
    var ans = confirm('Are you sure you want to delete this account?');
    if(ans){
      //action after confirming deletion, redirected to delete query
      window.location = 'delete.php?ID=' + ID;
    }
  }
</script>

</body>
</html>