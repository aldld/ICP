<?php
require_once 'include/database.php';
require_once 'include/format.php';

$db = Database::getInstance();

$user = Auth::getUser($_SESSION['user']);

$errors = array();

if (isset($_POST['submit'])) {
	
	// Ensure fields are filled
	if (empty($_POST['title'])) $errors[] = 'Please enter a title.';
	if (empty($_POST['content'])) $errors[] = 'Please at least enter something in the space provided.';
	
	if (empty($errors)) {
		// Sanitize HTML to prevent XSS
		$title = htmlentities($_POST['title']);
		$content = htmlentities($_POST['content']);
		
		// Parse for code blocks
		$content = str_replace(htmlentities('<code>'), '</p><pre>', $content);
		$content = str_replace(htmlentities('</code>'), '</pre><p>', $content);
		
		// Format content
		$content = format($content);
		
		$data = array(
			'title'   => $title,
			'content' => $content,
			'author'  => $user->id,
			'date'    => date('Y-m-d H:i:s', time())
		);
		
		$stmt = $db->prepare('INSERT INTO post (title, content, author, date)
			VALUES (:title, :content, :author, :date)');
		
		if ($stmt->execute($data)) {
			$id = $db->lastInsertID();
			header('Location: ' . BASE_URL . 'index.php?action=single&id=' . $id);
		} else {
			echo '<h1>Something went wrong while processing your request.</h1>';
		}
	}
	
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Introduction to Computer Programming &ndash; Dashboard</title>
</head>
<body>
	
	<h1>Dashboard</h1>
	
	<p>
		Logged in as <?php echo $user->fullName; ?>. 
		<a href="<?php echo BASE_URL; ?>index.php?action=logout">Logout</a>
	</p>
	
	<h2>Quick Links</h2>
	<ul>
		<li><a href="<?php echo BASE_URL; ?>index.php?action=video">Latest Video</a></li>
		<li><a href="http://www.youtube.com/user/aldld">YouTube Channel</a></li>
		<li><a href="https://docs.google.com/leaf?id=0Bzuha_iJwCLONjc4ODdlZTYtNTcyNC00NjMyLWI2Y2UtZTBkZjYzNWZhNzdm&hl=en_US">Google Docs</a></li>
		<li><a href="<?php echo BASE_URL; ?>index.php?action=instructions">Instructions for submitting files</a></li>
	</ul>
	
	<h2>Discussion</h2>
	
	<h3>Add New Post</h3>
	
	<?php
	if (!empty($errors)) {
		echo '<ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
	?>
	
	<form action="<?php echo BASE_URL; ?>index.php?action=dashboard" method="post">
		
		<p>
			<input type="text" name="title" value="<?php if (isset($_POST['title'])) echo $_POST['title']; ?>" />
		</p>
		
		<p>
			<textarea name="content" rows="7" cols="40"><?php if (isset($_POST['content'])) echo $_POST['content']; ?></textarea>
		</p>
		
		<p>
			<input type="submit" name="submit" value="Save" />
		</p>
		
	</form>
	
	<p>If you have any problems using this site please contact me at eric.bannatyne@gmail.com.</p>
	
	<?php
	$stmt = $db->prepare('SELECT id, title, content, author, date FROM post ORDER BY date DESC');
	$stmt->execute();
	
	if ($stmt->rowCount() > 0) {
		$stmt->setFetchMode(PDO::FETCH_OBJ);
		echo '<ul>';
		while ($post = $stmt->fetch()) {
			echo '<li><a href="' . BASE_URL . 'index.php?action=single&id=' . $post->id . '">';
			echo $post->title;
			echo '</a></li>';
		}
		echo '</ul>';
	} else {
		echo '<p>No posts to display.</p>';
	}
	?>
	
	
	
</body>
</html>
