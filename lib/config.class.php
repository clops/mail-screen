<?php

	class config {

		private static $instance = null;
		private $configVars = array();


		private function __construct() {
		} //disable generation without getInstance


		private function __clone() {
		} //disable cloning


		/**
		 * singelton for config class
		 *
		 * @return Config
		 */
		public static function get() {
			if (is_null(self::$instance)) {
				self::$instance = new Config();
				self::$instance->init();
			}

			return self::$instance;
		}


		/**
		 * Destroy Singleton
		 */
		public static function destroyInstance() {
			self::$instance = null;
		}


		public final function init( $configFile = 'config' ) {
			$this->reset()->loadFromFile($configFile);
		}


		public final function loadFromFile( $configFile ) {
			$iniGlobal   = $iniLocal = $iniDefault = array();
			$prefix      = dirname(__FILE__) . '/../config/';
			$defaultFile = $prefix . $configFile . '_default.ini';
			$globalFile  = $prefix . $configFile . '.ini';
			$localFile   = $prefix . $configFile . '_local.ini';

			//parse default file (silent ignore!)
			if (file_exists($defaultFile) && is_readable($defaultFile)) {
				$iniDefault = parse_ini_file($defaultFile, false);
			}

			//parse file or throw Exception
			if (file_exists($globalFile) && is_readable($globalFile)) {
				$iniGlobal = parse_ini_file($globalFile, false);
			} else {
				throw new Exception('Global INI File "' . $globalFile . '" not found.');
			}

			//parse local file (silent ignore!)
			if (file_exists($localFile) && is_readable($localFile)) {
				$iniLocal = parse_ini_file($localFile, false);
			}

			return $this->evalIniData(array_merge($iniDefault, $iniGlobal, $iniLocal));
		}


		public function setConfigVar( $var, $val ) {
			return $this->evalIniData(array($var => $val));
		}

		public function isTestSystem() {
			return (bool)$this->testsystem;
		}

		private function evalIniData( $ini ) {
			foreach ($ini AS $key => $value) {
				if (!is_array($value)) {
					if (substr($value, 0, 5) == 'json:') {
						$value = json_decode(substr(str_replace('_Q_', '"', $value), 5), true);
					} else {
						$current = $this;
						$value   = preg_replace_callback(
							'!\{([^\}]+)\}!',
							function ( $matches ) use ( $current ) {
								return $current->__get($matches[1]);
							},
							$value
						);
					}
				}
				$this->configVars[ $key ] = $value;
			}

			return $this;
		}


		public final function registerGlobals() {
			foreach ($this->configVars AS $key => $value) {
				$GLOBALS['_CONFIG'][ $key ] = $value;
			}

			return true;
			//return $this->registerConstants(); //register constants as well!
		}


		public final function registerConstants() {
			foreach ($this->configVars AS $key => $value) {
				if (preg_match('/^[A-Z_0-9]+$/', $key)) {
					if (!defined($key)) {
						define($key, $value, false);
					} else {
						trigger_error('Constant "' . $key . '" already set.', E_USER_NOTICE);
					}
				}
			}

			return $this;
		}


		public function reset() {
			$this->configVars = array();

			return $this;
		}


		public final function __get( $name ) {
			if (!isset($this->configVars[ $name ])) {
				return null;
			}

			return $this->configVars[ $name ];
		}


		public final function __isset( $name ) {
			return isset($this->configVars[ $name ]);
		}
	}