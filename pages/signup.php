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
			header('Location: ' . BASE_URL . 'index.php?action=login&msg=registered');
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
	
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>
<body>
	
	<div id="header">
		<p>Already registered?
			<a href="<?php echo BASE_URL; ?>index.php?action=login">Click here to login.</a>
		</p>
	</div>
	
	<div id="sidebar">
		<h2>Welcome</h2>
		
		<p>This introductory programming course covers the use of the Python programming language as a tool
		for solving problems. The course is aimed at people with little or no prior programming experience.</p>
		
		<p><a href="https://docs.google.com/viewer?a=v&pid=explorer&chrome=true&srcid=0Bzuha_iJwCLOZGNjOTkwZDItYTBhZS00MDViLWI4ZTUtZGI2YzJhMzc0OTg0&hl=en_US">
			View the course outline.</a>
		</p>
	</div>
	
	<div id="content">
		<h2>Signup</h2>
		
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
			<table>
				<tr>
					<td>
						<label for="fullName">Full Name: </label>
					</td>
					<td>
						<input type="text" name="fullName"
							value="<?php echo isset($_POST['fullName']) ? $_POST['fullName'] : ''; ?>" />
					</td>
				</tr>
				
				<tr>
					<td>
						<label for="email">Email Address: </label>
					</td>
					<td>
						<input type="text" name="email"
							value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
					</td>
				</tr>
				
				<tr>
					<td>
						<label for="username">Username: </label>
					</td>
					<td>
						<input type="text" name="username"
							value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" />
					</td>
				</tr>
				
				<tr>
					<td>
						<label for="password">Password: </label>
					</td>
					<td>
						<input type="password" name="password" />
					</td>
				</tr>
				
				<tr>
					<td style="width:200px;">
						<label for="cas">Check here if you are taking this course for CAS: </label>
					</td>
					<td>
						<input type="checkbox" name="cas" />
					</td>
				</tr>
			
	
			</table>
			
			<p>
				<input type="submit" name="submit" value="Submit" />
			</p>
		</form>
	</div>
	
</body>
</html>
