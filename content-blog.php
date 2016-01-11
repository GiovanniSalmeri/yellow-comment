<?php echo $yellow->page->getExtra("commentsSend") ?>
<div class="content main">
<?php $yellow->page->set("entryClass", "entry") ?>
<?php if($yellow->page->isExisting("tag")): ?>
<?php foreach(preg_split("/,\s*/", $yellow->page->get("tag")) as $tag) { $yellow->page->set("entryClass", $yellow->page->get("entryClass")." ".$yellow->toolbox->normaliseArgs($tag, false)); } ?>
<?php endif ?>
<div class="<?php echo $yellow->page->getHtml("entryClass") ?>">
<div class="entry-header">
<h1 class="header_title"><span><?php echo $yellow->page->getHtml("titleContent") ?></span></h1>
<h1 class="komm_title"><a href="#comments">
<?php echo $yellow->page->getExtra("commentsCount") ?>
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

<?php echo $yellow->page->getExtra("comments") ?>

<!-- comments end -->

<?php if($yellow->page->get("status") != "done"): ?>

<div class="content seperate"></div>

<p><?php echo $yellow->page->getHtml("contactStatus") ?><p>

<form class="contact-form" action="<?php echo htmlspecialchars($yellow->page->getLocation()) ?>" method="post">
<p class="contact-name"><label for="name"><?php echo $yellow->text->getHtml("contactName") ?></label><br /><input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($_REQUEST["name"]) ?>" /></p>
<p class="contact-from"><label for="from"><?php echo $yellow->text->getHtml("contactEmail") ?></label><br /><input type="text" class="form-control" name="from" id="from" value="<?php echo htmlspecialchars($_REQUEST["from"]) ?>" /></p>
<p class="contact-from"><label for="url">Webseite</label><br /><input type="text" class="form-control" name="url" id="url" value="<?php echo htmlspecialchars($_REQUEST["url"]) ?>" /></p>
<p class="contact-message"><label for="message"><?php echo $yellow->text->getHtml("contactMessage") ?></label><br /><textarea class="form-control" name="message" id="message" rows="7" cols="70"><?php echo htmlspecialchars($_REQUEST["message"]) ?></textarea></p>
<input type="hidden" name="beitrag" value="<?php echo $yellow->page->get('pageFile')?>" />
<input type="hidden" name="status" value="send" />
<input type="submit" value="<?php echo $yellow->text->getHtml("contactButton") ?>" class="btn contact-btn" />
</form>
<p class="comments_info">
Kommentare werden von Hand moderiert. Die Veröffentlichung kann schon mal einen Tag dauern.
</p>
<?php else: ?>
<p><?php echo $yellow->page->getHtml("contactStatus") ?><p>
<?php endif ?>
</div>
