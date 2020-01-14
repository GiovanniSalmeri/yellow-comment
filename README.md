# Comments 0.8.9

Simple commenting system.

<p align="center"><img src="comments-screenshot.png?raw=true" alt="Screenshot"></p>

## Installation

1. [Download and install Datenstrom Yellow](https://github.com/datenstrom/yellow/).
2. [Download extension](../../archive/master.zip). If you are using Safari, right click and select 'Download file as'.
4. Copy `comments.zip` into your `system/extensions` folder.

To uninstall delete the [extension files](extension.ini).

## How to use Comments

The extension adds automatically a comments section on blog pages.\*

To add comments on other pages (that is, non-blog pages) create a [comments] shortcut at the end of each page in which you want a comments section. In this case the following optional argument is available:

`opening` = number of days from publication after which comments are closed; this argument, if present, overrides the `commentsOpening` setting (in other words: this value is used and `commentsOpening` is ignored). See below for details and for the meaning of the special values `0` and `-1`.

To add comments on every page of the site, add `<?php echo $this->yellow->page->getExtra("comments") ?>` in  `system/layouts/default.html`, after the line `<?php echo $this->yellow->page->getContent() ?>`.

If you want the comments section *not* to appear on a specific page, add `Comments: no` to the Settings at the top of it.

\* Please remark that in the sample pages of the Blog extension comments are normally closed because of the publication date of the pages!

Once installed, this Comments extension is particularly easy to use:

+ easy for the administrator (zero configuration: all default values are sensible)
+ easy for users (one click for sending a comment, without registration)
+ easy for the moderator (one click in the received email for approving comments)

In spite of that the extension is hard for spammers (spam filter, moderation, honeypot captcha, automatic closing of comments).

## How to configure Comments

The following settings can be configured in file `system/settings/system.ini`. You can leave alone the default values and all will work sensibly.

`commentsModerator` (default: (empty)) = email address of moderator. If not present, main `email` address of site is used; this value can be overridden with a setting `Moderator` in the page.  
`commentsDir` (default:  `comments/`) = the location where your comment files are stored  
`commentsAutoPublish` (default:  `0`) = if set to `1` any comment is published immediately and the moderator can later remove it; if set to `0` no comment is published unless the moderator approves it (except particular cases, this latter behaviour is much more desirable)  
`commentsMaxSize` (default:  `10000`) = maximum size of a comment  
`commentsTimeout` (default:  `0`) = number of days after which a comment is permanently deleted if not approved for publication; if set to `0` comments are never automatically deleted  
`commentsOpening` (default:  `30`) = number of days from publication after which comments are closed; if set to `0` comments are never closed, if set to `-1` all comments are closed regardless of publication date (can be used as a maintenance mode while manually editing the comments file); only `0` and `-1` are meaningful if the metadata of the page does not contain the `published` setting; this value can be overridden with an optional argument when using the shortcut `[comments]`  
`commentsAuthorNotification` (default:  `1`) = if set to `1`, authors are notified with an email of the publication of their comments (useful also as a check on the authenticity of the email entered)  
`commentsSpamFilter` (default:  `href=|url=`) = spam filter as regular expression  
`commentsIconSize` (default:  `80`) = size in pixel of the icon  
`commentsIconGravatar` (default:  `0`) = use [Gravatar](https://en.gravatar.com/) images instead of the internal image creator; when set to `1` also the Name field is syncronously filled in, if available in the Gravatar profile  
`commentsIconGravatarDefault` (default:  `mp`) = default image for Gravatar (see the [documentation](https://en.gravatar.com/site/implement/images/) for possible values); without effect if `commentsIconGravatar` is `0`  

## Example

Embedding comments in a non-blog page:

```
[comments]
[comments 0]
```

## Updating from a previous version

If you were using a version of this extension previous to 0.7.6, you can update the comments to the new format with a simple script:

```
<?php
$extension = "-comments";
$separator = "----";
$dir = "comments/";
$trash = "trash/";

@mkdir($dir);
@mkdir($trash);
$files = glob("*.*");
$count = 0;
foreach ($files as $file) {
    if (preg_match("/^(.*)" . $extension . "(\.\w+)$/", $file, $parts)) {
        $text = str_replace("\r\n", "\n", file_get_contents($file));
        $text = preg_replace("/^". $separator . "$/m", "", $text);
        $new = fopen($dir . $parts[1] . $parts[2], "w") or die("Cannot create new file\n");
        fwrite($new, $text) or die("Cannot write in new file\n");
        fclose($new);
        rename($file, $trash . $file)  or die("Cannot trash old file\n");
        $count++;
    }
}
echo "$count files updated\n";
```

Put it in the blog directory with the name `update.php`, change the first four variables if necessary, and execute with `php update.php`. If all has gone right, delete the script and trash directory. If you cannot easily execute the script remotely, you can for example download all blog files, execute locally the script and then upload again.

## Developers

Previous maintainers nasendackel, wunderfeyd; current maintainer Giovanni Salmeri
