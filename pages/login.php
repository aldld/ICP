<?php
// Login handler

if (Auth::loggedIn()) header('Location: ' . BASE_URL);

$errors = array();

if (isset($_POST['submit'])) {
	
	// Make sure the username and password fields are filled
	if (empty($_POST['username'])) $errors[] = 'Please enter your username.';
	if (empty($_POST['password'])) $errors[] = 'Please enter your password.';
	
	if (empty($errors)) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		// Make sure a valid username/password combination is given
		if (Auth::verifyUser($username, $password)) { // Successful login
			// $_SESSION['user'] stores the ID of the current user
			$_SESSION['user'] = Auth::getUserID($username);
			
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
	
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>
<body>
	
	
	<div id="header">
		<p>Don't have an account?
			<a href="<?php echo BASE_URL; ?>">Click here to sign up.</a>
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
		<h2>Login</h2>
		
		<?php
		if (isset($_GET['msg']) && $_GET['msg'] == 'registered') {
			echo '<p>Registration successful! You may now login.';
		}
		?>
		
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
			
			<table>
				<tr>
					<td><label for="username">Username</label></td>
					<td><input type="text" name="username" /></td>
				</tr>
			
				<tr>
					<td><label for="password">Password</label></td>
					<td><input type="password" name="password" /></td>
				</tr>
			</table>
			
			<p>
				<input type="submit" name="submit" value="Login" />
			</p>
			
		</form>
		
		<p>Trouble logging in? Contact eric.bannatyne@gmail.com for assistance.</p>
	</div>
	
</body>
</html>
