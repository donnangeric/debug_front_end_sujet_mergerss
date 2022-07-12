<?php


##############################################################
#                                                            #
#  DO NOT CHANGE ANYTHING HERE! USE THE LOCAL CONFIGURATION  #
#  FILE localconfig.php TO MODIFY THE CONFIGURATION!  		 #
#                                                            #
##############################################################


/**
 * -------------------------------------------------------------------------
 * GENERAL SETTINGS
 * -------------------------------------------------------------------------
 */

$GLOBALS['TL_CONFIG']['characterSet']   = 'utf-8';
$GLOBALS['TL_CONFIG']['displayErrors']  = false;
$GLOBALS['TL_CONFIG']['logErrors']      = true;



/**
 * -------------------------------------------------------------------------
 * DATE AND TIME SETTINGS
 * -------------------------------------------------------------------------
 *
 *   datimFormat = show date and time
 *   dateFormat  = show date only
 *   timeFormat  = show time only
 *   timeZone    = the server's default time zone
 *
 * See PHP function date() for more information.
 */
$GLOBALS['TL_CONFIG']['datimFormat']      = 'd-m-Y H:i';
$GLOBALS['TL_CONFIG']['dateFormat']       = 'd-m-Y';
$GLOBALS['TL_CONFIG']['timeFormat']       = 'H:i';
$GLOBALS['TL_CONFIG']['timeZone']         = (!is_null(ini_get('date.timezone')) ? ini_get('date.timezone') : 'GMT+1');
$GLOBALS['TL_CONFIG']['DAYS']             = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
$GLOBALS['TL_CONFIG']['DAYS_SHORT']       = array('Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam');
$GLOBALS['TL_CONFIG']['MONTHS']           = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
$GLOBALS['TL_CONFIG']['MONTHS_SHORT']     = array('Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc');


/**
 * -------------------------------------------------------------------------
 * RSS SETTINGS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_CONFIG']['maxResultsPerBlock']   	= 50;
$GLOBALS['TL_CONFIG']['enableCache']   			= true;
$GLOBALS['TL_CONFIG']['cacheDuration']   		= 3600;	//~ 1h
$GLOBALS['TL_CONFIG']['RssReaderReferer']   	= "RSS-Fusion with SimplePie";


/**
 * -------------------------------------------------------------------------
 * WORDS AND SEARCH SETTINGS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_CONFIG']['_starWords'] = array();
$GLOBALS['TL_CONFIG']['_badWords'] 	= array();
$GLOBALS['TL_CONFIG']['where'] 		= 'all';		//~ (title, description, link, all)

//*
//	Search in title only
//	$GLOBALS['TL_CONFIG']['where'] 		= 'title';
//*

//*
//	Search in description only
//	$GLOBALS['TL_CONFIG']['where'] 		= ' description';
//*

//*
//	Search in url only
//	$GLOBALS['TL_CONFIG']['where'] 		= 'link';
//*

//*
//	Search in multiple content, separate with "|"
//	$GLOBALS['TL_CONFIG']['where'] 		= 'title|description';
//*

//*
//	Search in all content
//	$GLOBALS['TL_CONFIG']['where'] 		= 'all';
//*