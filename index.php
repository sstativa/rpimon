<html>
    <head>
        <title>Raspberry Pi</title>
    </head>
    <body>

<?php

require_once("rrd_graph.php");

$ENV_TEMP_FILE = "/var/www/rpimon/env.rrd";
$HDD_TEMP_FILE = "/var/www/rpimon/hdd.rrd";

if (isset($_GET["id"])){
    echo "<a href=\"index.php\"><img src=" . call_user_func($_GET["id"], 3600 * 2) . " /></a><br/>\n";
    echo "<a href=\"index.php\"><img src=" . call_user_func($_GET["id"], 3600 * 24)  . " /></a><br/>\n";
    echo "<a href=\"index.php\"><img src=" . call_user_func($_GET["id"], 3600 * 24 * 5)  . " /></a><br/>\n";        
} else {
    echo "<a href=\"index.php?id=cpu_graph\"><img src=" . cpu_graph(3600 * 2) . " /></a><br/>\n";
    echo "<a href=\"index.php?id=mem_graph\"><img src=" . mem_graph(3600 * 2) . " /></a><br/>\n";            
    echo "<a href=\"index.php?id=cpu_temp_graph\"><img src=" . cpu_temp_graph(3600 * 2) . " /></a><br/>\n";
    if (file_exists($ENV_TEMP_FILE)) {
        echo "<a href=\"index.php?id=env_temp_graph\"><img src=" . env_temp_graph(3600 * 2) . " /></a><br/>\n";
    }
    if (file_exists($HDD_TEMP_FILE)) {
        echo "<a href=\"index.php?id=hdd_temp_graph\"><img src=" . hdd_temp_graph(3600 * 2) . " /></a><br/>\n";
    }
}

?>

    </body>
</html>

