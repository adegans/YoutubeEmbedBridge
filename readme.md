# Youtube Embed Bridge for RSS Bridge

An alternative way to watch Youtube in your RSS reader through [RSS Bridge](https://github.com/RSS-Bridge/rss-bridge).
Watch Youtube videos from your favorite channels in your favorite RSS reader without ads.

## Installation
You'll need a working setup of [RSS Bridge](https://github.com/RSS-Bridge/rss-bridge).

Once RSS Bridge works you'll add the Youtube Embed Bridge to it.
Download the YoutubeEmbedBridge.php and place it in your RSS Bridge `/bridges/` folder.

Depending on your configuration you may need to enable the bridge in your config.ini.php (Around line 27) like so:
`enabled_bridges[] = YoutubeEmbed`

Or whitelist the bridge in your whitelist.txt by adding `YoutubeEmbed` at the end on a new line.

### Configuration
Required settings need to be added to your config.ini.php file, all the way at the bottom is fine.  
Set the settings to the values you want. If you do **NOT** wish to use the embed page, set the `embed_use_embed_page` value to false, the other settings are then ignored.

```
[YoutubeEmbedBridge]
; Set to true to be able to embed YT videos on a webpage you host yourself. Set to false to disable this and watch YT videos on the YT website.
embed_use_embed_page = true

; Where is your embed page hosted?
; You can host the embedpage/ folder anywhere you want, just put the correct url below. 
embed_page = "https://www.example.com/bridge/embedpage/embed.php"

; In embed page, how wide should the player be in percents (1-100)? Generally 70 works fine for most viewports (browser windows).
; Set it lower if the video is too high for your screen, or higher if a larger video fits your screen.
embed_player_width = "70"
```

## Usage
Simply load your RSS Bridge page and look for the Youtube Embed Bridge.
You can follow channels one by one, similar to how you would have subscriptions on youtube.com.

Enter the Channel Handle (The name with the @ in front of it), or the Channel ID (The ugly long code seen in some urls) and click Generate Feed.

### Finding the Channel Handle.
This looks a bit like a Instagram or Telegram name and has a **@** in front of it.
You can find it on most Channel main pages below the header image.

[![Find a channel handle](https://ajdg.solutions/assets/github-repo-assets/youtubeembed-channel-screenshot.webp)](https://ajdg.solutions/assets/github-repo-assets/youtubeembed-channel-screenshot.webp)

If it's not there you can find it in the channel details along with the Channel ID for most channels.
