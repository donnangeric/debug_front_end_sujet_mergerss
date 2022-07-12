<?php


/**
 * Loads the local configuration file
 */
class Config
{

	/**
	 * Object instance (Singleton)
	 * @var \Config
	 */
	protected static $objInstance;

	/**
	 * Local file existance
	 * @var boolean
	 */
	protected static $blnHasLcf;

	/**
	 * Top content
	 * @var string
	 */
	protected $strTop = '<?php';

	/**
	 * Bottom content
	 * @var string
	 */
	protected $strBottom = '';

	/**
	 * Data
	 * @var array
	 */
	public $arrData = array();

	/**
	 * Default Data
	 * @var array
	 */
	public $arrDefaultData = array();


	/**
	 * Prevent direct instantiation (Singleton)
	 */
	protected function __construct() {}


	/**
	 * Prevent cloning of the object (Singleton)
	 */
	final public function __clone() {}


	/**
	 * Return the current object instance (Singleton)
	 *
	 * @return \Config The object instance
	 */
	public static function getInstance()
	{
		if (static::$objInstance === null)
		{
			static::$objInstance = new static();
			static::$objInstance->initialize();
		}

		return static::$objInstance;
	}


	/**
	 * Load all configuration files
	 */
	protected function initialize()
	{
		if (static::$blnHasLcf === null)
		{
			static::preload();
		}

		// Include the local configuration file again
		if (static::$blnHasLcf)
		{
			include TL_ROOT.'/system/config/localconfig.php';
		}
	}


	/**
	 * Return a configuration value
	 *
	 * @param string $strKey The short key (e.g. "displayErrors")
	 *
	 * @return mixed|null The configuration value
	 */
	public static function get($strKey)
	{
		if (isset($GLOBALS['TL_CONFIG'][$strKey]))
		{
			return $GLOBALS['TL_CONFIG'][$strKey];
		}

		return null;
	}


	/**
	 * Temporarily set a configuration value
	 *
	 * @param string $strKey   The short key (e.g. "displayErrors")
	 * @param string $varValue The configuration value
	 */
	public static function set($strKey, $varValue)
	{
		$GLOBALS['TL_CONFIG'][$strKey] = $varValue;
	}


	/**
	 * Preload the default and local configuration
	 */
	public static function preload()
	{
		// Load the default files
		include TL_ROOT.'/system/config/default.php';
		include TL_ROOT.'/system/config/agents.php';

		// Include the local configuration file
		if (($blnHasLcf = file_exists(TL_ROOT.'/system/config/localconfig.php')) === true)
		{
			include TL_ROOT.'/system/config/localconfig.php';
		}

		static::$blnHasLcf = $blnHasLcf;
	}


	/**
	 * Read file and set array
	 */
	public function read()
	{

		// Parse the local configuration file
		if (static::$blnHasLcf)
		{
			
			$resFile = fopen(TL_ROOT.'/system/config/localconfig.php', 'rb');

			while (!feof($resFile))
			{
				$strLine = fgets($resFile);
				$strTrim = trim($strLine);

				if ($strTrim == '?>' OR $strTrim == $this->strTop)
				{
					continue;
				}

				if ($strTrim != '')
				{
					$arrChunks = array_map('trim', explode('=', $strLine, 2));
					$this->arrData[$arrChunks[0]] = $arrChunks[1];
				}
			}

			fclose($resFile);

		}
	}


	/**
	 * Save the local configuration file
	 */
	public function save()
	{
		if ($this->strTop == '')
		{
			$this->strTop = '<?php';
		}

		$strFile  = trim($this->strTop) . "\n\n";

		foreach ($this->arrData as $k=>$v)
		{
			$strFile .= "$k = $v\n";
		}

		$this->strBottom = trim($this->strBottom);

		if ($this->strBottom != '')
		{
			$strFile .= "\n" . $this->strBottom . "\n";
		}

		$strTemp = md5(uniqid(mt_rand(), true));

		// Write to a temp file first
		$objFile = fopen(TL_ROOT.'/system/cache/' . $strTemp, 'wb');
		fputs($objFile, $strFile);
		fclose($objFile);

		// Make sure the file has been written
		if (!filesize(TL_ROOT.'/system/cache/' . $strTemp))
		{
			return;
		}

		// Then move the file to its final destination
		rename(TL_ROOT.'/system/cache/' . $strTemp, TL_ROOT.'/system/config/localconfig.php');

		// Reset the Zend OPcache
		if (function_exists('opcache_invalidate'))
		{
			opcache_invalidate(TL_ROOT.'/system/config/localconfig.php', true);
		}

		// Reset the Zend Optimizer+ cache (unfortunately no API to delete just a single file)
		if (function_exists('accelerator_reset'))
		{
			accelerator_reset();
		}

		// Recompile the APC file
		if (function_exists('apc_compile_file') && !ini_get('apc.stat'))
		{
			apc_compile_file(TL_ROOT.'/system/config/localconfig.php');
		}

		// Purge the eAccelerator cache
		if (function_exists('eaccelerator_purge') && !ini_get('eaccelerator.check_mtime'))
		{
			@eaccelerator_purge();
		}

		// Purge the XCache cache (thanks to Trenker)
		if (function_exists('xcache_count') && !ini_get('xcache.stat'))
		{
			if (($count = xcache_count(XC_TYPE_PHP)) > 0)
			{
				for ($id=0; $id<$count; $id++)
				{
					xcache_clear_cache(XC_TYPE_PHP, $id);
				}
			}
		}

	}


	/**
	 * Add a configuration variable to the local configuration file
	 *
	 * @param string $strKey   The full variable name
	 * @param mixed  $varValue The configuration value
	 */
	public function add($strKey, $varValue)
	{
		$this->read();
		$this->arrData[$strKey] = $this->escape($varValue) . ';';
	}
	

	/**
	 * Remove a configuration variable
	 *
	 * @param string $strKey The full variable name
	 */
	public function delete($strKey)
	{
		$this->read();
		unset($this->arrData[$strKey]);
	}

	/**
	 * Permanently set a configuration value
	 *
	 * @param string $strKey   The short key or full variable name
	 * @param mixed  $varValue The configuration value
	 */
	public static function persist($strKey, $varValue)
	{
		$objConfig = static::getInstance();

		if (strncmp($strKey, '$GLOBALS', 8) !== 0)
		{
			$strKey = "\$GLOBALS['TL_CONFIG']['$strKey']";
		}

		$objConfig->add($strKey, $varValue);
	}

	/**
	 * Escape a value depending on its type
	 *
	 * @param mixed $varValue The value
	 *
	 * @return mixed The escaped value
	 */
	protected function escape($varValue)
	{
		if (is_numeric($varValue) && !preg_match('/e|^00+/', $varValue) && $varValue < PHP_INT_MAX)
		{
			return $varValue;
		}

		if (is_bool($varValue))
		{
			return $varValue ? 'true' : 'false';
		}

		if ($varValue == 'true')
		{
			return 'true';
		}

		if ($varValue == 'false')
		{
			return 'false';
		}

		return "'" . str_replace('\\"', '"', preg_replace('/[\n\r\t ]+/', ' ', addslashes($varValue))) . "'";
	}

	
}
