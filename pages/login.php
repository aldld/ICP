<?php
// Login handler

$errors = array();

if (isset($_POST['submit'])) {
	
	// Make sure the username and password fields are filled
	if (empty($_POST['username'])) $errors[] = 'Please enter your username.';
	if (empty($_POST['password'])) $errors[] = 'Please enter your password.';
	
	if (empty($errors)) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		// Make sure a valid username/password combination is given
		if (Auth::verifyUser($username, $password)) {
			// Successful login
			$_SESSION['user'] = $username;
			
			// Redirect to dashboard
			header('Location: ' . BASE_URL);
		} else {
			$errors[] = 'Incorrect username or password.';
		}
	}
	
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Introduction to Computer Programming &ndash; Login</title>
</head>
<body>
	
	<h1>Login</h1>
	
	<p>Don't have an account?
		<a href="<?php echo BASE_URL; ?>">Click here to sign up.</a>
	</p>
	
	<?php
	if (!empty($errors)) {
		echo '<ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
	?>
	
	
	<form action="<?php echo BASE_URL; ?>index.php?action=login" method="post">
		
		<p>
			<label for="username">Username</label>
			<input type="text" name="username" />
		</p>
		
		<p>
			<label for="password">Password</label>
			<input type="password" name="password" />
		</p>
		
		<p>
			<input type="submit" name="submit" value="Login" />
		</p>
		
	</form> 
	
</body>
</html>
