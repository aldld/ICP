<?php
// Signup handler

$errors = array();

if (isset($_POST['submit'])) {
	// User has submitted the form
	
	// Ensure username field has been filled
	if (empty($_POST['username'])) $errors[] = 'Please enter a username.';
	else {
		// Check if username is already in use
		if (Auth::userExists($_POST['username'])) {
			$errors[] = 'The username you entered is already in use.';
		}
	}
	
	// Ensure email field has been filled
	if (empty($_POST['email'])) $errors[] = 'Please enter your email address.';
	else {
		// Check if email is already in use
		if (Auth::emailInUse($_POST['email'])) {
			$errors[] = 'The email address you entered is currently in use';
		}
	}
	
	// Ensure password field has been filled
	if (empty($_POST['password'])) $errors[] = 'Please enter a password.';
	
	// Ensure the full name field has been filled
	if (empty($_POST['fullName'])) $errors[] = 'Please enter your full name.';
	
	// If there are no errors, create the user.
	if (empty($errors)) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email    = $_POST['email'];
		$fullName = $_POST['fullName'];
		$cas      = isset($_POST['cas']);
		
		if (Auth::createUser($username, $password, $email, $fullName, $cas)) {
			// TODO: Redirect to login page upon success
			echo 'success!';
		} else {
			// Something went wrong.
			$errors[] = 'Login failure. Contact eric.bannatyne@gmail for assistance.';
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Introduction to Computer Programming &ndash; Signup</title>
</head>
<body>
	
	<h1>Signup</h1>
	
	<p>Already registered?
		<a href="<?php echo BASE_URL; ?>index.php?action=login">Click here to login.</a>
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
	
	<form action="<?php echo BASE_URL; ?>" method="post">
		<p>
			<label for="fullName">Full Name: </label>
			<input type="text" name="fullName"
				value="<?php echo isset($_POST['fullName']) ? $_POST['fullName'] : ''; ?>" />
		</p>
		
		<p>
			<label for="email">Email Address: </label>
			<input type="text" name="email"
				value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
		</p>
		
		<p>
			<label for="username">Username: </label>
			<input type="text" name="username"
				value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" />
		</p>
		
		<p>
			<label for="password">Password: </label>
			<input type="password" name="password" />
		</p>
		
		<p>
			<label for="cas">Check here if you are taking this course for CAS: </label>
			<input type="checkbox" name="cas" />
		</p>
		
		<p>
			<input type="submit" name="submit" value="Submit" />
		</p>
	</form>
	
</body>
</html>
