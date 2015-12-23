<?php
    #### DEFAULT SETTINGS ####
    #
    ini_set('display_errors', false);
    ini_set('register_globals', false);
    ini_set('magic_quotes_gpc', false);
    ini_set('zlib.output_compression', false);
    ini_set('output_buffering', false);
    ini_set('allow_call_time_pass_reference', false);
    ini_set('safe_mode', false);
    ini_set('short_open_tag', true);
    date_default_timezone_set('Europe/Vienna');
    #
    ##

    #### AUTOLOADERS ####
    #
    require_once 'lib/config.class.php';
    function screenAutoloader($className){
        if(file_exists(config::get()->root.'classes/'.$className.'.class.php')){
            require_once config::get()->root.'classes/'.$className.'.class.php';
        }
    }
    spl_autoload_register('screenAutoloader');
    #
    ##

    ## Step 1 - Init Config and prepare error reporting ##
    #
    if(config::get()->isTestSystem()){
        ini_set('display_errors', 1);   //always display errors
    }
    ##

    ## INIT Dynamic Config ##
    #
    config::get()->registerGlobals();
    #
    ##

    ## INIT System ##
    #
    setlocale(LC_ALL, "de_DE.utf8", config::get()->locale);
    #
    ##
