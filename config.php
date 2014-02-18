#!/usr/bin/php
<?php

Class Config {
    protected $iniPath;

    public function __construct($iniPath) {
        $this->iniPath = $iniPath;
        // check if we've already loaded this file 
        $alreadyLoaded = apc_fetch("$iniPath-loaded");
        if ($alreadyLoaded === false) {
            // if not then load it into APC
            $this->loadAPC();
        }
    }

    public function get($key) {
        $value = apc_fetch($key, $success);
        if (!$success) {
            // value not found, value may have dropped out 
            // of memory, try recovering
            $value = $this->recover($key);
        }
        return $value;
    }

    protected function loadAPC() {
        // load and parse the ini file
        $config = parse_ini_string(
            file_get_contents($this->iniPath, true)
        );
        // store each key/value into APC verbatim
        foreach($config as $key => $value) {
            apc_store($key, $value);
        }
        // set a flag to say we've now loaded the file
        apc_store($this->iniPath . "-loaded", true);
    }

    protected function recover($key) {
        $this->loadAPC();
        $value = apc_fetch($key, $success);
        if (!$success) {
            // if we get here one of two things has happened:
            // 1 - APC is pooched
            // 2 - someone is trying to use a key that doesnt 
            //     exist in the ini file
            // Either way we shouldnt carry on.
            throw new Exception("Failed to request '$key' from APC config cache after ".
                                "recovery. Is the key defined in your INI file?");
        }
        return $value;
    }
}

$cfg = new Config("/etc/config.ini");
echo $cfg->get("database.username");
