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
		$data = array(
			'content' => format(htmlentities($_POST['content'])),
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
	<title>Introduction to Computer Programming</title>
</head>
<body>
	
	<p><a href="<?php echo BASE_URL; ?>">Return to dashboard</a></p>
	
	<?php
	if (isset($error404)) echo '<h1>The post you specified could not be found.</h1>';
	else {
		echo '<h1>' . $post->title . '</h1>';
		echo '<p>' . date('F j, Y h:i A', strtotime($post->date)) . ' by ' . Auth::getUser($post->author)->fullName . '</p>';
		echo $post->content;
	}
	?>
	
	<!-- TODO: Show comments here -->
	
	<div id="comments">
		<?php
		$data = array('post' => $post->id);
		$stmt = $db->prepare('SELECT id, content, author, date FROM comment WHERE post=:post');
		$stmt->execute($data);
		
		if ($stmt->rowCount() > 0) {
			$stmt->setFetchMode(PDO::FETCH_OBJ);
			while ($comment = $stmt->fetch()) {
				echo '<div id="comment-' . $comment->id . '">';
				echo '<p class="comment-meta">' . $comment->author . ' &ndash; ' . date('F j, Y h:i A', strtotime($comment->date)) . '</p>';
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
			<textarea name="content" rows="7" cols="40"></textarea>
		</p>
		
		<p>
			<input type="submit" name="submit" value="Save" />
		</p>
		
	</form>
	
	<!-- TODO: How to insert code snippets -->
	
</body>
</html>