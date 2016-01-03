<?php
// Commentplugin
class YellowComments
{
	const Version = "0.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;	
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{

	$comment_f = "comments/" . $this->yellow->page->get("pageFile");

 	if(file_exists($comment_f)){
	$comment_t = file_get_contents($comment_f);
	$comment_a = explode("\n", $comment_t);
	$comment_z = $comment_a[0];
	unset($comment_a[0]);
	
		$output = NULL;
		if($name=="Comments" || $name=="comments")
		{
			$output .= "<div class='comments'>";
			$output .= "<h1><span>Kommentare: ". $comment_z ."</span></h1>";
			$output .= "<div class='content seperate'></div>";
			$output .= implode("", $comment_a);
			$output .= "</div>";
		}
		return $output;
	
	} 
	
	else {
		$output = NULL;
		if($name=="Comments" || $name=="comments") {
			$output .= "<div class='comments'>";
			$output .= "<h1><span>Kommentare: 0</span></h1>";
			$output .= "</div>";
		}
		return $output;
		
	}
	
	}
	
} 

$yellow->plugins->register("Comments", "YellowComments", YellowComments::Version);
?>
