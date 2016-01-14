<?php $commentHandler = $yellow->plugins->get("Comments") ?>
<?php $commentHandler->processSend($yellow->page->get("pageFile")) ?>
<?php $comments = $commentHandler->loadComments($yellow->page->get("pageFile")) ?>
<div class="content main">
<?php $yellow->page->set("entryClass", "entry") ?>
<?php if($yellow->page->isExisting("tag")): ?>
<?php foreach(preg_split("/,\s*/", $yellow->page->get("tag")) as $tag) { $yellow->page->set("entryClass", $yellow->page->get("entryClass")." ".$yellow->toolbox->normaliseArgs($tag, false)); } ?>
<?php endif ?>
<div class="<?php echo $yellow->page->getHtml("entryClass") ?>">
<div class="entry-header">
<h1 class="header_title"><span><?php echo $yellow->page->getHtml("titleContent") ?></span></h1>
<h1 class="komm_title"><a href="#comments">
<?php echo $commentHandler->getCommentCount($comments) ?>
</a></h1>
</div>
<div class="entry-content"><?php echo $yellow->page->getContent() ?></div>
<div class="entry-footer">
	<?php if($yellow->page->isExisting("tag")): ?>
		&nbsp;&nbsp;<i class="fa fa-tags"></i> <?php $tagCounter = 0; foreach(preg_split("/,\s*/", $yellow->page->get("tag")) as $tag) { if(++$tagCounter>1) echo ", "; echo "<a href=\"".$yellow->page->getParentTop()->getLocation().$yellow->toolbox->normaliseArgs("tag:$tag")."\">".htmlspecialchars($tag)."</a>"; } ?>
	<?php endif ?>
</div>
<div class="content seperate">
</div>
</div>

<a name="comments"></a>

<!-- comments begin -->

<div class="comments">
<h1><span><?php echo $this->yellow->text->get("commentsComments")." ".$commentHandler->getCommentCount($comments) ?></span></h1>
<?php foreach($comments as $comment) { ?> 
<?php if($comment->isPublished()) { ?>
<div class="comment">
<a name="<?php echo $comment->getHtml("uid") ?>"></a>
<div class="commentname">
<a href="<?php echo ($comment->getHtml("url")=="")?$yellow->page->getLocation():$comment->getHtml("url")?>"><?php echo $comment->getHtml("name") ?></a>:</div>
<div class="commentcontent"><?php echo preg_replace("/\n/", "<br/>", htmlspecialchars($comment->comment)) // TODO: Maybe use Markdown here ?></div>
<div class="commentdate"><?php echo $this->yellow->text->normaliseDate($comment->get("created")) ?></div>
</div>
<?php } ?>
<?php } ?>
</div>

<!-- comments end -->

<?php if($yellow->page->get("status") != "done"): ?>

<div class="content seperate"></div>

<p><?php echo $yellow->page->getHtml("commentsStatus") ?><p>

<form class="contact-form" action="<?php echo htmlspecialchars($yellow->page->getLocation()) ?>" method="post">
<p class="contact-name"><label for="name"><?php echo $yellow->text->getHtml("contactName") ?></label><br /><input type="text" class="form-control<?php echo $commentHandler->required("name", " commentrequired") ?>" name="name" id="name" value="<?php echo htmlspecialchars($_REQUEST["name"]) ?>" /></p>
<p class="contact-from"><label for="from"><?php echo $yellow->text->getHtml("contactEmail") ?></label><br /><input type="text" class="form-control<?php echo $commentHandler->required("from", " commentrequired") ?>" name="from" id="from" value="<?php echo htmlspecialchars($_REQUEST["from"]) ?>" /></p>
<p class="contact-url"><label for="url"><?php echo $yellow->text->getHtml("contactUrl") ?></label><br /><input type="text" class="form-control<?php echo $commentHandler->required("url", " commentrequired") ?>" name="url" id="url" value="<?php echo htmlspecialchars($_REQUEST["url"]) ?>" /></p>
<p class="contact-comment"><label for="comment"><?php echo $yellow->text->getHtml("contactMessage") ?></label><br /><textarea class="form-control<?php echo $commentHandler->required("name", " required") ?>" name="comment" id="comment" rows="7" cols="70"><?php echo htmlspecialchars($_REQUEST["comment"]) ?></textarea></p>
<input type="hidden" name="beitrag" value="<?php echo $yellow->page->get('pageFile')?>" />
<input type="hidden" name="status" value="send" />
<input type="submit" value="<?php echo $yellow->text->getHtml("contactButton") ?>" class="btn contact-btn" />
</form>
<p class="comments_info">
<?php echo ($this->yellow->config->get("commentsAutoPublish")!="1")?$this->yellow->text->get("commentsManual"):"" ?>
</p>
<?php else: ?>
<p><?php echo $yellow->page->getHtml("commentsStatus") ?><p>
<?php endif ?>
</div>
