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
	var $requiredField;
	var $comments;
	var $pageText;
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("commentsDir", "");
		$this->yellow->config->setDefault("commentsExtension", "-comments");
		$this->yellow->config->setDefault("commentsTemplate", "system/config/comments-template.txt");
		$this->yellow->config->setDefault("commentsSeparator", "----");
		$this->yellow->config->setDefault("commentsAutoAppend", "0");
		$this->yellow->config->setDefault("commentsAutoPublish", "0");
		$this->yellow->config->setDefault("commentsMaxSize", "10000");
		$this->yellow->config->setDefault("commentsSpamFilter", "href=|url=");
		$this->requiredField = "";
		$this->cleanup();
	}

	// Check if the web interface is active
	function isWebinterface($page)
	{
		$location = $page->getLocation();
		$webinterface = $this->yellow->plugins->get("webinterface");
		return $webinterface?$webinterface->checkRequest($location):false;
	}

	// Cleanup datastructures
	function onParseContentRaw($page, $text)
	{
		return (lcfirst($page->get("parser"))=="comments")?$this->yellow->text->get("commentsWebinterfaceModify"):$text;
	}

	// Handle page meta data parsing
	function onParseMeta($page)
	{
		if(lcfirst($page->get("parser"))=="comments") $page->visible = false;
	}

	// Cleanup datastructures
	function cleanup()
	{
		$this->comments = array();
		$this->pageText = "";
	}

	// Return Email
	function getEmail()
	{
		return $this->yellow->config->isExisting("commentsEmail")?$this->yellow->config->get("commentsEmail"):$this->yellow->page->get("commentsEmail");
	}

	// Return file name from page object (depending on settings)
	function getCommentFileName($page)
	{
		if($this->yellow->config->get("commentsDir")=="")
		{
			$file = $page->fileName;
			$extension = $this->yellow->config->get("contentExtension");
			if(substru($file, strlenu($file)-strlenu($extension))==$extension)
				$file = substru($file, 0, strlenu($file)-strlenu($extension));
			$file .= $this->yellow->config->get("commentsExtension").$extension;
			return $file;
		} else {
			return $this->yellow->config->get("commentsDir").$page->get("pageFile");
		}
	}
	
	// Load comments from given file name
	function loadComments($page)
	{
		$file = $this->getCommentFileName($page);
		$this->cleanup();
		if(file_exists($file))
		{
			$contents = explode($this->yellow->config->get("commentsSeparator"), file_get_contents($file));
			if(count($contents>0))
			{
				$pageText = $contents[0];
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
						array_push($this->comments, $comment);
					}
				}
			}
		}
	}
	
	// Save comments
	function saveComments($page, $checkSize)
	{
		// TODO: create directory
		$file = $this->getCommentFileName($page);
		$error = "";

		if($this->pageText=="")
		{
			$this->pageText = file_get_contents($this->yellow->config->get("commentsTemplate"));
			if($this->pageText=="")
			{
				$this->pageText = "---\nTitle: Comments\nParser: comments\n---\n";
			}
		}

		$content = $this->pageText;
		foreach($this->comments as $comment)
		{
			$content.= $this->yellow->config->get("commentsSeparator")."\n";
			$content.= "---\n";
			foreach($comment->metaData as $key=>$value)
			{
				$content.= ucfirst($key).": ".$value."\n";
			}
			$content.= "---\n";
			$content.= $comment->comment."\n";
		}
		if(strlen($content)<$this->yellow->config->get("commentsMaxSize") || !$checkSize)
		{
			$fd = @fopen($file, "c");
			if($fd!==false)
			{
				flock($file, LOCK_EX);
				fseek($fd, 0, SEEK_SET);
				fwrite($fd, $content);
				ftruncate($fd, ftell($fd));
				flock($file, LOCK_UN);
				fclose($fd);
			} else {
				$error = "Error";
			}
		} else {
			$error = "Error";
		}
		return $error;
	}

	// Build comment from input
	function buildComment()
	{
		$comment = new YellowComment;
		$comment->set("name", filter_var(trim($_REQUEST["name"]), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$url = filter_var(trim($_REQUEST["url"]), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if($url!="") $comment->set("url", $url);
		$comment->set("from", filter_var(trim($_REQUEST["from"]), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$comment->set("created", date("Y-m-d H:i:s"));
		$comment->set("uid", hash("sha256", $this->yellow->toolbox->createSalt(64)));
		$comment->set("aid", hash("sha256", $this->yellow->toolbox->createSalt(64)));
		if($this->yellow->config->get("commentsAutoPublish")!="1") $comment->set("published", "No");
		$comment->comment = trim($_REQUEST["comment"]);
		return $comment;
	}

	// verify comment for safe use
	function verifyComment($comment)
	{
		// TODO: fold me :)
		$error = "";
		$field = "";
		$spamFilter = $this->yellow->config->get("commentsSpamFilter");
		if(strempty($comment->comment)) { $field = "comment"; $error = "InvalidComment"; }
		if(!strempty($comment->comment) && preg_match("/$spamFilter/i", $comment->comment)) { $field = "comment"; $error = "Error"; }
		if(!strempty($comment->get("name")) && preg_match("/[^\pL\d\-\. ]/u", $comment->get("name"))) { $field = "name"; $error = "InvalidName"; }
		if(!strempty($comment->get("from")) && !filter_var($comment->get("from"), FILTER_VALIDATE_EMAIL)) { $field = "from"; $error = "InvalidMail"; }
		if(!strempty($comment->get("from")) && preg_match("/[^\w\-\.\@ ]/", $comment->get("from"))) { $field = "from"; $error = "InvalidMail"; }
		if(!strempty($comment->get("url")) && !preg_match("/^https?\:\/\//i", $comment->get("url"))) { $field = "url"; $error = "InvalidUrl"; }

		$separator = $this->yellow->config->get("commentsSeparator");
		if(strpos($comment->comment, $separator)!==false) { $field = "comment"; $error = "InvalidComment"; }
		if(strpos($comment->get("name"), $separator)!==false) { $field = "name"; $error = "InvalidName"; }
		if(strpos($comment->get("from"), $separator)!==false) { $field = "from"; $error = "InvalidMail"; }
		if(strpos($comment->get("url"), $separator)!==false) { $field = "url"; $error = "InvalidUrl"; }
		$this->requiredField = $field;
		return $error;
	}

	// Process user input
	function processSend($page)
	{
		if(PHP_SAPI == "cli") $this->yellow->page->error(500, "Static website not supported!");
		$aid = trim($_REQUEST["aid"]);
		$action = trim($_REQUEST["action"]);
		if($aid!="")
		{
			$changed = false;
			for($n=0; $n<count($this->comments); $n++)
			{
				if($this->comments[$n]->get("aid")==$aid)
				{
					if($action=="remove")
					{
						unset($this->comments[$n]);
						$changed = true;
						break;
					} else if($action=="publish") {
						$this->comments[$n]->set("published", null);
						$changed = true;
						break;
					}
				}
			}
			if($changed) $this->saveComments($page, false);
		}
		$status = trim($_REQUEST["status"]);
		if($status=="send")
		{
			$comment = $this->buildComment();
			$error = $this->verifyComment($comment);
			if($error=="") array_push($this->comments, $comment);
			if($error=="" && $this->yellow->config->get("commentsAutoAppend")) $error = $this->saveComments($page, true);
			if($error=="" && $this->getEmail()!="") $error = $this->sendEmail($comment);
			if($error=="")
			{
				$this->yellow->page->set("commentsStatus", $this->yellow->text->get("commentsStatusDone"));
				$status = "done";
			} else {
				$this->yellow->page->set("commentsStatus", $this->yellow->text->get("commentsStatus".$error));
			}
			$this->yellow->page->setHeader("Last-Modified", $this->yellow->toolbox->getHttpDateFormatted(time()));
			$this->yellow->page->setHeader("Cache-Control", "no-cache, must-revalidate");
		} else {
			$this->yellow->page->set("commentsStatus", $this->yellow->text->get("commentsStatusNone"));
		}
		$this->yellow->page->set("status", $status);
	}
	
	// Send comment email
	function sendEmail($comment)
	{
		$mailMessage = $comment->comment."\r\n";
		$mailMessage.= "-- \r\n";
		$mailMessage.= "Name: ".$comment->get("name")."\r\n";
		$mailMessage.= "Mail: ".$comment->get("from")."\r\n";
		$mailMessage.= "Url:  ".$comment->get("url")."\r\n";
		$mailMessage.= "Uid:  ".$comment->get("uid")."\r\n";
		$mailMessage.= "-- \r\n";
		if($this->yellow->config->get("commentsAutoAppend"))
		{
			if($this->yellow->config->get("commentsAutoPublish")!="1")
			{
				$mailMessage.= "Publish: ".$this->yellow->page->getUrl()."?aid=".$comment->get("aid")."&action=publish\r\n";
			} else {
				$mailMessage.= "Remove: ".$this->yellow->page->getUrl()."?aid=".$comment->get("aid")."&action=remove\r\n";
			}
		}
		$mailSubject = mb_encode_mimeheader($this->yellow->page->get("title"));
		$mailHeaders = empty($from) ? "From: noreply\r\n" : "From: ".mb_encode_mimeheader($name)." <$from>\r\n";
		$mailHeaders .= "X-Contact-Url: ".mb_encode_mimeheader($this->yellow->page->getUrl())."\r\n";
		$mailHeaders .= "X-Remote-Addr: ".mb_encode_mimeheader($_SERVER["REMOTE_ADDR"])."\r\n";
		$mailHeaders .= "Mime-Version: 1.0\r\n";
		$mailHeaders .= "Content-Type: text/plain; charset=utf-8\r\n";
		return mail($this->getEmail(), $mailSubject, $mailMessage, $mailHeaders) ? "" : "Error";
	}

	// Return number of visible comments
	function getCommentCount()
	{
		$count = 0;
		foreach($this->comments as $comment)
		{
			if($comment->isPublished())
			{
				$count++;
			}
		}
		return $count;
	}
	
	// Return default string if field is required by name otherwise an empty string
	function required($field, $default)
	{
		return ($this->requiredField==$field)?$default:"";
	}
} 

$yellow->plugins->register("comments", "YellowComments", YellowComments::Version);
?>
