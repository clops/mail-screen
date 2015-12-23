<?php
    require_once 'init.inc.php';

    require_once "lib/Imap.php";
    $mailbox = config::get()->imap_host;
    $username = config::get()->imap_user;
    $password = config::get()->imap_password;
    $encryption = config::get()->imap_encryption; // or ssl or ''

    // open connection
    $imap = new Imap($mailbox, $username, $password, $encryption);

    // stop on error
    if($imap->isConnected()===false)
        die($imap->getError());

    // get all folders as array of strings
    $folders = $imap->getFolders();
    foreach($folders as $folder)
        echo $folder;