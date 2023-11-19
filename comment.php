<?php
// Comment extension, https://github.com/GiovanniSalmeri/yellow-comment

class YellowComment {
    const VERSION = "0.8.19";
    public $yellow;         //access to API

    var $comments;
    var $fileHandle;
    var $areOpen;

    // Handle initialisation
    public function onLoad($yellow) {
    $this->yellow = $yellow;
        $this->yellow->system->setDefault("commentModerator", "");
        $this->yellow->system->setDefault("commentDirectory", "comment/");
        $this->yellow->system->setDefault("commentAutoPublish", "0");
        $this->yellow->system->setDefault("commentMaxSize", "5000");
        $this->yellow->system->setDefault("commentTimeout", "0");
        $this->yellow->system->setDefault("commentOpening", "30");
        $this->yellow->system->setDefault("commentAuthorNotification", "1");
        $this->yellow->system->setDefault("commentSpamFilter", "href=|url=");
        $this->yellow->system->setDefault("commentIconSize", "80");
        $this->yellow->system->setDefault("commentIconGravatar", "0");
        $this->yellow->system->setDefault("commentIconGravatarDefault", "mp");
        $this->yellow->system->setDefault("commentConsent", "0");
        $this->yellow->language->setDefaults([
            "Language: en",
            "CommentCommentList: Comments:",
            "CommentStatusNone: Interested to discuss? Leave a comment.",
            "CommentStatusClosed: Comments are closed.",
            "CommentStatusDone: Thanks for your feedback.",
            "CommentPrivacy: Your email will not be published nor shared with anyone.",
            "CommentGravatar: Please use the service <a href=\"https://en.gravatar.com\">Gravatar</a> if you would like a photo beside your name.",
            "CommentMarkdown: In your text you can use <code>*italic*</code>, <code>**bold**</code>, <code>[links](http://example.org)</code>.",
            "CommentManual: These comments are moderated and published manually as soon as possible.",
            "CommentPublished: Your comment has been published. Thank you very much!",
            "CommentHoneypot: Please leave this field blank:",
            "CommentName: Name:",
            "CommentEmail: Email:",
            "CommentMessage: Message:",
            "CommentConsent: I consent that this website stores my message.",
            "CommentButton: Send message",
            "CommentStatusIncomplete: Please fill out all fields.",
            "CommentStatusInvalid: Please enter a valid email.",
            "CommentStatusError: Your comment could not be sent, please try again later.",
            "CommentStatusToolong: Your comment is too long, please be more concise...",
            "Language: de",
            "CommentCommentList: Kommentare:",
            "CommentStatusNone: Interesse an einer Diskussion? Schreibe einen Kommentar.",
            "CommentStatusClosed: Kommentare sind geschlossen.",
            "CommentStatusDone: Danke für die Rückmeldung.",
            "CommentPrivacy: Die E-Mail-Adresse wird nicht veröffentlicht noch an Dritte weitergegeben.",
            "CommentGravatar: Benutze <a href=\"https://de.gravatar.com\">Gravatar</a>, wenn ein Bild neben deinem Namen erscheinen soll.",
            "CommentMarkdown: In deinem Text kannst du <code>*Kursivschrift*</code>, <code>**Fettschrift**</code> und <code>[Links](http://example.org)</code> verwenden.",
            "CommentManual: Der Kommentar wird moderiert und so bald wie möglich freigeschaltet.",
            "CommentPublished: Dein Kommentar wurde veröffentlicht. Danke schön!",
            "CommentHoneypot: Dieses Feld bitte leer lassen:",
            "CommentName: Name:",
            "CommentEmail: E-Mail:",
            "CommentMessage: Nachricht:",
            "CommentConsent: Ich stimme zu, dass diese Webseite meine Nachricht speichert.",
            "CommentButton: Nachricht absenden",
            "CommentStatusIncomplete: Bitte alle Felder ausfüllen.",
            "CommentStatusInvalid: Bitte eine gültige E-Mail angeben.",
            "CommentStatusError: Nachricht konnte nicht versandt werden, versuche es später erneut.",
            "CommentStatusToolong: Dein Kommentar ist zu lang, bitte fasse dich besser zusammen...",
            "Language: fr",
            "CommentCommentList: Commentaires:",
            "CommentStatusNone: Envie de discuter? Laissez un commentaire.",
            "CommentStatusClosed: Les commentaires sont fermés.",
            "CommentStatusDone: Merci de votre participation.",
            "CommentPrivacy: L'adresse email ne sera pas publiée ni partagée avec autrui.",
            "CommentGravatar: Si vous voulez une photo à côté de votre nom, utilisez le service <a href=\"https://fr.gravatar.com\">Gravatar</a>.",
            "CommentMarkdown: Dans votre texte vous pouvez utiliser l'<code>*italique*</code>, le <code>**gras**</code>, les <code>[liens](http://example.org)</code>.",
            "CommentManual: Les commentaires sont modérés et publiés manuellement dès que possible.",
            "CommentPublished: Votre commentaire a été publié. Merci de votre participation!",
            "CommentHoneypot: Laissez ce champ vide, s'il vous plaît:",
            "CommentName: Nom:",
            "CommentEmail: Email:",
            "CommentMessage: Message:",
            "CommentConsent: Je consens à ce que ce site stocke mon message.",
            "CommentButton: Envoyer le message",
            "CommentStatusIncomplete: S'il vous plaît, veuillez remplir tous les champs.",
            "CommentStatusInvalid: S'il vous plaît, veuillez entrer une adresse email valide.",
            "CommentStatusError: Votre message n'a pas pu être envoyé, réessayez plus tard s'il vous plaît.",
            "CommentStatusToolong: Votre commentaire est trop long, veuillez être plus concis...",
            "Language: it",
            "CommentCommentList: Commenti:",
            "CommentStatusNone: Vuoi contribuire alla discussione? Lascia un commento!",
            "CommentStatusClosed: I commenti sono chiusi.",
            "CommentStatusDone: Grazie per il contributo!",
            "CommentPrivacy: Il tuo indirizzo di posta elettronica non sarà né pubblicato né ceduto a terzi.",
            "CommentGravatar: Usa il servizio <a href=\"https://it.gravatar.com\">Gravatar</a> se vuoi che sia mostrata una foto accanto al tuo nome.",
            "CommentMarkdown: Nel testo puoi usare il <code>*corsivo*</code>, il <code>**neretto**</code>, i  <code>[collegamenti](http://example.org)</code>.",
            "i collegamenti <code>&lt;http://example.org&gt;</code> e altri elementi.",
            "CommentManual: I commenti sono moderati e pubblicati manualmente appena possibile.",
            "CommentPublished: Il tuo commento è stato pubblicato. Grazie per il contributo!",
            "CommentHoneypot: Lascia questo campo vuoto:",
            "CommentName: Nome:",
            "CommentEmail: Email:",
            "CommentMessage: Messaggio:",
            "CommentConsent: Acconsento alla registrazione del mio messaggio in questo sito.",
            "CommentButton: Invia il messaggio",
            "CommentStatusIncomplete: Compila per favore tutti i campi.",
            "CommentStatusInvalid: Inserisci per favore un indirizzo email valido.",
            "CommentStatusError: C'è stato un problema nell'invio del messaggio. Riprova per favore più tardi.",
            "CommentStatusToolong: Il commento è troppo lungo, prova ad essere più conciso...",
        ]);
    }

