<?php
	//~ Include Merge RSS!
	require_once 'vendor/RSS-Fusion/index.php';

	//~ Generate token
	session_start();
	if(empty($_SESSION['csrf_token'])) {
	    $_SESSION['csrf_token'] = uniqid(rand(), true);
	}
?>
<!DOCTYPE html>
<html lang="<?php echo \Config::get('language');?>" dir="ltr">
<head>
	<title>Merge RSS!</title>
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="chrome=1"><![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="format-detection" content="telephone=no"/>
	<meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width" />
	<meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?>">
	<link rel="dns-prefetch" href="http://fonts.googleapis.com/">
	<link rel="dns-prefetch" href="http://ajax.googleapis.com/"> 
	<link rel="dns-prefetch" href="http://html5shim.googlecode.com/">
	<link rel="shortcut icon" href="img/favicon.ico">
	<link rel="apple-touch-icon" href="img/apple-touch-icon-57x57-precomposed.png">
	<link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72-ipad.png">
	<link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114-retina.png">
	<link rel="apple-touch-icon" sizes="144x144" href="img/apple-touch-icon-144x144-retina.png">
	<link rel="stylesheet" type="text/css" href="vendor/goofi-bronco/goofi.php?family=Source+Sans+Pro:200,400,700,400italic">
	<link rel="stylesheet" type="text/css" href="styles/reset.css">
	<link rel="stylesheet" type="text/css" href="styles/styles.css">
	<link rel="stylesheet" type="text/css" href="styles/font-awesome.css">
	<!--[if IE]><link href="http://www.3818.com.ar/styles/fix-old-ie.css" media="all" type="text/css" rel="stylesheet">
<![endif]-->
	<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>

<body>
<!--[if IE]>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script>
    <style>.chromeFrameInstallDefaultStyle { width: 100%; border: 5px solid #ffa700; }</style><div id="prompt"></div>
    <script>window.attachEvent("onload", function() {CFInstall.check({mode: "overlay", node: "prompt"});});</script>
<![endif]-->

	<header>
		<div class="wrapper">
			<div class="content">
				<hgroup>
					<h1>Merge RSS!</h1>
					<h2 data-lng>Fusion et filtre de flux RSS</h2>
				</hgroup>		
				<div class="clear"></div>
			</div>
		</div>
	</header>

	<div class="sectionorange">
		<section id="xml">
			<div class="separator"> 
				<div class="line"></div>
				<h2 data-lng>Créer un flux personnalisé</h2>
				<div class="line"></div>
			</div>
			<div class="clear"></div>
			<div class="xml-form shadow" id="form">
				<form id="xml_form" method="POST" action="" accept-charset="UTF-8">
					<div style="background-color:#ddd;">
						<fieldset class="full-form">
							<div class="container-feeds">
								<div class="bloc mcw-bloc">
									<span data-lng>Pour le flux :</span>
									<input class="mcw mcw-text" type="text" name="flux[]" placeholder="URL">
									<br><br>
									<span data-lng class="small">je désire :</span>
									<select class="mcw mcw-select" name="filter[]">
										<option value="show" data-lng>afficher</option>
										<option value="hide" data-lng>masquer</option>
									</select>
									<span data-lng class="small">en effectuant ma recherche sur</span>
									<select class="mcw mcw-select" name="where[]">
										<option value="title" data-lng>le titre</option>
										<option value="description" data-lng>la description</option>
										<option value="link" data-lng>le lien</option>
										<option value="title|description" data-lng>le titre et la description</option>
										<option value="title|link" data-lng>le titre et le lien</option>
										<option value="link|description" data-lng>le lien et la description</option>
										<option value="all" data-lng>tous les éléments</option>
									</select>
									<br><br>
									<span data-lng>les items contenant le(s) mot(s) clés (séparer chaque mot clé par une virgule) :</span>
									<br><input class="mcw mcw-text" type="text" name="words[]" value="">
									<span class="mcw mcw-buttons buttons">
										<a class="mcw mcw-a add_field" href="#">Add</a>      
									</span>
									<div class="clear"></div>
									<div class="bottom-shadow"></div>
									<div class="no-shadow hidden"></div>
									<div class="clear"></div>   
								</div>
							</div>
						</fieldset>
					</div>
				
			
					<h3 data-lng>Options du flux généré</h3>
					<fieldset>
						<label for="titre" data-lng>Titre du flux général</label>
						<input data-lng="value" type="text" name="titre" value="Flux personnalisé Merge RSS!" id="titre">
					</fieldset>
					<fieldset>
						<label for="desc" data-lng>Description du flux général</label>
						<input data-lng="value" type="text" name="desc" value="Flux généré avec Merge RSS!" id="desc">
					</fieldset>
					<fieldset class="full-form">
						<label for="link" data-lng>Base URL du flux général</label>
						<input type="text" name="link" value="<?php echo ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) || $_SERVER['SSL'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);?>" id="link">
					</fieldset>
		            <div class="clear"></div>
		            <fieldset class="fsubmit full-form">
		            	<div id="ajax-message"></div>
		            	<div class="clear"></div>
						<input data-lng="value" id="submit" type="button" class="btn btn-success btn-submit" value="Générer !">
						<div class="clear"></div>
						<div id="rss-lk"></div>
					</fieldset>
				</form>
			</div>
		</section>
	</div>
	<div class="sectionblue">
		<footer id="footer">
			<div class="wrapper">
				<span class="small">version 0.9</span>
			</div>
		</footer>
	</div>
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/modernizr.custom.js" type="text/javascript"></script>
	<script src="js/jquery.scrollTo-1.4.3.1.js" type="text/javascript"></script>
	<?php if(\Config::get('displayErrors')):  ?>
		<script>
			var debugShow = true;
		</script>
	<?php endif;?>
	<script src="js/custom.js" type="text/javascript"></script>
	<script src="js/multicolumns.js" type="text/javascript"></script>
</body>
</html>