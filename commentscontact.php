<?php

class YellowCommentsContact
{
	const Version = "0.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("contactSpamFilter", "href=|url=");
	}
	
	// Handle page parsing
	function onParsePage()
	{
		if($this->yellow->page->get("template") == "commentscontact")
		{
			if(PHP_SAPI == "cli") $this->yellow->page->error(500, "Static website not supported!");
			$status = trim($_REQUEST["status"]);
			if($status == "send")
			{
				$status = $this->sendEmail();
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
	}
	
	// Send contact email
	function sendEmail()
	{
		$status = "send";
		$spamFilter = $this->yellow->config->get("contactSpamFilter");
		if(strempty(trim($_REQUEST["message"]))) $status = "incomplete";
		if(!strempty($_REQUEST["from"]) && !filter_var($_REQUEST["from"], FILTER_VALIDATE_EMAIL)) $status = "invalid";
		if(!strempty($_REQUEST["message"]) && preg_match("/$spamFilter/i", $_REQUEST["message"])) $status = "error";
		$name = preg_replace("/[^\pL\d\-\. ]/u", "-", $_REQUEST["name"]);
		$url = preg_replace("/[^\pL\d\-\. ]/u", "-", $_REQUEST["url"]);
		$beitrag = preg_replace("/[^\pL\d\-\. ]/u", "-", $_REQUEST["beitrag"]);
		$from = preg_replace("/[^\w\-\.\@ ]/", "-", $_REQUEST["from"]);
		if($status == "send")
		{
			$mailMessage = $_REQUEST["message"]."\r\n-- \r\n$name"."\r\n-- \r\n$from"."\r\n-- \r\n$url"."\r\n-- \r\n$beitrag";
			$mailTo = $this->yellow->page->get("contactEmail");
			if($this->yellow->config->isExisting("contactEmail")) $mailTo = $this->yellow->config->get("contactEmail");
			$mailSubject = mb_encode_mimeheader($this->yellow->page->get("title"));
			$mailHeaders = empty($from) ? "From: noreply\r\n" : "From: ".mb_encode_mimeheader($name)." <$from>\r\n";
			$mailHeaders .= "X-Contact-Url: ".mb_encode_mimeheader($this->yellow->page->getUrl())."\r\n";
			$mailHeaders .= "X-Remote-Addr: ".mb_encode_mimeheader($_SERVER["REMOTE_ADDR"])."\r\n";
			$mailHeaders .= "Mime-Version: 1.0\r\n";
			$mailHeaders .= "Content-Type: text/plain; charset=utf-8\r\n";
			$status = mail($mailTo, $mailSubject, $mailMessage, $mailHeaders) ? "done" : "error";
		}
		return $status;
	}
}

$yellow->plugins->register("commentscontact", "YellowCommentsContact", YellowCommentsContact::Version);
?>
