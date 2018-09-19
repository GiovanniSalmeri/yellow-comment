<?php
// Comment plugin
// Copyright (c) 2016-2018 Authors
// This file may be used and distributed under the terms of the public license.

class YellowComment {
    var $metaData;
    var $comment;
    
    // Set comment meta data
    function set($key, $value) {
        $this->metaData[$key] = $value;
    }
    
    // Return comment meta data
    function get($key) {
        return $this->metaData[$key];
    }

    // Return comment meta data, HTML encoded
    function getHtml($key) {
        return htmlspecialchars($this->metaData[$key]);
    }
    
    // Check if comment was published
    function isPublished() {
        return lcfirst($this->get("published")) != "no";
    }
}

class YellowComments {
    const VERSION = "0.7.6";
    public $yellow;         //access to API

    var $comments;
    var $pageText;
    var $fileHandle;
    var $areOpen;

    // Handle initialisation
    function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->config->setDefault("commentsModerator", "");
        $this->yellow->config->setDefault("commentsDir", "comments/");
        $this->yellow->config->setDefault("commentsAutoPublish", "0");
        $this->yellow->config->setDefault("commentsMaxSize", "10000");
        $this->yellow->config->setDefault("commentsTimeout", "0");
        $this->yellow->config->setDefault("commentsOpening", "30");
        $this->yellow->config->setDefault("commentsAuthorNotification", "1");
        $this->yellow->config->setDefault("commentsSpamFilter", "href=|url=");
        $this->yellow->config->setDefault("commentsIconSize", "80");
        $this->yellow->config->setDefault("commentsIconGravatar", "0");
        $this->yellow->config->setDefault("commentsIconGravatarDefault", "mp");
    }

    // Handle page content of custom block
    public function onParseContentBlock($page, $name, $text, $shortcut) {
        $output = null;
        if ($name=="comments" && $shortcut) {
            list($opening) = $this->yellow->toolbox->getTextArgs($text);
            if ($opening == "") $opening = $this->yellow->config->get("commentsOpening");
            $this->areOpen = time()-$opening*86400 < strtotime($this->yellow->page->get("published")) || !$opening;

            $this->lockComments($this->yellow->page, false);
            $this->loadComments();
            $this->processSend();
            $this->unlockComments();
            $iconSize = intval($this->yellow->config->get("commentsIconSize")); 

            $output = "<div class=\"comments\" id=\"comments\">\n";
            $output .= "<h2><span>" . $this->yellow->text->get("commentsComments") . " " . $this->getCommentCount() . "</span></h2>\n";
            foreach ((array)$this->comments as $comment) {
                if ($comment->isPublished()) {
                    $output .= "<div class=\"comment\" id=\"" . $comment->getHtml("uid") . "\">\n";
                    $output .= "<div class=\"comment-icon\"><img src=\"" . $this->getUserIcon($comment->get("from")) . "\" width=\"" . $iconSize . "\" height=\"" . $iconSize . "\" alt=\"Image\" /></div>\n";
                    $output .= "<div class=\"comment-main\">\n";
                    $output .= "<div class=\"comment-name\">" . $comment->getHtml("name") . "</div>\n";
                    $output .= "<div class=\"comment-date\">" . $this->yellow->text->normaliseDate($comment->get("created")) . "</div>\n";
                    $output .= "<div class=\"comment-content\">" . $this->transformText($this->yellow->page, $comment->comment) . "</div>\n";
                    $output .= "</div>\n";
                    $output .= "</div>\n";
                }
            }
            $output .= "</div>\n";

            $output .= "<div class=\"content separate\" id=\"form\"></div>\n";
            if ($this->yellow->page->get("status") != "done" && $this->areOpen) {
                $output .= "<p class=\"" . $this->yellow->page->getHtml("status") . "\">" . $this->yellow->text->getHtml("commentsStatus".ucfirst($this->yellow->page->get("status"))) . "</p>\n";
                $output .= "<form class=\"comments-form comment\" action=\"" . $this->yellow->page->getLocation(true) . "#form\" method=\"post\">\n";
                if ($this->yellow->config->get("commentsIconGravatar")) {
                    $output .= "<div class=\"comments-icon\"><img id=\"gravatar\" src=\"" . $this->getUserIcon($this->yellow->page->get("status") == "invalid" ? "" : $_REQUEST["from"]) . "\" width=\"" . $iconSize . "\" height=\"" . $iconSize . "\" data-default=\"" . rawurlencode($this->yellow->config->get("commentsIconGravatarDefault")) . "\" alt=\"Image\" /></div>\n";
                } else {
                    $output .= "<div class=\"comments-icon\"><img src=\"" . $this->getUserIcon($_REQUEST["from"]) . "\" width=\"" . $iconSize . "\" height=\"" . $iconSize . "\" alt=\"Image\" /></div>\n";
                }
                $output .= "<div class=\"comments-main\">\n";
                $output .= "<div class=\"comments-from\"><label for=\"from\">" . $this->yellow->text->getHtml("commentsEmail") . "</label><br /><input type=\"text\" size=\"40\" class=\"form-control\" name=\"from\" id=\"from\" value=\"" . htmlspecialchars($_REQUEST["from"]) . "\" /></div>\n";
                $output .= "<div class=\"comments-name\"><label for=\"name\">" . $this->yellow->text->getHtml("commentsName") . "</label><br /><input type=\"text\" size=\"40\" class=\"form-control\" name=\"name\" id=\"name\" value=\"" . htmlspecialchars($_REQUEST["name"]) . "\" /></div>\n";
                $output .= "<div class=\"comments-message\"><label for=\"message\">" . $this->yellow->text->getHtml("commentsHoneypot") . "</label><br /><textarea class=\"form-control\" name=\"message\" id=\"message\" rows=\"2\" cols=\"70\">" . htmlspecialchars($_REQUEST["message"]) . "</textarea></div>\n";
                $output .= "<div class=\"comments-comment\"><label for=\"comment\">" . $this->yellow->text->getHtml("commentsMessage") . "</label><br /><textarea class=\"form-control\" name=\"comment\" id=\"comment\" rows=\"7\" cols=\"70\" maxlength=\"" . $this->yellow->config->get("commentsMaxSize") . "\">" . htmlspecialchars($_REQUEST["comment"]) . "</textarea></div>\n";
                $output .= "<div class=\"comments-consent\"><input type=\"checkbox\" name=\"consent\" value=\"consent\" id=\"consent\"" . ($_REQUEST["consent"] ? " checked=\"checked\"" : "") . "> <label for=\"consent\">" . $this->yellow->text->getHtml("commentsConsent") . "</label></div>\n";
                $output .= "<div>\n";
                $output .= "<input type=\"hidden\" name=\"status\" value=\"send\" />\n";
                $output .= "<input type=\"submit\" value=\"" . $this->yellow->text->getHtml("commentsButton") . "\" class=\"btn contact-btn\" />\n";
                $output .= "</div>\n";
                $output .= "</div>\n";
                $output .= "</form>\n";
                $output .= "<p class=\"comments-info\">";
                $output .= $this->yellow->text->get("commentsPrivacy") . " ";
                $output .= $this->yellow->config->get("commentsIconGravatar") ? $this->yellow->text->get("commentsGravatar") . " " : "";
                $output .= $this->yellow->text->get("commentsMarkdown") . " ";
                $output .= !$this->yellow->config->get("commentsAutoPublish") ? $this->yellow->text->get("commentsManual") : "";
                $output .= "</p>\n";
            } else {
                $output .= "<p class=\"" . $this->yellow->page->getHtml("status") . "\">" . $this->yellow->text->getHtml("commentsStatus".ucfirst($this->yellow->page->get("status"))) . "</p>\n";
            }
        }
        return $output;
    }

    // Handle page extra data
    public function onParsePageExtra($page, $name) {
        $output = null;
        if ($name=="header") {
            $pluginLocation = $this->yellow->config->get("serverBase").$this->yellow->config->get("pluginLocation");
            $output .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$pluginLocation}comments.css\" />\n";
            $output .= "<script type=\"text/javascript\" defer=\"defer\" src=\"{$pluginLocation}comments-textarea.js\"></script>\n";
            if ($this->yellow->config->get("commentsIconGravatar")) $output .= "<script type=\"text/javascript\" defer=\"defer\" src=\"{$pluginLocation}comments-gravatar.js\"></script>\n";
        }
        if ($name=="comments") {
            $output = $this->onParseContentBlock($page, "comments", "", true);
        }
        return $output;
    }

    // Return Email
    function getEmail() {
        return $this->yellow->page->get("moderator") ? $this->yellow->page->get("moderator") : ($this->yellow->config->isExisting("commentsModerator") ? $this->yellow->config->get("commentsModerator") : $this->yellow->config->get("email"));
    }

    // Return file name from page object
    function getCommentFileName($page) {
        return dirname($page->fileName) . "/" . $this->yellow->config->get("commentsDir") . basename($page->fileName);
    }

    // Lock comments file
    function lockComments($page, $forceOpen) {
        if ($this->fileHandle)
            return;
        $fileName = $this->getCommentFileName($page);
        if ($forceOpen) @mkdir(dirname($fileName));
        if (file_exists($fileName) || $forceOpen)
            $this->fileHandle = @fopen($fileName, "c+");
        if ($this->fileHandle)
            flock($this->fileHandle, LOCK_EX);
    }

    // Unlock comments file
    function unlockComments() {
        if (!$this->fileHandle)
            return;
        flock($this->fileHandle, LOCK_UN);
        fclose($this->fileHandle);
        unset($this->fileHandle);
    }

    // Load comments from given file name
    function loadComments() {
        if (!$this->fileHandle)
            return;
        fseek($this->fileHandle, 0, SEEK_END);
        $length = ftell($this->fileHandle);
        fseek($this->fileHandle, 0, SEEK_SET);
        $contents = explode("\n\n---\n", str_replace("\r\n", "\n", $length>0 ? fread($this->fileHandle, $length) : ""));
//        more robust (doesn't need \n\n before ---), but slower; it requires...
//        preg_match_all("/---\n.+?---\n.((?!---\n).)*/s", ($length>0) ? fread($this->fileHandle, $length) : "", $temp);
//        $contents = $temp[0];
        $pageText = array_shift($contents);
        foreach ($contents as $content) {
            if (preg_match("/^(.+?)\n+---\n+(.*)/s", $content, $parts)) {
//            ... a different regex
//            if (preg_match("/^---\n(.+?)\n+---\n+(.*)/s", $content, $parts)) {
                $comment = new YellowComment;
                foreach (preg_split("/\n+/", $parts[1]) as $line) {
                    preg_match("/^\s*(.*?)\s*:\s*(.*?)\s*$/", $line, $matches);
                    if (!empty($matches[1]) && !strempty($matches[2])) {
                        $comment->set(lcfirst($matches[1]), $matches[2]);
                        if (lcfirst($matches[1]) == "created") $this->yellow->page->setLastModified(strtotime($matches[2]));
                    }
                }
                $comment->comment = trim($parts[2]);
                $comment->comment = preg_replace("/^-(-{3,})$/m", "$1", $comment->comment); // invert safety substitution
                $this->comments[] = $comment;
            }
        }
    }
    
    // Save comments
    function saveComments($checkSize) {
        if (!$checkSize || (strlenu($comment->comment) < $this->yellow->config->get("commentsMaxSize"))) {
            $status = "send";
            $this->lockComments($this->yellow->page, true);
            if ($this->pageText == "") {
                $this->pageText = @file_get_contents(strreplaceu("(.*)", "comments", $this->yellow->config->get("configDir").$this->yellow->config->get("newFile")));
                if ($this->pageText == "") {
                    $this->pageText = "---\nTitle: Comments\nParser: comments\n---\n";
                }
            }

            $timeout = time()-$this->yellow->config->get("commentsTimeout")*86400;
            $content = $this->pageText;
            foreach ($this->comments as $comment) {
                if ($comment->isPublished() || $timeout < strtotime($comment->get("created")) || $this->yellow->config->get("commentsTimeout") == 0) {
                    $content .= "\n\n---\n";
                    foreach ($comment->metaData as $key=>$value) {
                        $content .= ucfirst($key). ": " . $value . "\n";
                    }
                    $content .= "---\n";
                    $content .= $comment->comment . "\n";
                }
            }
            if ($this->fileHandle) {
                fseek($this->fileHandle, 0, SEEK_SET);
                fwrite($this->fileHandle, $content);
                ftruncate($this->fileHandle, ftell($this->fileHandle));
            } else {
                $status = "error";
            }
        } else {
            $status = "error";
        }
        return $status;
    }

    // Build comment from input
    function buildComment() {
        $comment = new YellowComment;
        $comment->set("name", filter_var(trim($_REQUEST["name"]), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
        $comment->set("from", filter_var(trim($_REQUEST["from"]), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
        $comment->set("created", date("Y-m-d H:i"));
        $comment->set("fingerprint", $_ENV["REMOTE_ADDR"] . "@" . $_SERVER["REQUEST_TIME_FLOAT"]);
        $comment->set("uid", hash("sha256", $this->yellow->toolbox->createSalt(64)));
        $comment->set("aid", hash("sha256", $this->yellow->toolbox->createSalt(64)));
        if (!$this->yellow->config->get("commentsAutoPublish")) $comment->set("published", "No");
        $comment->comment = str_replace("\r\n", "\n", trim($_REQUEST["comment"]));
        $comment->comment = preg_replace("/^-{3,}$/m", "-$0", $comment->comment); // safety substitution
        return $comment;
    }

    // verify comment for safe use
    function verifyComment($comment) {
        $status = "send";
        $name = $comment->get("name");
        $from = $comment->get("from");
        $message = $comment->comment;
        $consent = $_REQUEST["consent"];
        $spamFilter = $this->yellow->config->get("commentsSpamFilter");
        if (empty($name) || empty($from) || empty($message) || empty($consent)) $status = "incomplete";
        if (!empty($from) && !filter_var($from, FILTER_VALIDATE_EMAIL)) $status = "invalid";
        if (!empty($message) && preg_match("/$spamFilter/i", $message)) $status = "error";
        if (!empty($_REQUEST["message"])) $status = "error"; // honeypot
        return $status;
    }

    // Process user input
    function processSend() {
        if ($this->yellow->isCommandLine()) $this->yellow->page->error(500, "Static website not supported!");
        $aid = trim($_REQUEST["aid"]);
        $action = trim($_REQUEST["action"]);
        if ($aid) {
            $changed = false;
            foreach ($this->comments as &$comment) {
                if ($comment->get("aid") == $aid) {
                    if ($action == "remove") {
                        unset($comment);
                        $changed = true;
                        break;
                    } elseif ($action == "publish") {
                        $comment->set("published", null);
                        $changed = true;
                        if ($this->yellow->config->get("commentsAuthorNotification"))
                            $this->sendNotificationEmail($comment);
                        break;
                    }
                }
            }
            if ($changed) $this->saveComments(false);
        }
        $status = trim($_REQUEST["status"]);
        if ($status == "send") {
            $comment = $this->buildComment();
            $status = $this->verifyComment($comment);
            if (!$this->areOpen) $status = "closed";
            if ($status == "send") {
                $this->comments[] = $comment;
                $status = $this->saveComments(true);
            }
            if ($status == "send" && $this->getEmail()) $status = $this->sendEmail($comment);
            $this->yellow->page->setHeader("Last-Modified", $this->yellow->toolbox->getHttpDateFormatted(time()));
            $this->yellow->page->setHeader("Cache-Control", "no-cache, must-revalidate");
        } else {
            $status = $this->areOpen ? "none" : "closed";
        }
        $this->yellow->page->set("status", $status);
    }
    
    // Send comment email
    function sendEmail($comment) {
        $mailMessage = $comment->comment."\r\n";
        $mailMessage .= "-- \r\n";
        $mailMessage .= "Name: " . $comment->get("name") . "\r\n";
        $mailMessage .= "Mail: " . $comment->get("from") . "\r\n";
        $mailMessage .= "Uid:  " . $comment->get("uid") . "\r\n";
        $mailMessage .= "Fingerprint:  " . $comment->get("fingerprint") . "\r\n";
        $mailMessage .= "-- \r\n";
        if (!$this->yellow->config->get("commentsAutoPublish")) {
            $mailMessage.= "Publish: " . $this->yellow->page->getUrl() . "?aid=" . $comment->get("aid") . "&action=publish\r\n";
        } else {
            $mailMessage.= "Remove: " . $this->yellow->page->getUrl() . "?aid=" . $comment->get("aid") . "&action=remove\r\n";
        }
        $mailSubject = mb_encode_mimeheader("[".$this->yellow->config->get("sitename")."] " . $this->yellow->page->get("title"));
        $mailHeaders = "From: " . mb_encode_mimeheader($comment->get("name")) . " <" . $comment->get("from") . ">\r\n";
        $mailHeaders .= "X-Contact-Url: " . mb_encode_mimeheader($this->yellow->page->getUrl()) . "\r\n";
        $mailHeaders .= "X-Remote-Addr: " . mb_encode_mimeheader($_SERVER["REMOTE_ADDR"]) . "\r\n";
        $mailHeaders .= "Mime-Version: 1.0\r\n";
        $mailHeaders .= "Content-Type: text/plain; charset=utf-8\r\n";
        return mail($this->getEmail(), $mailSubject, $mailMessage, $mailHeaders) ? "done" : "error";
    }

    // Send notification email
    function sendNotificationEmail($comment) {
        $mailMessage = $this->yellow->text->get("commentsPublished")."\r\n\r\n";
        $mailMessage .= $this->yellow->page->getUrl() . "#" . $comment->get("uid") . "\r\n\r\n";
        $mailMessage .= "-- \r\n";
        $mailMessage .= $this->yellow->config->get("sitename") . "\r\n";
        $mailSubject = mb_encode_mimeheader("[".$this->yellow->config->get("sitename")."] " . $this->yellow->page->get("title"));
        $mailHeaders = "From: " . mb_encode_mimeheader($this->yellow->config->get("sitename")) . "<" . $this->yellow->config->get("email") . ">\r\n";
        $mailHeaders .= "Mime-Version: 1.0\r\n";
        $mailHeaders .= "Content-Type: text/plain; charset=utf-8\r\n";
        return mail($comment->get("from"), $mailSubject, $mailMessage, $mailHeaders) ? "done" : "error";
    }

    // Return number of visible comments
    function getCommentCount() {
        $count = 0;
        foreach ((array)$this->comments as $comment) {
            if ($comment->isPublished()) {
                $count++;
            }
        }
        return $count;
    }
    
    // Transform (markdown) text to HTML
    function transformText($page, $text) {
        if (class_exists("MarkdownExtraParser")) {
            $markdownHandler = new MarkdownExtraParser();
            $markdownHandler->no_markup = true;
            $markdownHandler->hard_wrap = true;
            $text = $markdownHandler->transform($text);
        } elseif (class_exists("ParsedownExtra")) {
            $markdownHandler = new ParsedownExtra();
            $markdownHandler->setSafeMode(true);
            $markdownHandler->setBreaksEnabled(true);
            $markdownHandler->setUrlsLinked(true);
            $text = $markdownHandler->text($text);
        }
//        Simpler, but allows shortcodes
//        if ($this->yellow->plugins->isExisting($this->yellow->config->get("parser"))) {
//            $markdownHandler = $this->yellow->plugins->get($this->yellow->config->get("parser"));
//            $page->parserSafeMode = true; // always disallow HTML in comments
//            $text = $markdownHandler->onParseContentRaw($page, $text);
//        }
        $text = preg_replace('/<h\d>(.*)<\/h\d>/', '<p><strong>$1</strong></p>', $text); // no headers, please
        return $text;
    }

    function getUserIcon($email) {
        if ($this->yellow->config->get("commentsIconGravatar")) {
            $base = "//gravatar.com/avatar/";
            return $base . hash("md5", strtolower(trim($email))) . "?s=" . intval($this->yellow->config->get("commentsIconSize")) . "&d=" . rawurlencode($this->yellow->config->get("commentsIconGravatarDefault"));
        } else {
            return "data:image/png;base64," . base64_encode($this->getUserIconPng($email));
        }
    }

    // Get user icon without any service
    function getUserIconPng($email) {
        $hash = hexdec(substr(hash("sha256", strtolower(trim($email))), 0, 6));
        $color_background = 0xFFFFFF;
        $colors = [0xFF0000, 0xCF0000, 0x00FF00, 0x00CF00, 0x0000FF, 0x0000CF, 0xFFCF000, 0xCFFF00, 0x00FFCF, 0x00CFFF, 0xCF00FF, 0xFF00CF];
        $color_foreground = $colors[($hash >> 15) % count($colors)];
        $multiplicator = ceil($this->yellow->config->get("commentsIconSize")/40);
        $size = 5*8*$multiplicator;
        $png = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52";
        $png .= pack("N", $size) . pack("N", $size);
        $png .= "\x01\x03\x00\x00\x00";
        $png .= hash("crc32b", substr($png, 0xc), true);
        $png .= "\x00\x00\x00\x06\x50\x4c\x54\x45";
        $png .= substr(pack("N", $color_foreground), 1);
        $png .= substr(pack("N", $color_background), 1);
        $png .= hash("crc32b", substr($png, 0x25), true);
        $map = [0, 1, 2, 1, 0];
        for ($y=0; $y < 5; $y++) {
            $line = "\x00";
            for ($x=0; $x < 5; $x++) {
                $line .= str_repeat(((($hash>>($y*5 + $map[$x])) & 1) == 1) ? "\xff" : "\x00", $multiplicator);
            }
            $pixel .= str_repeat($line, 8*$multiplicator);
        }
        $pixel = gzcompress($pixel, 6);
        $length = strlen($pixel);
        $png .= pack("N", $length);
        $png .= "\x49\x44\x41\x54" . $pixel;
        $png .= hash("crc32b", substr($png, 0x37), true);
        $png .= "\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60\x82";
        return $png;
    }
}

$yellow->plugins->register("comments", "YellowComments", YellowComments::VERSION);
