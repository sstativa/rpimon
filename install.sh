#!/bin/bash

SCRIPT_FILE="/var/www/rpimon/collect.sh"
IMG_DIR="/var/www/rpimon/img"

if [ ! -e /etc/init.d/rpimon ];  then 

cat >/etc/init.d/rpimon << EOF
#!/bin/sh
#/etc/init.d/rpimon

### BEGIN INIT INFO
# Provides:          rpimon
# Required-Start:    $`echo remote_fs` $`echo syslog`
# Required-Stop:     $`echo remote_fs` $`echo syslog`
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: 
# Description:       
### END INIT INFO

case "$`echo 1`" in
  start)
    echo "Starting rpimon"
    $SCRIPT_FILE &
    ;;
  stop)
    echo "Stopping rpimon"
    killall $SCRIPT_FILE
    ;;
  *)
    echo "Usage: /etc/init.d/rpimon {start|stop}"
    exit 1
    ;;
esac

exit 0
EOF

fi

chmod 755 /etc/init.d/rpimon
sudo update-rc.d rpimon defaults

if [ ! -e $IMG_DIR ]; then
    mkdir $IMG_DIR
fi

chown www-data:www-data ./*.php $IMG_DIR
