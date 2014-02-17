#!/bin/bash

RPI_RRD="/var/www/rpimon/rpi.rrd"
ENV_RRD="/var/www/rpimon/env.rrd"
HDD_RRD="/var/www/rpimon/hdd.rrd"

# create $RPI_RRD file if it does not exist
if [ ! -f $RPI_RRD ]; then
  rrdtool create $RPI_RRD --step 60 \
    DS:user:COUNTER:120:0:100 \
    DS:nice:COUNTER:120:0:100 \
    DS:sys:COUNTER:120:0:100 \
    DS:free:GAUGE:120:0:U \
    DS:buffers:GAUGE:120:0:U \
    DS:cached:GAUGE:120:0:U \
    DS:du:GAUGE:120:0:U \
    DS:temp:GAUGE:120:0:U \
    RRA:AVERAGE:0.5:1:120 \
    RRA:AVERAGE:0.5:3:480 \
    RRA:AVERAGE:0.5:15:480
fi

# try to initialise DS18B20
modprobe w1-gpio
modprobe w1-therm
sleep 5

# check existense of DS18B20
if [ -e /sys/bus/w1/devices/28-*/w1_slave ]; then
    has_ds18b20="TRUE"
fi

# create $ENV_RRD file if DS18B20 is connected but file does not exist
if [ "$has_ds18b20" == "TRUE" ] && [ ! -f $ENV_RRD ]; then
  rrdtool create $ENV_RRD --step 60 \
    DS:temp:GAUGE:120:0:U \
    RRA:AVERAGE:0.5:1:120 \
    RRA:AVERAGE:0.5:3:480 \
    RRA:AVERAGE:0.5:15:480
fi

# check presence of smartctl
which smartctl >/dev/null 2>&1
if [ $? -eq 0 ]; then
    has_smartctl="TRUE" 
fi

# create $HDD_RRD file if it does not exist and smartctl is available
if [ "$has_smartctl" == "TRUE" ] && [ ! -f $HDD_RRD ]; then
  rrdtool create $HDD_RRD --step 60 \
    DS:temp:GAUGE:120:0:U \
    RRA:AVERAGE:0.5:1:120 \
    RRA:AVERAGE:0.5:3:480 \
    RRA:AVERAGE:0.5:15:480
fi

# inifinity loop 
while : ; do
    # pause till the beginning of next minute
    perl -e 'sleep 60 - time % 60'
    # CPU
    stat=(`head -n 1 /proc/stat`)
    # Memory
    mem=(`head -n 4 /proc/meminfo | awk '{print $2}'`)
    # Used space of rootfs
    du=`df -k | grep rootfs | awk '{print $3 / 1048576}'`
    # CPU temperature
    temp=`cat /sys/class/thermal/thermal_zone0/temp | awk '{print $1/1000}'`

    rrdupdate $RPI_RRD N:${stat[1]}:${stat[2]}:${stat[3]}:${mem[1]}:${mem[2]}:${mem[3]}:$du:$temp  
    
    # shutdown if the CPU temperature is too high
    if [ `echo $temp |cut -d "." -f1` -ge 75 ]; then
        logger "Shutting down due to SoC temp ${temp}."
        shutdown -h now
    fi

    if [ "$has_ds18b20" == "TRUE" ]; then
        # DS18B20 temperature
        temp=`cat /sys/bus/w1/devices/28-*/w1_slave | grep t= | awk -F "=" '{print $2/1000}'`
        rrdupdate $ENV_RRD N:$temp
    fi

    if [ "$has_smartctl" == "TRUE" ]; then
        # HDD temperature 
        temp=`smartctl -A /dev/sda | grep Temperature | awk '{print $10}'`
        rrdupdate $HDD_RRD N:$temp
        
        # shutdown if the HDD temperature is too high
        if [ `echo $temp | cut -d "." -f1` -ge 48 ]; then
            logger "Shutting down due to HDD temp ${temp}."
            shutdown -h now
        fi
    fi
done

