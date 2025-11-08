<?php
/**
* RssBridgeEmbedYoutube
* Get YouTube videos as embeds in RSS feed items for easy viewing using the Channel Handle or Channel ID
* Make sure config.ini.php is up-to-date. See readme.md (on Github: https://github.com/adegans/YoutubeEmbedBridge) for details.
*/

class YoutubeEmbedBridge extends BridgeAbstract {
	const NAME = 'YouTube Embed Bridge';
	const MAINTAINER = 'Arnan de Gans';
	const URI = 'https://www.youtube.com';
	const CACHE_TIMEOUT = 60 * 60 * 3;
	const DESCRIPTION = 'Get the newest videos from a YouTube channel as a RSS feed.';

	const PARAMETERS = [
		'Channel handle' => [
			'h' => [
				'name' => 'Channel Handle',
				'exampleValue' => '@arnandegans',
				'required' => true
			]
		],
		'Channel id' => [
			'c' => [
				'name' => 'Channel ID',
				'exampleValue' => 'UC-gNtK3RMsVTOxvoJxVUTMw',
				'required' => true
			]
		],
	];

    const CONFIGURATION = [
        'embed_use_embed_page' => [
            'required' => true,
        ],
        'embed_page' => [
            'required' => true,
        ],
        'embed_player_width' => [
            'required' => true,
        ]
    ];

	private $feedName = '';
	private $feeduri = '';
	private $feedIconUrl = '';

	public function collectData() {
		$cacheKey = 'youtube_rate_limit';
		if($this->cache->get($cacheKey)) {
			throw new HttpException('429 Too Many Requests', 429);
		}
		try {
			$this->collectDataInternal();
		} catch (HttpException $e) {
			if($e->getCode() === 429) {
				$this->cache->set($cacheKey, true, 60 * 16);
			}
			throw $e;
		}
	}

	private function collectDataInternal() {
		$html = '';
		$url_listing = '';
		$url_feed = '';

		$channel_handle = $this->getInput('h');
		$channel_id = $this->getInput('c');

		if($channel_handle) {
			$url_listing = self::URI . '/' . urlencode(trim($channel_handle)) . '/videos';

			// Find the feed url for the channel handle
			$html = $this->fetch($url_listing);
			if(!preg_match('/var ytInitialData = (.*?);<\/script>/i', $html, $matches)) {
				$this->logger->debug('Could not find ytInitialData');
	
				$jsondata = null;
			} else { 
				$jsondata = json_decode($matches[1]);
			}

			if(is_null($jsondata)) returnClientError("Jsondata can not be null.");

            $url_feed = $jsondata->metadata->channelMetadataRenderer->rssUrl;
			$this->feediconurl = $jsondata->metadata->channelMetadataRenderer->avatar->thumbnails[0]->url;

			$this->feeduri = $url_listing;

			// Fetch xml feed
			$html = $this->fetch($url_feed);
			$this->extractItemsFromXmlFeed($html);

			$this->feedName = $html->find('title', 0)->plaintext;
		} elseif($channel_id) {
			$url_listing = self::URI . '/channel/' . urlencode(trim($channel_id)) . '/videos';
			$url_feed = self::URI . '/feeds/videos.xml?channel_id=' . urlencode(trim($channel_id));

			$this->feeduri = $url_listing;

			// Fetch xml feed
			$html = $this->fetch($url_feed);
			$this->extractItemsFromXmlFeed($html);

			$this->feedName = $html->find('title', 0)->plaintext;
		} else {
			returnClientError("You must either specify either a; YouTube Channel ID (UC-gNtK3RMsVTOxvoJxVUTMw) found in the Channels about or a Channel Handle (@arnandegans) visible in the channel URL.");
		}
	}

	private function extractItemsFromXmlFeed($xml) {
		$this->feedName = html_entity_decode($xml->find('feed > title', 0)->plaintext, ENT_QUOTES, 'UTF-8');

		foreach($xml->find('entry') as $element) {
			$videoid = str_replace('yt:video:', '', $element->find('id', 0)->plaintext);

			if(strpos($videoid, 'googleads') !== false) continue;

			$title = html_entity_decode($element->find('title', 0)->plaintext);
			$author = $element->find('name', 0)->plaintext;
			$description = $element->find('media:description', 0)->innertext;
			$description = htmlspecialchars($description);
			// Regex came from repo BetterVideoRss of VerifiedJoseph.
			$description = preg_replace('/(https?:\/\/(?:www\.)?(?:[a-zA-Z0-9-.]{2,256}\.[a-z]{2,20})(\:[0-9]{2,4})?(?:\/[a-zA-Z0-9@:%_\+.,~#"\'!?&\/\/=\-*]+|\/)?)/ims', '<a href="$1" target="_blank">$1</a> ', $description);
			$timestamp = strtotime($element->find('published', 0)->plaintext);

			$this->addItem($videoid, $title, $author, $description, $timestamp);
		}
	}

