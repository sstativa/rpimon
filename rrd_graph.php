<?php

function cpu_graph($period) {
    $rrd_file = "/var/www/rpimon/rpi.rrd";
    $img_file = "img/cpu_{$period}.png";
    
    if (!file_exists($img_file) || filemtime($rrd_file) > filemtime($img_file)) {
        $opts = array(
            "--start", "end-{$period}s", "--end", -time() % 60, 
            "DEF:sys={$rrd_file}:sys:AVERAGE",
            "DEF:nice={$rrd_file}:nice:AVERAGE",
            "DEF:user={$rrd_file}:user:AVERAGE",
            "AREA:sys#6855DD:sys:STACK",
            "AREA:nice#8477DD:nice:STACK",
            "AREA:user#A198DD:user:STACK",
            "--imgformat", "PNG", "--width", "480", "--height", "200",
            "--border=0",
            "--vertical-label", "%",
            "--title", "CPU"
            );
        if ($period > 7200) {
            $opts[] = "--slope-mode";
        }
        rrd_graph($img_file, $opts);
    }
    return $img_file;
}

function mem_graph($period) {
    $rrd_file = "/var/www/rpimon/rpi.rrd";
    $img_file = "img/mem_{$period}.png";

    if (!file_exists($img_file) || filemtime($rrd_file) > filemtime($img_file)) {
        $total = exec("cat /proc/meminfo | grep MemTotal | awk '{print $2}'");
        $upper_limit = round($total / 10000) * 10000;

        $opts = array(
            "--start", "end-{$period}s", "--end", -time() % 60,
            "DEF:free=".$rrd_file.":free:AVERAGE",
            "DEF:buffers=".$rrd_file.":buffers:AVERAGE",
            "DEF:cached=".$rrd_file.":cached:AVERAGE",
            "AREA:free#6855DD:free:STACK",
            "AREA:buffers#8477DD:buffers:STACK",
            "AREA:cached#A198DD:cached:STACK",
            "HRULE:{$total}#D34E5A:total",
            "--upper-limit", $upper_limit,
            "--lower-limit", "0",
            "--imgformat", "PNG", "--width", "480", "--height", "200",
            "--border=0",
            "--vertical-label", "Bytes",
            "--units-exponent", "0",
            "--title", "Free Memory"
            );
        if ($period > 7200) {
            $opts[] = "--slope-mode";
        }
        rrd_graph($img_file, $opts);
    }
    return $img_file;
}

function du_graph($period) {
    $rrd_file = "/var/www/rpimon/rpi.rrd";
    $img_file = "img/du_{$period}.png";

    if (!file_exists($img_file) || filemtime($rrd_file) > filemtime($img_file)) {
        $opts = array(
            "--start", "end-{$period}s", "--end", -time() % 60,
            "DEF:du=".$rrd_file.":du:AVERAGE",
            "LINE:du#6855DD:du",
            "GPRINT:du:LAST:Used space\: %2.1lf GB",
            "--imgformat", "PNG", "--width", "480", "--height", "200",
            "--border=0",
            "--vertical-label", "GB",
            "--title", "Used Space"
            );
        if ($period > 7200) {
            $opts[] = "--slope-mode";
        }
        rrd_graph($img_file, $opts);
    }
    return $img_file;
}

function cpu_temp_graph($period) {
    $rrd_file = "/var/www/rpimon/rpi.rrd";
    $img_file = "img/cpu_temp_{$period}.png";

    if (!file_exists($img_file) || filemtime($rrd_file) > filemtime($img_file)) {
        $opts = array(
            "--start", "end-{$period}s", "--end", -time() % 60,
            "DEF:temp=".$rrd_file.":temp:AVERAGE",
            "LINE:temp#6855DD:temp",
            "HRULE:75#D34E5A",
            "GPRINT:temp:LAST:Current temperature\: %2.1lf °C",
            "--lower-limit", "0",
            "--imgformat", "PNG", "--width", "480", "--height", "200",
            "--border=0",
            "--vertical-label", "°C",
            "--title", "CPU Temperature"
            );
        if ($period > 7200) {
            $opts[] = "--slope-mode";
        }
        rrd_graph($img_file, $opts);
    }
    return $img_file;
}

function env_temp_graph($period) {
    $rrd_file = "/var/www/rpimon/env.rrd";
    $img_file = "img/env_temp_{$period}.png";

    if (!file_exists($img_file) || filemtime($rrd_file) > filemtime($img_file)) { 
        $opts = array(
            "--start", "end-{$period}s", "--end", -time() % 60,
            "DEF:temp=".$rrd_file.":temp:AVERAGE",
            "LINE:temp#6855DD:temp",
            "GPRINT:temp:LAST:Current temperature\: %2.1lf °C",
            "--lower-limit", "0",
            "--imgformat", "PNG", "--width", "480", "--height", "200",
            "--border=0",
            "--vertical-label", "°C",
            "--title", "Environment Temperature"
            );
        if ($period > 7200) {
            $opts[] = "--slope-mode";
        }
        rrd_graph($img_file, $opts);
    }
    return $img_file;
}

function hdd_temp_graph($period) {
    $rrd_file = "/var/www/rpimon/hdd.rrd";
    $img_file = "img/hdd_temp_{$period}.png";

    if (!file_exists($img_file) || filemtime($rrd_file) > filemtime($img_file)) {
        $opts = array(
            "--start", "end-{$period}s", "--end", -time() % 60,
            "DEF:temp=".$rrd_file.":temp:AVERAGE",
            "LINE:temp#6855DD:temp",
            "HRULE:50#D34E5A",
            "GPRINT:temp:LAST:Current temperature\: %2.1lf °C",
            "--lower-limit", "0",
            "--imgformat", "PNG", "--width", "480", "--height", "200",
            "--border=0",
            "--vertical-label", "°C",
            "--title", "HDD Temperature"
            );
        if ($period > 7200) {
            $opts[] = "--slope-mode";
        }
        rrd_graph($img_file, $opts);
    }
    return $img_file;
}

?>
