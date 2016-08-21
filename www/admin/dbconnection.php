<?php
/**
 * Created by: Wouter van den Brink<wouter@van-den-brink.net>
 * User: wouter
 * Date: 14-8-2016
 * Time: 13:45
 */
if ($dbsettings = parse_ini_file(__DIR__ ."/dbsettings.ini")) {
    
    $mysqli = new mysqli($dbsettings['dbhost'], $dbsettings['dbuser'], $dbsettings['dbpasswd'], $dbsettings['dbname']);
    $mysqli->query("SET NAMES 'utf8'");
    if (!$mysqli->set_charset('utf8')) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
        exit();
    }

}else{
    exit("unable to parse settings");
}
