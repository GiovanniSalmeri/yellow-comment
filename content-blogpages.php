<div class="content main">

<?php if($yellow->page->isExisting("titleBlog")): ?>
<h1 class><?php echo $yellow->page->getHtml("titleBlog") ?></h1>
<?php endif ?>
<?php foreach($yellow->page->getPages() as $page): ?>
<?php $page->set("entryClass", "entry") ?>
<?php if($page->isExisting("tag")): ?>
<?php foreach(preg_split("/,\s*/", $page->get("tag")) as $tag) { $page->set("entryClass", $page->get("entryClass")." ".$yellow->toolbox->normaliseArgs($tag, false)); } ?>
<?php endif ?>
<div class="<?php echo $page->getHtml("entryClass") ?>">
<div class="entry-header">
	<h1 class="header_title"><a href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("title") ?></a></h1>
	<h1 class="komm_title"><a href="<?php echo $page->getLocation() ?>#kommentare">
	<?php $comment_f = "comments/" . $page->get("pageFile");
 	if(file_exists($comment_f)){
		$comment_t = file_get_contents($comment_f);
		$comment_a = explode("\n", $comment_t);
		$comment_z = $comment_a[0];
		unset($comment_a[0]);
	} else {
		$comment_z = 0;
	}
	echo $comment_z;
	?>
	</a></h1>
</div>

<div class="entry-content"><?php echo $yellow->toolbox->createTextDescription($page->getContent(), 1024, false, "<!--more-->", " <a href=\"".$page->getLocation()."\">".$yellow->text->getHtml("blogMore")."</a>") ?></div>
</div>
<div class="content seperate">
</div>
<?php endforeach ?>
<?php $yellow->snippet("pagination", $yellow->page->getPages()) ?>
</div>
