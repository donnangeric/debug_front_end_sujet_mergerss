<?php

/**
 * Read RSS feeds
 *
 * The class provides an interface to read RSS feeds.
 *
 * Usage:
 *
 *     $feed = new Feed('FEED_URL');
 *     echo $feed->objParseFeed->title
 *
 */
class FeedReader
{

	/**
	 * objParseFeed
	 * @var object
	 */
	public $objParseFeed;

	/**
	 * Items
	 * @var array
	 */
	public $arrItems = array();

	public $objFeed;

	/**
	 * Store the feed name
	 *
	 * @param array $_urlFeeds The feed url
	 */
	public function __construct($_urlFeeds)
	{
		$this->objFeed = new SimplePie();
		$this->objFeed->set_useragent(\Config::get('RssReaderReferer'));

		if (count($_urlFeeds) > 1)
		{
			$this->objFeed->set_feed_url($_urlFeeds);
		}
		else
		{
			$this->objFeed->set_feed_url($_urlFeeds[0]);
		}

		$this->objFeed->set_output_encoding(\Config::get('characterSet'));
		$this->objFeed->set_cache_location(TL_ROOT.'/system/cache/');
		$this->objFeed->enable_cache(\Config::get('enableCache'));
		$this->objFeed->set_cache_duration(\Config::get('cacheDuration'));
		$this->objFeed->enable_order_by_date();
		

		if (!$this->objFeed->init())
		{
			return '';
		}

		$this->objFeed->handle_content_type();

		$this->objParseFeed = new \stdClass();
		$this->objParseFeed->link = $this->objFeed->get_link();
		$this->objParseFeed->title = $this->objFeed->get_title();
		$this->objParseFeed->language = $this->objFeed->get_language();
		$this->objParseFeed->description = $this->objFeed->get_description();
		$this->objParseFeed->copyright = $this->objFeed->get_copyright();

		// Add image
		if ($this->objFeed->get_image_url())
		{
			$this->objParseFeed->image = true;
			$this->objParseFeed->src = $this->objFeed->get_image_url();
			$this->objParseFeed->alt = $this->objFeed->get_image_title();
			$this->objParseFeed->href = $this->objFeed->get_image_link();
			$this->objParseFeed->height = $this->objFeed->get_image_height();
			$this->objParseFeed->width = $this->objFeed->get_image_width();
		}

		// Get the items 
		$this->arrItems = array_slice($this->objFeed->get_items(0, $GLOBALS['TL_CONFIG']['maxResultsPerBlock']), 0, $GLOBALS['TL_CONFIG']['maxResultsPerBlock']);

		$limit = count($this->arrItems);
		$offset = 0;
		$items = array();
		$last = min($limit, count($this->arrItems)) - 1;

		for ($i=0; $i<$limit; $i++)
		{
			$items[$i] = array
			(
				'link'        => $this->arrItems[$i]->get_link(),
				'title'       => $this->arrItems[$i]->get_title(),
				'permalink'   => $this->arrItems[$i]->get_permalink(),
				'description' => str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $this->arrItems[$i]->get_description()),
				'pubdate'     => $this->arrItems[$i]->get_date('U'),
				'date_read'	  => \Date::parse(Config::get('datimFormat'), $this->arrItems[$i]->get_date('U')),
				'category'    => $this->arrItems[$i]->get_category(0),
				'base'        => $this->arrItems[$i]->get_base(),
				'show'		  => 'show' 	// show || hide || star
			);

			
			// Where
			$where = \Config::get('where');
			$_cible = explode('|', \Config::get('where'));

			if(count($_cible) > 1){
				$merge_str = "";
				foreach ($_cible as $cible) {
					$merge_str .= " ".strtolower($items[$i][$cible]);
				}
			}else{
				if($_cible[0] && $_cible[0] != 'all'){
					$merge_str = strtolower($items[$i][$_cible[0]]);
				}else{
					$merge_str = strtolower($items[$i]['title']." ".$items[$i]['description']." ".$items[$i]['link']);
				}
			}

			// Bad and star words
			if(count(Config::get('_starWords'))){
				if($this->isInStr($merge_str, Config::get('_starWords')))
				{
					$items[$i]['show'] = 'star';
				}
			}
			
			if(count(Config::get('_badWords'))){
				if($this->isInStr($merge_str, Config::get('_badWords')))
				{
					$items[$i]['show'] = 'hide';
				}
			}

			// Add author
			if (($objAuthor = $this->arrItems[$i]->get_author(0)) != false)
			{
				$items[$i]['author'] = trim($objAuthor->name . ' ' . $objAuthor->email);
			}

			// Add enclosure
			if (($objEnclosure = $this->arrItems[$i]->get_enclosure(0)) != false)
			{
				$items[$i]['enclosure'] = $objEnclosure->get_link();
			}
		}

		$this->objParseFeed->items = array_values($items);
	}

	
	public static function isInStr($strString, $varWords)
	{	
		foreach ((array) $varWords as $strWord)
		{	
			if(empty($strWord)){
				continue;
			}
			$t = preg_match("/".preg_quote(htmlentities($strWord), '/')."/i", $strString);
		    
			if($t){

				return true;
			}
		}
		return false;
	}
}
