# Simple Raspberry Pi Monitor



## Prerequirements

* Raspbian
* Lighttpd + PHP
* rrdtool
* smartmontools (Optional)

### Installation of Lighttpd + PHP
~~~
sudo apt-get install -y lighttpd php5-cgi php5-cli
sudo lighttpd-enable-mod fastcgi-php
sudo /etc/init.d/lighttpd force-reload

sudo chown www-data:www-data /var/www
sudo chmod 775 /var/www
sudo usermod -a -G www-data pi  
~~~

### Installation of rrdtool
~~~
sudo apt-get install -y rrdtool
~~~

### Installation of smartmontools (Optional)
~~~
sudo apt-get install -y smartmontools
~~~



## Installation of rpimon
~~~
sudo -i
cd /var/www
git clone git://github.com/sstativa/rpimon.git
cd rpimon
./install.sh
/etc/init.d/rpimon start
~~~



## Usage
Open URL `http://<IP of your Raspberry Pi>/rpimon` in your web browser.
