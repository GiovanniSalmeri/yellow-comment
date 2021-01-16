# Comments 0.8.16

Simple commenting system.

<p align="center"><img src="comments-screenshot.png?raw=true" alt="Screenshot"></p>

## How to use Comments

The extension adds automatically a comments section on blog pages.

To add comments on other pages (that is, non-blog pages) create a [comments] shortcut at the end of each page in which you want a comments section. In this case the following optional argument is available:

`opening` = number of days from publication after which comments are closed; this argument, if present, overrides the `commentsOpening` setting (in other words: this value is used and `commentsOpening` is ignored). See below for details and for the meaning of the special values `0` and `-1`.

To put comments on every page of the site, add `<?php echo $this->yellow->page->getExtra("comments") ?>` in  `system/layouts/default.html`, after the line `<?php echo $this->yellow->page->getContent() ?>`.

If you want the comments section *not* to appear on a specific page, add `Comments: no` to the Settings at the top of it.

## Example

Embedding comments in a non-blog page:

```
[comments]
[comments 0]
```

## Settings

The following settings can be configured in file `system/settings/system.ini`. You can leave alone the default values and all will work sensibly.

`commentsModerator` (default: (empty)) = email address of moderator. If not present, main `email` address of site is used; this value can be overridden with a setting `Moderator` in the page.  
`commentsDir` (default:  `comments/`) = the location where your comment files are stored  
`commentsAutoPublish` (default:  `0`) = if set to `1` any comment is published immediately and the moderator can later remove it; if set to `0` no comment is published unless the moderator approves it (except particular cases, this latter behaviour is much more desirable)  
`commentsMaxSize` (default:  `5000`) = maximum size of a comment  
`commentsTimeout` (default:  `0`) = number of days after which a comment is permanently deleted if not approved for publication; if set to `0` comments are never automatically deleted  
`commentsOpening` (default:  `30`) = number of days from publication after which comments are closed; if set to `0` comments are never closed, if set to `-1` all comments are closed regardless of publication date (can be used as a maintenance mode while manually editing the comments file); only `0` and `-1` are meaningful if the metadata of the page does not contain the `published` setting; this value can be overridden with an optional argument when using the shortcut `[comments]`  
`commentsAuthorNotification` (default:  `1`) = if set to `1`, authors are notified with an email of the publication of their comments (useful also as a check on the authenticity of the email entered)  
`commentsSpamFilter` (default:  `href=|url=`) = spam filter as regular expression  
`commentsIconSize` (default:  `80`) = size in pixel of the icon  
`commentsIconGravatar` (default:  `0`) = use [Gravatar](https://en.gravatar.com/) images instead of the internal image creator and fill in the name field, if available in the Gravatar profile  
`commentsIconGravatarDefault` (default:  `mp`) = default image for Gravatar (see the [documentation](https://en.gravatar.com/site/implement/images/) for possible values)  
`commentsConsent` (default:  `0`) = shows a consent checkbox (not required by European GDPR)   

## Installation

[Download extension](https://github.com/GiovanniSalmeri/yellow-comments/archive/master.zip) and copy zip file into your `system/extensions` folder. Right click if you use Safari.

## Developers

Giovanni Salmeri. [Get help](https://github.com/GiovanniSalmeri/yellow-comments/issues).

Previous developers nasendackel, wunderfeyd
