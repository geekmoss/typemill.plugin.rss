# typemill.plugin.rss

This plugin allows you to add RSS feeds to all your folders. 

Also a generic RSS feed with all posts from all folders is available just add `/rss` to your base url. The title and description of this generic RSS feed can be set in the plugin settings.

## Changes in this fork

Modified version of the original plugin, since my instance does not generate `navigation.txt` and therefore RSS is not generated at all or only links to the homepage.

My version takes the `structure.txt` file where the site structure is cached. But unfortunately it lacks, for example, a description (the page name is used).
Also RSS per folder is missing in this version.

The logic for generating RSS XML is in `rssBuilder.php`, which uses the PHP DOM.