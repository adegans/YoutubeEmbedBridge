# Youtube Embed Bridge for RSS Bridge

An alternative way to watch Youtube in your RSS reader through [RSS Bridge](https://github.com/RSS-Bridge/rss-bridge){:target="_blank"}.
Watch Youtube videos from your favorite channels in your favorite RSS reader without ads.

## Installation
You'll need a working setup of [RSS Bridge](https://github.com/RSS-Bridge/rss-bridge){:target="_blank"}.

Once RSS Bridge works you can add the Youtube Embed Bridge to it.
Download the YoutubeEmbedBridge.php and place it in your RSS Bridge `/bridges/` folder.

Depending on your configuration you may need to enable the bridge in your config.ini.php (Around line 27) like so:
`enabled_bridges[] = YoutubeEmbed`

Or whitelist the bridge in your whitelist.txt by adding `YoutubeEmbed` at the end on a new line.

## Usage
Simply load your RSS Bridge page and look for the Youtube Embed Bridge.
You can follow channels similar to how you would have subscriptions.

Enter the Channel Handle (The name with the @ in front of it), or the Channel ID (The ugly long code seen in some urls) and click Generate Feed.

### Finding the Channel Handle.
This looks a bit like a Instagram or Telegram name and has a **@** in front of it.
You can find it on most Channel main pages below the header image.

[![Find a channel handle](https://ajdg.solutions/assets/github-repo-assets/youtubeembed-channel-screenshot.webp)](https://ajdg.solutions/assets/github-repo-assets/youtubeembed-channel-screenshot.webp)

If it's not there you can find it in the channel details along with the Channel ID for most channels.
