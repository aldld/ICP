<?php
// Formatting paragraphs and code blocks

// Produces terrible markup but whatever.
// Assumes escaped HTML entities
function format($content) {
	$paragraphs = explode("\n", $content);
	$i = 0;
	$addParaTag = true;
	while ($i < count($paragraphs)) {
		if (empty($paragraphs[$i])) {
			unset($paragraphs[$i]);
			$paragraphs = array_values($paragraphs);
		} else {
			if (strpos($paragraphs[$i], '<pre>') !== false) {
				$addParaTag = false;
			}
			
			if (strpos($paragraphs[$i], '</pre>') !== false) {
				$addParaTag == true;
			}
			
			if ($addParaTag && $i != count($paragraphs) - 1) {
				$paragraphs[$i] = $paragraphs[$i] . '</p><p>';
			} else {
				$paragraphs[$i] = $paragraphs[$i] . "\n";
			}
			$i++;
		}
	}
	
	$paragraphs = implode('', $paragraphs);
	return '<p>' . $paragraphs . '</p>';
}
