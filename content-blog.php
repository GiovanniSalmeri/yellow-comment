<?php $commentHandler = $yellow->plugins->get("comments") ?>
<?php $commentHandler->lockComments($yellow->page, false) ?>
<?php $commentHandler->loadComments() ?>
<?php $commentHandler->processSend() ?>
<?php $commentHandler->unlockComments() ?>
<div class="content main">
<?php $yellow->page->set("entryClass", "entry") ?>
<?php if($yellow->page->isExisting("tag")): ?>
<?php foreach(preg_split("/,\s*/", $yellow->page->get("tag")) as $tag) { $yellow->page->set("entryClass", $yellow->page->get("entryClass")." ".$yellow->toolbox->normaliseArgs($tag, false)); } ?>
<?php endif ?>
<div class="<?php echo $yellow->page->getHtml("entryClass") ?>">
<div class="entry-header"><h1><?php echo $yellow->page->getHtml("titleContent") ?></h1></div>
<div class="entry-meta"><?php echo htmlspecialchars($yellow->page->getDate("published")) ?> <?php echo $yellow->text->getHtml("blogBy") ?> <?php $authorCounter = 0; foreach(preg_split("/,\s*/", $yellow->page->get("author")) as $author) { if(++$authorCounter>1) echo ", "; echo "<a href=\"".$yellow->page->getPage("blog")->getLocation().$yellow->toolbox->normaliseArgs("author:$author")."\">".htmlspecialchars($author)."</a>"; } ?></div>
<div class="entry-content"><?php echo $yellow->page->getContent() ?></div>
<div class="entry-footer">
<?php if($yellow->page->isExisting("tag")): ?>
<p><?php echo $yellow->text->getHtml("blogTag") ?> <?php $tagCounter = 0; foreach(preg_split("/,\s*/", $yellow->page->get("tag")) as $tag) { if(++$tagCounter>1) echo ", "; echo "<a href=\"".$yellow->page->getPage("blog")->getLocation().$yellow->toolbox->normaliseArgs("tag:$tag")."\">".htmlspecialchars($tag)."</a>"; } ?></p>
<?php endif ?>
</div>
</div>
</div>

<a name="comments"></a>

<!-- comments begin -->

<div class="comments">
<h1><span><?php echo $this->yellow->text->get("commentsComments")." ".$commentHandler->getCommentCount() ?></span></h1>
<?php $i = 0; ?>
<?php foreach($commentHandler->comments as $comment) { ?> 
<?php if($comment->isPublished() && !$commentHandler->isBlacklisted($comment)) { ?>
<div class="comment <?php $i++; if($i&1) { echo 'odd';} else {echo 'even';} ?>">
<a name="<?php echo $comment->getHtml("uid") ?>"></a>
<div class="commenticon"><img src="<?php echo $commentHandler->getUserIcon($comment) ?>"/></div>
<div class="commentname">
<a href="<?php echo ($comment->getHtml("url")=="")?$yellow->page->getLocation():$comment->getHtml("url")?>"><?php echo $comment->getHtml("name") ?></a>:</div>
<div class="commentcontent"><?php echo $commentHandler->transformText($yellow->page, $comment->comment) ?></div>
<div class="commentdate"><?php echo $this->yellow->text->normaliseDate($comment->get("created")) ?></div>
</div>
<?php } ?>
<?php } ?>
</div>

<!-- comments end -->

<?php if($yellow->page->get("parser")!="comments" && !$commentHandler->isWebinterface($yellow->page)): ?>
<?php if($yellow->page->get("status")!="done"): ?>

<div class="content seperate"></div>

<p class="comments_status"><?php echo $yellow->page->getHtml("commentsStatus") ?><p>

<form class="comments-form" action="<?php echo htmlspecialchars($yellow->page->getLocation()) ?>" method="post">
<p class="comments-name"><label for="name"><?php echo $yellow->text->getHtml("contactName") ?></label><br /><input type="text" class="form-control<?php echo $commentHandler->required("name", " commentrequired") ?>" name="name" id="name" value="<?php echo htmlspecialchars($_REQUEST["name"]) ?>" /></p>
<p class="comments-from"><label for="from"><?php echo $yellow->text->getHtml("contactEmail") ?></label><br /><input type="text" class="form-control<?php echo $commentHandler->required("from", " commentrequired") ?>" name="from" id="from" value="<?php echo htmlspecialchars($_REQUEST["from"]) ?>" /></p>
<p class="comments-url"><label for="url"><?php echo $yellow->text->getHtml("contactUrl") ?></label><br /><input type="text" class="form-control<?php echo $commentHandler->required("url", " commentrequired") ?>" name="url" id="url" value="<?php if (htmlspecialchars($_REQUEST["url"]) == "") { echo "http://"; } else {echo htmlspecialchars($_REQUEST["url"]);} ?>" /></p>
<p class="comments-comment"><label for="comment"><?php echo $yellow->text->getHtml("contactMessage") ?></label><br /><textarea class="form-control<?php echo $commentHandler->required("comment", " required") ?>" name="comment" id="comment" rows="7" cols="70"><?php echo htmlspecialchars($_REQUEST["comment"]) ?></textarea></p>
<input type="hidden" name="beitrag" value="<?php echo $yellow->page->get('pageFile')?>" />
<input type="hidden" name="status" value="send" />
<input type="submit" value="<?php echo $yellow->text->getHtml("contactButton") ?>" class="btn contact-btn" />
</form>
<p class="comments_info">
<?php echo ($this->yellow->config->get("commentsAutoPublish")!="1")?$this->yellow->text->get("commentsManual"):"" ?>
</p>
<?php else: ?>
<p class="comments_status"><?php echo $yellow->page->getHtml("commentsStatus") ?><p>
<?php endif ?>
<?php endif ?>
</div>

