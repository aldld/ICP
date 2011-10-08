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
	
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>
<body>
	
	<div id="header">
		<p>
			<strong>Dashboard</strong> &ndash;
			Logged in as <?php echo $user->fullName; ?>. 
			<a href="<?php echo BASE_URL; ?>index.php?action=logout">Logout</a>
		</p>
	</div>
	
	<div id="sidebar">
		<h2>Quick Links</h2>
		<ul>
			<li><a href="<?php echo BASE_URL; ?>index.php?action=video">Latest Video</a></li>
			<li><a href="http://www.youtube.com/user/aldld">YouTube Channel</a></li>
			<li><a href="https://docs.google.com/leaf?id=0Bzuha_iJwCLONjc4ODdlZTYtNTcyNC00NjMyLWI2Y2UtZTBkZjYzNWZhNzdm&hl=en_US">Google Docs</a></li>
			<li><a href="<?php echo BASE_URL; ?>index.php?action=instructions">Instructions for submitting files</a></li>
		</ul>
		
		<p><strong>Protip:</strong> To enter code snippets, use the &lt;code&gt; tags. For example:</p>
		
		&lt;code&gt;<br />
		def someFunction(x, y):<br />
		&nbsp;&nbsp;&nbsp;&nbsp;doStuff(y)<br />
		&nbsp;&nbsp;&nbsp;&nbsp;while x &lt; f(y):<br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;print 'something'<br />
		&lt;/code&gt;
		
		<p>Will display as:</p>
		
		<pre>
def someFunction(x, y):
    doStuff(y)
    while x &lt; f(y):
        print 'something'
		</pre>
		
		
	</div>
	
	<div id="content">
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
				<input type="text" name="title" size="30" value="<?php if (isset($_POST['title'])) echo $_POST['title']; ?>" />
			</p>
			
			<p>
				<textarea name="content" rows="10" cols="60"><?php if (isset($_POST['content'])) echo $_POST['content']; ?></textarea>
			</p>
			
			<p>
				<input type="submit" name="submit" value="Save" />
			</p>
			
		</form>
		
		
		<h2>Current Discussions</h2>
		<?php
		$stmt = $db->prepare('SELECT id, title, content, author, date FROM post ORDER BY date DESC');
		$stmt->execute();
		
		if ($stmt->rowCount() > 0) {
			$stmt->setFetchMode(PDO::FETCH_OBJ);
			echo '<ul id="post-list">';
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
	</div>
	
</body>
</html>
