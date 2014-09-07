
<?php
	include("db_connection.php");
	//mail("haris005@gmail.com", "Hello", "Hello World", "From: haris005@gmail.com");
	
	if(isset($_POST['rsubmit'])){
		// form was submitted
		$username = mysqli_real_escape_string($link, $_POST['rusername']);
		$password = mysqli_real_escape_string($link, $_POST['rpassword']);
		if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST['email'])){
			//valid email address
			$email = $_POST['email'];
		}
		else{
			//invalid email address
			die("Invalid Email. Try Again");
			exit();
		}
		
		if($username && $password && $email) {
			// form fields filled out 
			
			if(!($result = mysqli_query($link, "SELECT Username, Email, Password FROM LoginPage
                                   WHERE Username = '$username' OR Email = '$email'"))){
				echo "Error: " . mysqli_error($link);
			}
            $count = mysqli_num_rows($result);
			
			if($count == 0){
				// username and/or email not used previously
				
				// generate random salt to use append to password 
				$salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
				//sha512 hash password with random salt for protection
				$password = hash('sha512', $password . $salt);
				// md5 hash activation key that will be visible in GET response
				$activationKey = hash('md5', uniqid(mt_rand(1, mt_getrandmax()), true));
				// insert user attributes into db
				$insert = "INSERT INTO `LoginPage`(Username, Email, Password, Activation, ExpirationTime, Salt) VALUES
				('$username', '$email', '$password', '$activationKey', DATE_ADD(now(), INTERVAL 2 HOUR), '$salt')";
				//execute query
				if(!($result = mysqli_query($link, $insert))){
					echo "Error: " . mysqli_error($link);
				}
				
				// send confirmation email to email address provided
				$emailMessage = "Please activate your email by clicking the link:\nhttp://localhost:8888/activate.php?email=" . urlencode($email) . "&key=$activationKey";

				mail($email, 'Registration Confirmation Email', $emailMessage, 'From: haris005@gmail.com');
				
				echo "<div>Thanks for registering. An email has been sent to " . $email . " for confirmation. Click on the link to activate your account</div>";
			}
			else{
				// username and/or email exists
				die("Username and/or email already exists");
				exit();
			}
		}
		else{
			// incomplete input fields
			die("Form incomplete");
			exit();
		}
	}
?>

<form action="" method="post">
    	Username:<br> <input type="text" name="rusername" id="rusername"></input><br>
		Email: <br> <input type="text" name="email" id="email"></input><br>
    	Password:<br> <input type="password" name="rpassword" id="rpassword"></input><br>
    	<input type="submit" name="rsubmit" id="rsubmit" value="Register"></input><br>
		<br>
		<a href="index.html">Home</a>
</form>
