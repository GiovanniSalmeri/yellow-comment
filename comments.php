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
		return !$this->isExisting("published") || $this->get("published")=="yes";
	}
}

class YellowComments
{
	const Version = "0.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("commentsDir", "comments/");
		$this->yellow->config->setDefault("commentsSeparator", "----");
		$this->yellow->config->setDefault("commentsAutoAppend", "0");
		$this->yellow->config->setDefault("commentsMaxSize", "10000");
		$this->yellow->config->setDefault("contactSpamFilter", "href=|url=");
	}
	
	// Load comments from given file name
	function loadComments($file)
	{
		$file = $this->yellow->config->get("commentsDir").$file;
		$comments = array();
		if(file_exists($file))
		{
			$contents = explode($this->yellow->config->get("commentsSeparator"), file_get_contents($file));
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
	
	// Append comment
	function appendComment($file, $comment)
	{
		// TODO: create directory
		$file = $this->yellow->config->get("commentsDir").$file;
		$status = "send";
		$content = "---\n";
		$content.= "Published: No\n";
		$content.= "Name: ".$comment->get("name")."\n";
		$content.= "From: ".$comment->get("from")."\n";
		if($comment->get("url")!="") $content.= "Url: ".$comment->get("url")."\n";
		$content.= "---\n";
		$content.= $comment->comment."\n";

		$fd = @fopen($file, "c");
		if($fd!==false)
		{
			flock($file, LOCK_EX);
			fseek($fd, 0, SEEK_END);
			$position = ftell($fd);
			if($position+strlen($content)<$this->yellow->config->get("commentsMaxSize"))
			{
				if($position>0) fwrite($fd, $this->yellow->config->get("commentsSeparator")."\n");
				fwrite($fd, $content);
			} else {
				$status = "error";
			}
			flock($file, LOCK_UN);
			fclose($fd);
		} else {
			$status = "error";
		}
		return $status;
	}

	// Build comment from input
	function buildComment()
	{
		$comment = new YellowComment;
		$comment->set("name", trim($_REQUEST["name"]));
		$comment->set("url", trim($_REQUEST["url"]));
		$comment->set("from", trim($_REQUEST["from"]));
		$comment->comment = trim($_REQUEST["message"]);
		return $comment;
	}

	// verify comment for safe use
	function verifyComment($comment)
	{
		// TODO: return which field is wrong to the user
		// TODO: better texts
		// TODO: fold me :)
		$status = "send";
		$spamFilter = $this->yellow->config->get("contactSpamFilter");
		if(strempty($comment->comment)) $status = "incomplete";
		if(!strempty($comment->comment) && preg_match("/$spamFilter/i", $comment->comment)) $status = "error";
		if(!strempty($comment->get("name")) && preg_match("/[^\pL\d\-\. ]/u", $comment->get("name"))) $status = "incomplete";
		if(!strempty($comment->get("from")) && !filter_var($comment->get("from"), FILTER_VALIDATE_EMAIL)) $status = "incomplete";
		if(!strempty($comment->get("from")) && preg_match("/[^\w\-\.\@ ]/", $comment->get("from"))) $status = "incomplete";
		if(!strempty($comment->get("url")) && !preg_match("/^https?\:\/\//i", $comment->get("url"))) $status = "incomplete";

		$separator = $this->yellow->config->get("commentsSeparator");
		if(strpos($comment->comment, $separator)!==false) $status = "incomplete";
		if(strpos($comment->get("name"), $separator)!==false) $status = "incomplete";
		if(strpos($comment->get("from"), $separator)!==false) $status = "incomplete";
		if(strpos($comment->get("url"), $separator)!==false) $status = "incomplete";
		return $status;
	}

	// 
	function processSend($file)
	{
		if(PHP_SAPI == "cli") $this->yellow->page->error(500, "Static website not supported!");
		$status = trim($_REQUEST["status"]);
		if($status == "send")
		{
			$comment = $this->buildComment();
			$status = $this->verifyComment($comment);
			if($status=="send" && $this->yellow->config->get("commentsAutoAppend")) $status = $this->appendComment($file, $comment);
			if($status=="send") $status = $this->sendEmail($comment);
			switch($status)
			{
				case "incomplete":	$this->yellow->page->set("contactStatus", $this->yellow->text->get("contactStatusIncomplete")); break;
				case "invalid":		$this->yellow->page->set("contactStatus", $this->yellow->text->get("contactStatusInvalid")); break;
				case "done":		$this->yellow->page->set("contactStatus", $this->yellow->text->get("contactStatusDone")); break;
				case "error":		$this->yellow->page->error(500, $this->yellow->text->get("contactStatusError")); break;
			}
			$this->yellow->page->setHeader("Last-Modified", $this->yellow->toolbox->getHttpDateFormatted(time()));
			$this->yellow->page->setHeader("Cache-Control", "no-cache, must-revalidate");
		} else {
			$status = "none";
			$this->yellow->page->set("contactStatus", $this->yellow->text->get("contactStatusNone"));
		}
		$this->yellow->page->set("status", $status);
	}
	
	// Send contact email
	function sendEmail($comment)
	{
		$mailMessage = $comment->comment."\r\n";
		$mailMessage = "-- \r\n".$comment->get("name")."\r\n";
		$mailMessage = "-- \r\n".$comment->get("from")."\r\n";
		$mailMessage = "-- \r\n".$comment->get("url")."\r\n";
		$mailTo = $this->yellow->page->get("contactEmail");
		if($this->yellow->config->isExisting("contactEmail")) $mailTo = $this->yellow->config->get("contactEmail");
		$mailSubject = mb_encode_mimeheader($this->yellow->page->get("title"));
		$mailHeaders = empty($from) ? "From: noreply\r\n" : "From: ".mb_encode_mimeheader($name)." <$from>\r\n";
		$mailHeaders .= "X-Contact-Url: ".mb_encode_mimeheader($this->yellow->page->getUrl())."\r\n";
		$mailHeaders .= "X-Remote-Addr: ".mb_encode_mimeheader($_SERVER["REMOTE_ADDR"])."\r\n";
		$mailHeaders .= "Mime-Version: 1.0\r\n";
		$mailHeaders .= "Content-Type: text/plain; charset=utf-8\r\n";
		return mail($mailTo, $mailSubject, $mailMessage, $mailHeaders) ? "done" : "error";
	}

	// Return number of visible comments
	function getCommentCount($file)
	{
		$comments = $this->loadComments($file);
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
			$file = $this->yellow->page->get("pageFile");
			$comments = $this->loadComments($file);
			$output .= "<div class='comments'>";
			$output .= "<h1><span>Kommentare: ".$this->getCommentCount($file)."</span></h1>";
			foreach($comments as $comment)
			{
				if($comment->isPublished())
				{
					$output .= "<div class='comment'>";
					$output .= "<div class='commentname'>";
					$url = $comment->getHtml("url");
					if($url!="") $output .= "<a href='$url'>";						
					$output .= $comment->getHtml("name");
					if($url!="") $output .= "</a>";
					$output .= ":</div>";
					$output .= "<div class='commentcontent'>";					
					// TODO: Maybe use Markdown here
					$output .= preg_replace("/\n/", "<br/>", htmlspecialchars($comment->comment));
					$output .= "</div>";
					$output .= "</div>";
				}
			}
			$output .= "</div>";			
		} else if(lcfirst($name)=="commentsCount") {
			$output = $this->getCommentCount($this->yellow->page->get("pageFile"));
		} else if(lcfirst($name)=="commentsSend") {
			$output = $this->processSend($this->yellow->page->get("pageFile"));
		}
		return $output;
	}
	
} 

$yellow->plugins->register("Comments", "YellowComments", YellowComments::Version);
?>