	private function fetch($url, bool $cache = false) {
		$header = ['Accept-Language: en-US'];

		if($cache) {
			return getSimpleHTMLDOMCached($url, 172800, $header, [], true, true, DEFAULT_TARGET_CHARSET, false);
		}

		return getSimpleHTMLDOM($url, $header, [], true, true, DEFAULT_TARGET_CHARSET, false);
	}

	private function addItem($videoid, $title, $author, $description, $timestamp) {
		// Format description into paragraphs, if there is a description
		if(strlen($description) > 0) {
			// Clean up/prepare the description
			$description = nl2br($description);
			$description = $description . "\n";
			$description = preg_replace('|<br\s*/?>\s*<br\s*/?>|', "\n\n", $description);
			$description = str_replace(array("\r\n", "\r"), "\n", $description);
			$description = preg_replace("/\n\n+/", "\n\n", $description);
	
			// Split into initial paragraphs
			$paragraphs = preg_split('/\n\s*\n/', $description, -1, PREG_SPLIT_NO_EMPTY);
		
			$description = '';
		
			// Create proper paragraphs
			foreach($paragraphs as $paragraph) {
				$description .= '<p>' . trim($paragraph, "\n") . "</p>\n";
			}
		
			// Remove empty paragraphs, should they exist
			$description = preg_replace('|<p>\s*</p>|', '', $description);
		}

		$item = [];
		$item['id'] = $videoid;
		$item['title'] = $title;
		$item['author'] = $author;
		$item['timestamp'] = $timestamp;
		$item['uri'] = self::URI . '/watch?v=' . $videoid;

		// Do a thumbnail
        $thumbnail = str_replace('/www.', '/img.', self::URI) . '/vi/' . $item['id'] . '/0.jpg';

		// Embed url
		if($this->getOption('embed_use_embed_page')) {
			if(empty($this->getOption('embed_player_width')) || $this->getOption('embed_player_width') < 50 || $this->getOption('embed_player_width') > 95) {
				$embed_width = "75";
			} else {
				$embed_width = $this->getOption('embed_player_width');
			}
			
	        $embed_url = $this->getOption('embed_page').'?vid='.urlencode($item['id']).'&vw='.urlencode($embed_width).'&vt='.urlencode(htmlspecialchars($item['title'], ENT_QUOTES));
	        $embed_links = '<p>Video links: <a href="'.$embed_url.'">Watch embedded in browser</a> or <a href="'.$item['uri'].'">watch on YouTube</a>.</p>';
	    } else {
		    $embed_url = $item['uri'];
		    $embed_links = '<p>Video links: <a href="'.$item['uri'].'">watch on YouTube</a>.</p>';
		}

		$item['content'] = '';

/*
		// Standard Embed, older version
		$item['content'] .= "<p><iframe 
			style=\"width:100%;\" 
			width=\"100%\" 
			src=\"https://www.youtube.com/embed/".$item['id']."\" 
			title=\"".$item['title']." - YouTube\" 
			frameborder=\"0\" 
			allow=\"encrypted-media; web-share\" 
			referrerpolicy=\"strict-origin-when-cross-origin\" 
			allowfullscreen>
		</iframe></p>\n".
*/

/*
		// Standard Embed, modified with 16:9 ratio
		$item['content'] .= "<div style=\"position:relative; padding-bottom:56.25%; padding-top:25px; height:0;\">
		<iframe 
			style=\"position:absolute; top:0; left:0; width:100%; height:100%;\" 
			width=\"100%\" 
			src=\"https://www.youtube-nocookie.com/embed/".$item['id']."\" 
			title=\"YouTube video player\" 
			frameborder=\"0\" 
			allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" 
			referrerpolicy=\"strict-origin-when-cross-origin\" 
			allowfullscreen
		></iframe></div>\n";
*/

        $item['content'] .= '<p><a href="'.$embed_url.'"><img src="'.$thumbnail.'" /></a></p>';
		$item['content'] .= '<p>Video links: <a href="'.$embed_url.'">Watch embedded in browser</a> or <a href="'.$item['uri'].'">watch on YouTube</a>.</p>';
		$item['content'] .= $description;
		
		$this->items[] = $item;
		
		unset($embed_url, $embed_links, $embed_width);
	}

	public function getURI() {
		if($this->feeduri) {
			return $this->feeduri;
		}

		return parent::getURI();
	}

	public function getName() {
		switch($this->queriedContext) {
			case 'Channel id':
			case 'Channel handle':
				return htmlspecialchars_decode($this->feedName);
			default:
				return parent::getName();
		}
	}

	public function getIcon() {
		if(empty($this->feedIconUrl)) {
			return parent::getIcon();
		} else {
			return $this->feedIconUrl;
		}
	}
}
