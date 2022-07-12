<?php
  
  //~ Define the root path 
  define('TL_ROOT', dirname(__DIR__.'/RSS-Fusion'));

  require TL_ROOT.'/system/classes/Config.php';
  require TL_ROOT.'/system/classes/Date.php';
  require TL_ROOT.'/system/classes/FeedReader.php';
  require TL_ROOT.'/system/vendor/simplepie/SimplePie.php';

  Config::preload();
  
  //~ Adjust the error handling
  ini_set('display_errors', (Config::get('displayErrors') ? 1 : 0));
  error_reporting((Config::get('displayErrors') || Config::get('logErrors')) ? 1 : 0);

  
    /*
    ** For example
     */
    
  /*  $_links = array(
      "http://www.lemonde.fr/rss/une.xml", 
      "https://rss.framasoft.org/"
    );

    $feeds = new FeedReader($_links);

    
    echo "<pre>";
    var_dump($feeds->objParseFeed->items)*/
  
  
?>
