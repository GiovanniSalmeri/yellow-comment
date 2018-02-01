# YellowComments

```
"Just an idea of a comment-system for yellow. 
 Feel free to transfrom it in something good!" 

 Nasendackel/2016
```

This plugin uses stuff from the [yellow](https://github.com/datenstrom/yellow) flat-file cms system.

## Installation

1. [Download and install Yellow](https://github.com/datenstrom/yellow/).
2. Delete `content-blogpages.php` and `content-blog.php` in your `system/themes/snippets` directory.
3. [Download plugin](https://github.com/nasendackel/yellow-comments/archive/master.zip) to the `system/plugins` folder.
4. Make sure your `content` folder is writable. (It probably already is.)
5. Add `commentsEmail` to your settings file `system/config/config.ini`.
6. Check and tweak the settings until everything is as you need.  :)

## Configuration/Settings

The plugin is using the settings system of Yellow with `system/config/config.ini`. This is where you can change or add settings. 

A short overview over the settings so far:

* `commentsEmail`

  This setting has to be set at the moment. All comments entered are mailed to this address.

* `commentsDir` (default: `` (empty))

  The location where your comment files are stored, if empty the files are stored within the content directory by using the `commentsExtension` setting.

* `commentsExtension` (default: `-comments`)  

  If your comments are stored within the content files, it's needed to distinguish between normal content and comments. If a comment is stored the base file path is needed and extended with the extension given. For example `test.txt` would become `test-comments.txt`.

* `commentsTemplate` (default: `system/config/comments-template.txt`)  

  When a new comment file is created, one could set a default content for the head of the file. Maybe we can display it later. At the moment it's used to hide the comments from the page visitor and for better webinterface integration.

* `commentsSeparator` (default: `----`)

  When having multiple comments, the separator is used to split the comment file into separate comments.

* `commentsAutoAppend` (default: `0`)

  If this flag is set to `1`, entered comments are automatically added to the comment file. No need to do it by yourself.

* `commentsAutoPublish` (default: `0`)

  If a comment is added automatically, you may wish the comment is published immediately. If you set this to `1` you have to remove new comments instead of adding them.

* `commentsMaxSize` (default: `10000`)

  In case someone tries to overflow your webspace you can limit the comment files to the needed maximum size.

* `commentsSpamFilter` (default: `href=|url=`)

  The message/comment input field is checked against this filter, to ensure no unwanted content is within this message.

* `commentsTimeout` (default: `7`)

  If a comment isn't published it will be deleted several days after its creation.

* `commentsIconBackgroundColor` (default: `ffffff`)

  [RGB hex value](http://www.colorspire.com/rgb-color-wheel/) of the avatar/icons background in front of the comments.

* `commentsIconForegroundColors` (default: `ff0000,cf0000,00ff00,00cf00,0000ff,0000cf,ffcf000,cfff00,00ffcf,00cfff,cf00ff,ff00cf`)

  [RGB hex value](http://www.colorspire.com/rgb-color-wheel/) of the avatar/icons foreground in front of the comments. The images are individual to every mail/username combination.

* `commentsIconSize` (default: `2`)

  Scaling factor of such an image. Usally not needed, but if artefacts are displayed you should increase this value. NOTE: This works only if `commentsIconGravatar` is `0`.

* `commentsIconGravatar` (default: `0`)

  Use [Gravatar](https://en.gravatar.com/) images instead of the internal image creator.

* `commentsIconGravatarOptions` (default: `s=80&d=mm&r=g`)

  Set the [Gravatar](https://en.gravatar.com/) image options. Please consult the service website.

* `commentsBlacklist` (default: `system/config/comments-blacklist.ini`)

  A file which contains all mail addresses to blacklist. Comments with these addresses are not shown in the output any longer.



