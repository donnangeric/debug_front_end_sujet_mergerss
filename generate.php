<?php
	//~ Ajax only
	if(!array_key_exists('isAjax', $_POST) || $_POST['isAjax'] !== "true"){
		exit('No ajax');
	}

	//~ Check token
	session_start();
	if(empty($_SESSION['csrf_token'])){
	    $_SESSION['csrf_token'] = uniqid(mt_rand(), true);
	}

	if(!array_key_exists('t', $_POST) || !empty($_POST['t'])){
		if ($_POST['t'] !== $_SESSION['csrf_token']) {
	        //~ Wrong token
	        $_d['sucess'] 	= false;
			$_d['message'] 	= "Problème lors de la vérification du token";
		    header('Content-type: text/json');
			echo json_encode($_d);
			exit;
	    }
	}else {
		//~ No token
	    $_d['sucess'] 	= false;
		$_d['message'] 	= "Problème de token";
	    header('Content-type: text/json');
		echo json_encode($_d);
		exit;
	}

	//~ Include Merge RSS!
	require_once 'vendor/RSS-Fusion/index.php';
  	
  	//~ Array for return
  	$_d = array(
  		'sucess' 	=>	false,
  		'message'	=>	"",
  		'data'		=>	array()
  	);

  	//~ Check data
  	if(array_key_exists('flux', $_POST) && !empty($_POST['flux'])){
		//~ Check if feeds are valid
		$oFeeds = new FeedReader($_POST['flux']);

  		if(!is_null($oFeeds->objParseFeed)){
			//~ Valid !

			//~ Set <link> RSS
			$rss_link = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) || $_SERVER['SSL'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
			if(array_key_exists('link', $_POST) && !empty($_POST['link'])){
				$rss_link = htmlspecialchars($_POST['link'], ENT_QUOTES, 	Config::get('characterSet'));
			}

			//~ Set <title> RSS
			$rss_title = "";
			if(array_key_exists('titre', $_POST) && !empty($_POST['titre'])){
				$rss_title = htmlspecialchars($_POST['titre'], ENT_QUOTES, 	Config::get('characterSet'));
			}

			//~ Set <description> RSS
			$rss_description = "";
			if(array_key_exists('desc', $_POST) && !empty($_POST['desc'])){
				$rss_description = htmlspecialchars($_POST['desc'], ENT_QUOTES, 	Config::get('characterSet'));
			}

			//~ Get param for all feeds

			$_links = array();
					
			foreach($_POST['flux'] as $index => $link){
				if(!empty($link)){
					array_push(
						$_links,
						array(
							'flux'		=>	$link,
							'filter'	=>	$_POST['filter'][$index],
							'where'		=>	$_POST['where'][$index],
							'words'		=>	$_POST['words'][$index]
						)
					);
				}
			}

			//~ Config is ok ! Storage
			$conf_storage = json_encode(array(
				'flux'       	=>	$_links,
				'link'		 	=> 	$rss_link,
				'title'		 	=>	$rss_title,
				'description'	=> 	$rss_description
  			));

			$conf_name = str_replace('.', '', uniqid(mt_rand(), true));
			$path = TL_ROOT . '/../../c/';
			
			//~ Is folder writable ?
			if(is_writable(dirname($path.$conf_name))){
				if(file_put_contents($path.$conf_name, $conf_storage)){
					//~ Generate link to return
					$_d['sucess'] 	= true;
					$_d['message'] 	= "Voici le lien RSS relatif à votre configuration";
					$_d['data']		= array(
						'file'	=>	'./flux.php?c='.$conf_name,
						'_'		=>	$_POST
					);

					//~ Delete old file conf  
					if ($handle = opendir($path)){  
						//~ Loop through the directory  
						while (false !== ($file = readdir($handle))){  
							//~ Check the file we're doing is actually a file  
							if (is_file($path.$file) && $file !== 'index.php' && $file !== '.gitignore'){  
								//~ Check if the file is older than 30 days old  
								if (filemtime($path.$file) < (time() - (30 * 24 * 60 * 60))){  //~ 3 days
									//~ Do the deletion  
									unlink($path.$file);  
								}  
							}  
						}  
					}
				}else{
					//~ Error
					$_d['sucess'] 	= false;
					$_d['message'] 	= "Une erreur est survenue lors de l'enregistrement de votre configuration";
				}
			}else{
				//~ Error
				$_d['sucess'] 	= false;
				$_d['message'] 	= "Le répertoire ciblé pour l'enregistrement de la configuration n'est pas accessible en écriture";
			}
		}else{
			$_d['sucess'] 	= false;
			$_d['message'] 	= "Une erreur est survenue lors du chargement de flux RSS";
		}
	}else{
		//~ No data
		$_d['sucess'] 	= false;
		$_d['message'] 	= "Merci de renseigner au moins une URL de flux RSS";
	}

	header('Content-type: text/json');
	echo json_encode($_d);
	exit;
