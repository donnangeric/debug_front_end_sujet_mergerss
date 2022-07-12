<?php
	//~ Include Merge RSS!
	require_once 'vendor/RSS-Fusion/index.php';

	//~ Check if conf file exist
	if(!array_key_exists('c', $_GET) 
		OR !isset($_GET['c'])
			OR !file_exists(TL_ROOT . '/../../c/'.$_GET['c'])){
		exit;
	}

	$conf = TL_ROOT . '/../../c/'.$_GET['c'];

	//~ Update file date (file is deleted if is not updated every 30 days)
	touch($conf);

	//~ Open conf
	$oConf = json_decode(file_get_contents($conf));

	if($oConf && gettype($oConf) == 'object'){
		$_links = array();
		$_items = array();
		$_c     = array();

		//~ Load feeds
		foreach($oConf->flux as $link){
			//array_push($_links, $link['flux']);
		
			$oFeeds = new FeedReader(array($link->flux));
			if(!is_null($oFeeds->objParseFeed)){
				foreach($oFeeds->objParseFeed->items as $item){
					if(!empty($item['link']) && !in_array($item['link'], $_c)){
						//~ Prevent duplicate item by link
						array_push($_c, $item['link']);
						
						//~ Where
						$_cible = explode('|', $link->where);

						if(count($_cible) > 1){
							$merge_str = "";
							foreach ($_cible as $cible) {
								$merge_str .= " ".strtolower($item[$cible]);
							}
						}else{
							if($_cible[0] && $_cible[0] != 'all'){
								$merge_str = strtolower($item[$_cible[0]]);
							}else{
								$merge_str = strtolower($item['title']." ".$item['description']." ".$item['link']);
							}
						}
						

						$_words = explode(',', $link->words);
						if(count($_words) && $link->words != ""){
							$oIsIn = $oFeeds->isInStr($merge_str, $_words);
							
							if($link->filter == 'show' && $oIsIn){
								//~ add
								array_push($_items, $item);
							}elseif($link->filter == 'hide' && !$oIsIn){
								//~ add
								array_push($_items, $item);
							}	
						}else{
				
							//~ No "keywords", show all
							array_push($_items, $item);
						}
					}
				}
			}
		}
		
		//~ Sort by pubdate
		$pub = array();
		foreach ($_items as $key => $row)
		{
			$pub[$key] = $row['pubdate'];
		}
		array_multisort($pub, SORT_DESC, $_items);

		//~ Generate RSS
		$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
	   		$rssfeed .= '<rss version="2.0">';
	    		$rssfeed .= '<channel>';
	    			$rssfeed .= '<title>'.($oConf->title && !empty($oConf->title) ? $oConf->title : 'Flux personnalisé Merge RSS!').'</title>';
	    			$rssfeed .= '<link>'.($oConf->link && !empty($oConf->link) ? $oConf->link : 'https://framagit.org/Erase/RSS-Fusion-demo').'</link>';
					$rssfeed .= '<description>'.($oConf->description && !empty($oConf->description) ? $oConf->description : 'Flux généré avec Merge RSS!').'</description>';

	    if(count($_items)){
	    	foreach ($_items as $item){
	    		$rssfeed .= '<item>';
		    		$rssfeed .= '<title>'.html_entity_decode($item['title']).'</title>';
		    		$rssfeed .= '<link>'.$item['link'].'</link>';
		    		$rssfeed .= '<description><![CDATA['.$item['description'].']]></description>';
		    		$rssfeed .= '<pubDate>'.date('r', $item['pubdate']).'</pubDate>';
		    		$rssfeed .= '<category>'.$item['category'].'</category>';
		    		$rssfeed .= '<enclosure url="'.$item['enclosure'].'" />';

		    		$rssfeed .= '<permalink>'.$item['permalink'].'</permalink>';
	    		$rssfeed .= '</item>';
	    	}
	    }

	    		$rssfeed .= '</channel>';
    		$rssfeed .= '</rss>';


    	//~ Return RSS content
    	header('Content-type: application/xml');
		echo $rssfeed;
	}
	