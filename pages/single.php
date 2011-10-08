<?php
require_once 'include/database.php';
require_once 'include/format.php';

$db = Database::getInstance();

$post = null;

$errors = array();

if (!isset($_GET['id'])) {
	header('Location: ' . BASE_URL);
} else {
	$data = array('id' => $_GET['id']);
	$stmt = $db->prepare('SELECT id, title, content, author, date FROM post WHERE id=:id LIMIT 1');
	$stmt->execute($data);
	
	if ($stmt->rowCount() == 0) {
		$error404 = 404;
	} else {
		$stmt->setFetchMode(PDO::FETCH_OBJ);
		$post = $stmt->fetch();
	}
}

// Add comment processing
if (isset($_POST['submit'])) {
	if (empty($_POST['content'])) $errors[] = 'We need something here.';
	
	if (empty($errors)) {
		$content = htmlentities($_POST['content']);
		
		// Parse for code blocks
		$content = str_replace(htmlentities('<code>'), '</p><pre>', $content);
		$content = str_replace(htmlentities('</code>'), '</pre><p>', $content);
		
		$data = array(
			'content' => format($content),
			'author'  => Auth::getUser($_SESSION['user'])->fullName,
			'date'    => date('Y-m-d H:i:s', time()),
			'post'    => $post->id
		);
		
		$stmt = $db->prepare('INSERT INTO comment (content, author, date, post)
			VALUES (:content, :author, :date, :post)');
		
		if ($stmt->execute($data)) {
			$id = $db->lastInsertID();
			header('Location: ' . BASE_URL . 'index.php?action=single&id=' . $post->id . '#comment-' . $id);
		} else {
			echo '<h1>Something went wrong while processing your request.</h1>';
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Introduction to Computer Programming &ndash; Viewing single post</title>
	
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
</head>
<body>
	
	<div id="header">
		<p>
			<strong>Viewing Single Post</strong> &ndash;
			<a href="<?php echo BASE_URL; ?>">Return to dashboard</a> &ndash;
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
		<?php
		if (isset($error404)) echo '<h1>The post you specified could not be found.</h1>';
		else {
			echo '<h2>' . $post->title . '</h2>';
			echo '<p>' . date('F j, Y h:i A', strtotime($post->date)) . ' by ' . Auth::getUser($post->author)->fullName . '</p>';
			echo $post->content;
		}
		?>
		
		<div id="comments">
			<h2>Comments</h2>
			<?php
			$data = array('post' => $post->id);
			$stmt = $db->prepare('SELECT id, content, author, date FROM comment WHERE post=:post');
			$stmt->execute($data);
			
			if ($stmt->rowCount() > 0) {
				$stmt->setFetchMode(PDO::FETCH_OBJ);
				while ($comment = $stmt->fetch()) {
					echo '<div class="comment" id="comment-' . $comment->id . '">';
					echo '<p class="comment-meta"><strong>' . $comment->author . '</strong> &ndash; ' . date('F j, Y h:i A', strtotime($comment->date)) . '</p>';
					echo $comment->content;
					echo '</div>';
				}
			} else {
				echo 'No comments to display. Yet.';
			}
			?>
		</div>
		
		<h2>Leave a Comment</h2>
		
		<?php
		if (!empty($errors)) {
			echo '<ul>';
			foreach ($errors as $error) {
				echo '<li>' . $error . '</li>';
			}
			echo '</ul>';
		}
		?>
		
		<form action="<?php echo BASE_URL; ?>index.php?action=single&id=<?php echo $post->id; ?>" method="post">
			
			<p>
				<textarea name="content" rows="10" cols="60"></textarea>
			</p>
			
			<p>
				<input type="submit" name="submit" value="Save" />
			</p>
			
		</form>
		
		<!-- TODO: How to insert code snippets -->
	</div>
	
</body>
</html>