    // Handle page content parsing of custom block
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="comment" && ($type=="block" || $type=="inline") && !preg_match("/exclude/i", $page->get("comment"))) {
            list($opening) = $this->yellow->toolbox->getTextArguments($text);
            if ($opening == "") $opening = $this->yellow->system->get("commentOpening");
            $this->areOpen = time()-$opening*86400 < strtotime($this->yellow->page->get("published")) || !$opening;

            $this->lockComments($this->yellow->page, false);
            $this->loadComments();
            $this->processSend();
            if ($this->yellow->page->get("status") == "done") { // post/redirect/get
                setcookie("status", "done");
                $this->yellow->page->status(303, $this->yellow->page->getLocation(true));
            }
            $this->unlockComments();
            $iconSize = $this->yellow->system->get("commentIconSize");
            $maxSize = $this->yellow->system->get("commentMaxSize");

            $output = "<div class=\"comment\" id=\"comment\">\n";
            $output .= "<h2><span>" . $this->yellow->language->getText("commentCommentList") . " " . $this->getCommentCount() . "</span></h2>\n";
            foreach ($this->comments as $comment) {
                if ($comment["meta"]["published"] !== "No") {
                    $output .= "<div class=\"comment\" id=\"" . htmlspecialchars($comment["meta"]["uid"]) . "\">\n";
                    $output .= "<div class=\"comment-icon\"><img src=\"" . $this->getUserIcon($comment["meta"]["from"]) . "\" width=\"" . $iconSize . "\" height=\"" . $iconSize . "\" alt=\"Image\" /></div>\n";
                    $output .= "<div class=\"comment-main\">\n";
                    $output .= "<div class=\"comment-name\">" . htmlspecialchars($comment["meta"]["name"]) . "</div>\n";
                    $output .= "<div class=\"comment-date\">".$this->yellow->language->getDateFormatted(strtotime($comment["meta"]["created"]), $this->yellow->language->getText("coreDateFormatLong")) . "</div>\n";
                    $output .= "<div class=\"comment-content\">" . $this->toHtml($comment["text"]) . "</div>\n";
                    $output .= "</div>\n";
                    $output .= "</div>\n";
                }
            }
            $output .= "</div>\n";
            if ($this->yellow->toolbox->getCookie("status")=="done") {
                setcookie("status", "", 1);
                $this->yellow->page->set("status", "done");
            }
            $output .= "<div class=\"content separate\" id=\"form\"></div>\n";
            if ($this->yellow->page->get("status") != "done" && $this->areOpen) {
                $output .= "<p class=\"" . $this->yellow->page->getHtml("status") . "\">" . $this->yellow->language->getTextHtml("commentStatus".ucfirst($this->yellow->page->get("status"))) . "</p>\n";
                $output .= "<form class=\"comment-form comment\" action=\"" . $this->yellow->page->getLocation(true) . "#form\" method=\"post\">\n";
                if ($this->yellow->system->get("commentIconGravatar")) {
                    $output .= "<div class=\"comment-icon\"><img id=\"gravatar\" src=\"" . $this->getUserIcon($this->yellow->page->get("status") == "invalid" ? "" : $this->yellow->page->getRequest("from")) . "\" width=\"" . $iconSize . "\" height=\"" . $iconSize . "\" data-default=\"" . rawurlencode($this->yellow->system->get("commentIconGravatarDefault")) . "\" alt=\"Image\" /></div>\n";
                } else {
                    $output .= "<div class=\"comment-icon\"><img src=\"" . $this->getUserIcon($this->yellow->page->getRequest("from")) . "\" width=\"" . $iconSize . "\" height=\"" . $iconSize . "\" alt=\"Image\" /></div>\n";
                }
                $output .= "<div class=\"comment-main\">\n";
                $output .= "<div class=\"comment-from\"><label for=\"from\">" . $this->yellow->language->getTextHtml("commentEmail") . "</label><br /><input type=\"text\" size=\"40\" class=\"form-control\" name=\"from\" id=\"from\" value=\"" . $this->yellow->page->getRequestHtml("from") . "\" /></div>\n";
                $output .= "<div class=\"comment-name\"><label for=\"name\">" . $this->yellow->language->getTextHtml("commentName") . "</label><br /><input type=\"text\" size=\"40\" class=\"form-control\" name=\"name\" id=\"name\" value=\"" . $this->yellow->page->getRequestHtml("name") . "\" /></div>\n";
                $output .= "<div class=\"comment-message\"><label for=\"message\">" . $this->yellow->language->getTextHtml("commentHoneypot") . "</label><br /><textarea class=\"form-control\" name=\"message\" id=\"message\" rows=\"2\" cols=\"70\">" . $this->yellow->page->getRequestHtml("message") . "</textarea></div>\n";
                $output .= "<div class=\"comment-comment\"><label for=\"comment\">" . $this->yellow->language->getTextHtml("commentMessage") . "</label><br /><textarea class=\"form-control\" name=\"comment\" id=\"comment\" rows=\"7\" cols=\"70\" maxlength=\"" . $maxSize . "\">" . $this->yellow->page->getRequestHtml("comment") . "</textarea><small class=\"comment-charcount\">0 / " . $maxSize . "</small></div>\n";
                $output .= "";
                $output .= $this->yellow->system->get("commentConsent") ? "<div class=\"comment-consent\"><input type=\"checkbox\" name=\"consent\" value=\"consent\" id=\"consent\"" . ($this->yellow->page->isRequest("consent") ? " checked=\"checked\"" : "") . "> <label for=\"consent\">" . $this->yellow->language->getTextHtml("commentConsent") . "</label></div>\n" : "";
                $output .= "<div>\n";
                $output .= "<input type=\"hidden\" name=\"status\" value=\"send\" />\n";
                $output .= "<input type=\"submit\" value=\"" . $this->yellow->language->getTextHtml("commentButton") . "\" class=\"btn contact-btn\" />\n";
                $output .= "</div>\n";
                $output .= "</div>\n";
                $output .= "</form>\n";
                $output .= "<p class=\"comment-info\">";
                $output .= $this->yellow->language->getText("commentPrivacy") . " ";
                $output .= $this->yellow->system->get("commentIconGravatar") ? $this->yellow->language->getText("commentGravatar") . " " : "";
                $output .= $this->yellow->language->getText("commentMarkdown") . " ";
                $output .= !$this->yellow->system->get("commentAutoPublish") ? $this->yellow->language->getText("commentManual") : "";
                $output .= "</p>\n";
            } else {
                $output .= "<p class=\"" . $this->yellow->page->getHtml("status") . "\">" . $this->yellow->language->getTextHtml("commentStatus".ucfirst($this->yellow->page->get("status"))) . "</p>\n";
            }
        }
        return $output;
    }

    // Handle page extra data
    public function onParsePageExtra($page, $name) {
        $output = null;
        if ($name=="header") {
            $extensionLocation = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreExtensionLocation");
            $output .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$extensionLocation}comment.css\" />\n";
            $output .= "<script type=\"text/javascript\" defer=\"defer\" src=\"{$extensionLocation}comment-textarea.js\"></script>\n";
            if ($this->yellow->system->get("commentIconGravatar")) $output .= "<script type=\"text/javascript\" defer=\"defer\" src=\"{$extensionLocation}comment-gravatar.js\"></script>\n";
        }
        if ($name=="comment") {
            $output = $this->onParseContentShortcut($page, "comment", "", "block");
        }
        return $output;
    }

    // Return Email
    function getEmail() {
        return $this->yellow->page->get("moderator") ? $this->yellow->page->get("moderator") : ($this->yellow->system->isExisting("commentModerator") ? $this->yellow->system->get("commentModerator") : $this->yellow->system->get("email"));
    }

    // Return file name from page object
    function getCommentFileName($page) {
        return dirname($page->fileName) . "/" . $this->yellow->system->get("commentDirectory") . basename($page->fileName);
    }

    // Lock comments file
    function lockComments($page, $forceOpen) {
        if ($this->fileHandle) return;
        $fileName = $this->getCommentFileName($page);
        if ($forceOpen) @mkdir(dirname($fileName));
        if (file_exists($fileName) || $forceOpen) $this->fileHandle = @fopen($fileName, "c+");
        if ($this->fileHandle) flock($this->fileHandle, LOCK_EX);
    }

    // Unlock comments file
    function unlockComments() {
        if (!$this->fileHandle) return;
        flock($this->fileHandle, LOCK_UN);
        fclose($this->fileHandle);
        unset($this->fileHandle);
    }

    // Load comments
    function loadComments() {
        $this->comments = [];
        if (!$this->fileHandle) return;
        $length = fstat($this->fileHandle)['size'];
        $contents = array_slice(explode("\n\n---\n", str_replace("\r\n", "\n", $length>0 ? fread($this->fileHandle, $length) : "")), 1);
        foreach ($contents as $content) {
            if (preg_match("/^(.+?)\n+---\n+(.*)/s", $content, $parts)) {
                $comment = [];
                foreach (explode("\n", $parts[1]) as $line) {
                    if (preg_match("/^\s*(.*?)\s*:\s*(.*?)\s*$/", $line, $matches) && $matches[1] && $matches[2]) {
                        $comment["meta"][lcfirst($matches[1])] = $matches[2];
                    }
                }
                if (isset($comment["meta"]["created"])) {
                    $this->yellow->page->setLastModified(strtotime($comment["meta"]["created"]));
                }
                $comment["text"] = trim($parts[2]);
                $comment["text"] = preg_replace("/^-(-{3,})$/m", "$1", $comment["text"]); // revert safety substitution
                $this->comments[] = $comment;
            }
        }
    }
    
    // Save comments
    function saveComments() {
        $status = "send";
        $this->lockComments($this->yellow->page, true);
        $timeout = time()-$this->yellow->system->get("commentTimeout")*86400;
        $content = "---\nTitle: Comment\nStatus: unlisted\n---\n";
        foreach ($this->comments as $comment) {
            if ($comment["meta"]["published"] !== "No" || $timeout < strtotime($comment["meta"]["created"]) || $this->yellow->system->get("commentTimeout") == 0) {
                $content .= "\n\n---\n";
                foreach ($comment["meta"] as $key=>$value) {
                    $content .= ucfirst($key). ": " . $value . "\n";
                }
                $content .= "---\n";
                $content .= $comment["text"] . "\n";
            }
        }
        if ($this->fileHandle) {
            rewind($this->fileHandle);
            fwrite($this->fileHandle, $content);
            ftruncate($this->fileHandle, ftell($this->fileHandle));
        } else {
            $status = "error";
        }
        return $status;
    }

    // Build comment from input
    function buildComment() {
        $comment = [];
        $comment["meta"]["name"] = filter_var(trim($this->yellow->page->getRequest("name")), FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
        $comment["meta"]["from"] = filter_var(trim($this->yellow->page->getRequest("from")), FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
        $comment["meta"]["created"] = date("Y-m-d H:i");
        $comment["meta"]["published"] = $this->yellow->system->get("commentAutoPublish") ? $comment["meta"]["created"] : "No";
        $comment["meta"]["uid"] = md5($this->yellow->toolbox->getServer("REMOTE_ADDR").uniqid());
        $comment["meta"]["aid"] = md5($this->yellow->toolbox->getServer("REMOTE_ADDR").uniqid());
        $comment["text"] = str_replace("\r\n", "\n", trim($this->yellow->page->getRequest("comment")));
        $comment["text"] = preg_replace("/^-{3,}$/m", "-$0", $comment["text"]); // safety substitution
        return $comment;
    }

    // Verify comment for safe use
    function verifyComment($comment) {
        $name = $comment["meta"]["name"];
        $from = $comment["meta"]["from"];
        $text = $comment["text"];
        $consent = $this->yellow->page->getRequest("consent");
        $spamFilter = $this->yellow->system->get("commentSpamFilter");
        if (is_string_empty($name) || is_string_empty($from) || is_string_empty($text) || (is_string_empty($consent) && $this->yellow->system->get("commentConsent"))) {
            return "incomplete";
        } elseif (!is_string_empty($from) && !filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return "invalid";
        } elseif (!is_string_empty($text) && preg_match('/'.str_replace(['\\', '/'], ['\\\\', '\\/'], $spamFilter).'/i', $text)) {
            return "error";
        } elseif (!is_string_empty($this->yellow->page->getRequest("message"))) {
            return "error"; // honeypot
        } elseif (strlenu($text) > $this->yellow->system->get("commentMaxSize")) {
            return "toolong"; // should be avoided by maxlenght in textarea
        } else {
            return "send";
        }
    }

    // Process user input
    function processSend() {
        if ($this->yellow->lookup->isCommandLine()) $this->yellow->page->error(500, "Static website not supported!");
        $aid = $this->yellow->page->getRequest("aid");
        $action = $this->yellow->page->getRequest("action");
        if ($aid) {
            foreach ($this->comments as $key => &$comment) {
                if ($comment["meta"]["aid"] == $aid) {
                    if ($action == "remove") {
                        unset($this->comments[$key]);
                        $this->saveComments();
                        break;
                    } elseif ($action == "publish") {
                        $comment["meta"]["published"] = date("Y-m-d H:i");
                        $this->saveComments();
                        if ($this->yellow->system->get("commentAuthorNotification")) {
                            $this->sendNotificationEmail($comment);
                        }
                        break;
                    }
                }
            }
        }
        $status = $this->yellow->page->getRequest("status");
        if ($status == "send") {
            $comment = $this->buildComment();
            if (!$this->areOpen) {
                $status = "closed";
            } else {
                $status = $this->verifyComment($comment);
            }
            if ($status == "send") {
                $this->comments[] = $comment;
                $status = $this->saveComments();
            }
            if ($status == "send" && $this->getEmail()) {
                $status = $this->sendEmail($comment);
            }
            $this->yellow->page->setHeader("Last-Modified", $this->yellow->toolbox->getHttpDateFormatted(time()));
            $this->yellow->page->setHeader("Cache-Control", "no-cache, must-revalidate");
        } else {
            $status = $this->areOpen ? "none" : "closed";
        }
        $this->yellow->page->set("status", $status);
    }
    
    // Send comment email
    function sendEmail($comment) {
        $mailMessage = $comment["text"]."\r\n";
        $mailMessage .= "-- \r\n";
        $mailMessage .= "Name: " . $comment["meta"]["name"] . "\r\n";
        $mailMessage .= "Mail: " . $comment["meta"]["from"] . "\r\n";
        $mailMessage .= "Uid:  " . $comment["meta"]["uid"] . "\r\n";
        $mailMessage .= "-- \r\n";
        if (!$this->yellow->system->get("commentAutoPublish")) {
            $mailMessage.= "Publish: " . $this->yellow->page->getUrl() . "?aid=" . $comment["meta"]["aid"] . "&action=publish\r\n";
        } else {
            $mailMessage.= "Remove: " . $this->yellow->page->getUrl() . "?aid=" . $comment["meta"]["aid"] . "&action=remove\r\n";
        }
        $mailHeaders = array(
            "To" => $this->getEmail(),
            "From" => $this->yellow->system->get("sitename")." <".$this->yellow->system->get("email").">",
            "Reply-To" => $comment["meta"]["name"]." <".$comment["meta"]["from"].">",
            "Subject" => "[".$this->yellow->system->get("sitename")."] ".$this->yellow->page->get("title"),
            "Date" => date(DATE_RFC2822),
            "Mime-Version" => "1.0",
            "Content-Type" => "text/plain; charset=utf-8",
            "X-Request-Url" => $this->yellow->page->getUrl());
        return $this->yellow->toolbox->mail("comment", $mailHeaders, $mailMessage) ? "done" : "error";
    }

    // Send notification email
    function sendNotificationEmail($comment) {
        $mailMessage = $this->yellow->language->getText("commentPublished")."\r\n\r\n";
        $mailMessage .= $this->yellow->page->getUrl() . "#" . $comment["meta"]["uid"] . "\r\n\r\n";
        $mailMessage .= "-- \r\n";
        $mailMessage .= $this->yellow->system->get("sitename") . "\r\n";
        $mailHeaders = array(
            "To" => $comment["meta"]["name"]." <".$comment["meta"]["from"].">",
            "From" => $this->yellow->system->get("sitename")." <".$this->yellow->system->get("email").">",
            "Subject" => "[".$this->yellow->system->get("sitename")."] ".$this->yellow->page->get("title"),
            "Date" => date(DATE_RFC2822),
            "Mime-Version" => "1.0",
            "Content-Type" => "text/plain; charset=utf-8");
        return $this->yellow->toolbox->mail("comment", $mailHeaders, $mailMessage) ? "done" : "error";
    }

    // Return number of visible comments
    function getCommentCount() {
        $count = 0;
        foreach ($this->comments as $comment) {
            if ($comment["meta"]["published"] !== "No") {
                $count++;
            }
        }
        return $count;
    }
    
    // Transform a tiny subset of Markdown
    function toHtml($text) {
        $text = htmlspecialchars($text);
        $text = preg_replace_callback('/\\\[\\\n]/', function($m) { return $m[0] == "\\\\" ? "\\" : "<br />\n"; }, $text);
        $text = preg_replace("/\*\*(.+?)\*\*/", "<b>$1</b>", $text);
        $text = preg_replace("/\*(.+?)\*/", "<i>$1</i>", $text);
        $text = preg_replace("/(?<!\()(https?:\/\/[^ )]+)(?!\))/", "<a href=\"$1\">$1</a>", $text);
        $text = preg_replace("/\[(.*?)\]\((https?:\/\/[^ )]+)\)/", "<a href=\"$2\">$1</a>", $text);
        $text = preg_replace("/(\S+@\S+\.[a-z]+)/", "<a href=\"mailto:$1\">$1</a>", $text);
        return $text;
    }

    function getUserIcon($email) {
        if ($this->yellow->system->get("commentIconGravatar")) {
            $base = "//gravatar.com/avatar/";
            return $base . hash("md5", strtolower(trim($email))) . "?s=" . $this->yellow->system->get("commentIconSize") . "&d=" . rawurlencode($this->yellow->system->get("commentIconGravatarDefault"));
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
        $multiplicator = ceil($this->yellow->system->get("commentIconSize")/40);
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
        $pixel = "";
        for ($y=0; $y < 5; $y++) {
            $line = "\x00";
            for ($x=0; $x < 5; $x++) {
                $line .= str_repeat(((($hash>>($y*5 + $map[$x])) & 1) == 1) ? "\xff" : "\x00", $multiplicator);
            }
            $pixel .= str_repeat($line, 8*$multiplicator);
        }
        $pixel = gzcompress($pixel, 6);
        $png .= pack("N", strlen($pixel));
        $png .= "\x49\x44\x41\x54" . $pixel;
        $png .= hash("crc32b", substr($png, 0x37), true);
        $png .= "\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60\x82";
        return $png;
    }
}
