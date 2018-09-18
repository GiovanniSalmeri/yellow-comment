# Changelog 0.7.6

## General

+ works with Yellow 0.7.6
+ no more modified snippets for blog and blogpages
+ automatic insertion in blog pages and manual insertion with [comment] shortcode
+ update and micro-optimisation of code

## Settings

+ commentsEmail renamed commentsModerator
+ removed commentsExtension (file mode suppressed)
+ removed commentsTemplate (filename programmatically defined)
+ removed commentsSeparator (superfluous with new parsing)
+ removed commentsAutoAppend (manual appending disallowed)
+ added commentsAuthorNotification 
+ commentsTimeout accepts 0 for no timeout
+ added commentsOpening (number of days, 0 for indefinite, -1 for maintenance mode)
+ removed commentsUrlHighlight (now markdown styling)
+ removed commentsIconBackgroundColor and commentsIconForegroundColors (now hardcoded)
+ commentsIconSize is now in pixels for both types of icons
+ commentsIconGravatarDefault instead of commentsIconGravatarOptions
+ removed commentsBlacklist and blacklist (ineffective, should use DNSBL)

## Metadata

+ moderator setting in page, ovverriding commentsModerator

## Form

+ added checkbox for GDPR conformity
+ markdown styling (PHPMarkdown or Parsedown) in comment area
+ removed website field in form
+ added help messages
+ error messagges and checking uniformed to contact plugin

## Styling

+ synchronous filling in of gravatar and name from gravatar profile
+ auto-growing and -shrinking textarea 
+ css style self-adapting to icon size
+ css highlighting of focussed fields
+ css red colouring of error messages

## Other Safety Features

+ added honeypot captcha
+ IP and timestamp saved in comments file

## Bugs fixed

+ fixed date and hour localisation
+ fixed checking of comment size
+ fixed potential mangling of text in comment
