<?php
// Commentplugin
class YellowComment
{
	var $metaData;
	var $comment;
	
	function __construct()
	{
		$this->metaData = array();
	}

	// Set comment meta data
	function set($key, $value)
	{
		$this->metaData[$key] = $value;
	}
	
	// Return comment meta data
	function get($key)
	{
		return $this->isExisting($key) ? $this->metaData[$key] : "";
	}

	// Return comment meta data, HTML encoded
	function getHtml($key)
	{
		return htmlspecialchars($this->get($key));
	}
	
	// Check if comment meta data exists
	function isExisting($key)
	{
		return !is_null($this->metaData[$key]);
	}
	
	// Check if comment was published
	function isPublished()
	{
		return !isset($this->metaData["published"]) || $this->metaData["published"]=="yes";
	}
}

class YellowComments
{
	const Version = "0.1";
	var $yellow;			//access to API
	var $separator = "----"; 		// TODO: make this configurable?
	var $commentDir = "comments/";	// TODO: make this configurable?
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;	
	}
	
	// Load comment for for actual page
	function loadComments()
	{
		$file = $this->commentDir.$this->yellow->page->get("pageFile");
		$comments = array();
		if(file_exists($file))
		{
			$contents = explode($this->separator, file_get_contents($file));
			if(count($contents>0))
			{
				unset($contents[0]);
				foreach($contents as $content)
				{
					if(preg_match("/^(\xEF\xBB\xBF)?[\r\n]*\-\-\-[\r\n]+(.+?)[\r\n]+\-\-\-[\r\n]+(.*)/s", $content, $parts))
					{
						$comment = new YellowComment;
						foreach(preg_split("/[\r\n]+/", $parts[2]) as $line)
						{
							preg_match("/^\s*(.*?)\s*:\s*(.*?)\s*$/", $line, $matches);
							if(!empty($matches[1]) && !strempty($matches[2])) $comment->set(lcfirst($matches[1]), $matches[2]);
						}
						$comment->comment = trim($parts[3]);
						array_push($comments, $comment);
					}
				}
			}
		}
		return $comments;
	}

	// Return number of visible comments
	function getCommentCount()
	{
		$comments = $this->loadComments();
		$count = 0;
		foreach($comments as $comment)
		{
			if($comment->isPublished())
			{
				$count++;
			}
		}
		return $count;
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		if(lcfirst($name)=="comments")
		{
			$comments = $this->loadComments();
			$output .= "<div class='comments'>";
			$output .= "<h1><span>Kommentare: ".$this->getCommentCount()."</span></h1>";
			foreach($comments as $comment)
			{
				if($comment->isPublished())
				{
					$output .= "<div class='comment'>";
					$output .= "<div class='commentname'>";					
					$output .= $comment->getHtml("name");
					$output .= ":</div>";
					$output .= "<div class='commentconent'>";					
					// TODO: Maybe use Markdown here
					$output .= htmlspecialchars($comment->comment);
					$output .= "</div>";
					$output .= "</div>";
				}
			}
			$output .= "</div>";			
		} else if(lcfirst($name)=="commentsCount") {
			$output = $this->getCommentCount();
		}
		return $output;
	}
	
} 

$yellow->plugins->register("Comments", "YellowComments", YellowComments::Version);
?>
