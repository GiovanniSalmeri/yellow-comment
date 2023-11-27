# Comment 0.8.19

Simple commenting system.

<p align="SCREENSHOT.png?raw=true" alt="Screenshot"></p>

## How to install an extension

[Download ZIP file](https://github.com/GiovanniSalmeri/yellow-comment/archive/refs/heads/main.zip) and copy it into your `system/extensions` folder. [Learn more about extensions](https://github.com/annaesvensson/yellow-update).

## How to show comments

The extension adds a comment section on blog pages.

To add comments on other pages create a `[comment]` shortcut. The following optional argument is available:

`opening` = number of days from publication after which comments are closed, or `0` (never closed), or `-1` (always closed).

To put comments on every page of the site, add `<?php echo $this->yellow->page->getExtraHtml("comment") ?>` in  `system/layouts/default.html`, after the line `<?php echo $this->yellow->page->getContentHtml() ?>`.

If you don't want comments to be shown on a page, set `Comment: exclude` in the [page settings](https://github.com/annaesvensson/yellow-core#settings-page) at the top of a page.

## Examples

Content file with comments:

    ---
    Title: Example page
    ---
    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut 
    labore et dolore magna pizza. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
    nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit 
    esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt 
    in culpa qui officia deserunt mollit anim id est laborum.
    
    [comment]

Preventing comments being shown:

    ---
    Title: Example page
    Comment: exclude
    ---
    This page does not show comments.

Layout file with comments:

    <?php $this->yellow->layout("header") ?>
    <div class="content">
    <div class="main" role="main">
    <h1><?php echo $this->yellow->page->getHtml("titleContent") ?></h1>
    <?php echo $this->yellow->page->getContentHtml() ?>
    <?php echo $this->yellow->page->getExtraHtml("comment") ?>
    </div>
    </div>
    <?php $this->yellow->layout("footer") ?>

## Settings

The following settings can be configured in file `system/extensions/yellow-system.ini`:

`CommentModerator`) = email address of moderator. If not present, main `email` address of site is used; this value can be overridden with a setting `Moderator` in the page.  
`CommentDirectory` = directory for comments  
`CommentAutoPublish` = if set to `1` any comment is published immediately and the moderator can later remove it; if set to `0` no comment is published unless the moderator approves it (except particular cases, this latter behaviour is much more desirable)  
`CommentMaxSize` = maximum size of a comment  
`CommentTimeout` = number of days after which a comment is permanently deleted if not approved for publication; if set to `0` comments are never automatically deleted  
`CommentOpening` = number of days from publication after which comments are closed; if set to `0` comments are never closed, if set to `-1` all comments are closed regardless of publication date (can be used as a maintenance mode while manually editing the comments file); only `0` and `-1` are meaningful if the metadata of the page does not contain the `published` setting; this value can be overridden with an optional argument when using the shortcut `[comments]`  
`CommentAuthorNotification` = if set to `1`, authors are notified with an email of the publication of their comments (useful also as a check on the authenticity of the email entered)  
`CommentSpamFilter` = spam filter as regular expression  
`CommentIconSize` = size in pixel of the icon  
`CommentIconGravatar` = use [Gravatar](https://en.gravatar.com/) images instead of the internal image creator and fill in the name field, if available in the Gravatar profile  
`CommentIconGravatarDefault` = default image for Gravatar (see the [documentation](https://en.gravatar.com/site/implement/images/) for possible values)  
`CommentConsent` = shows a consent checkbox (not required by European GDPR)   

## Acknowledgements

This extension was previously maintained by Christoph Schröder and David Fehrmann. Thank you for the good work.

## Developer

Giovanni Salmeri. [Get help](https://datenstrom.se/yellow/help/).
