Onxshop
=======
This CMS/eCommerce is unique in strict separation of PHP, HTML, CSS and Javascript code, which makes it easy to customise for any type of web project. State of the art on page editing interface using maximum of flexible layout system will allow you to design in browser.

Multisite design allows to run multiple website and share one Onxshop installation (onxshop_dir).

Typical web project files (project_skeleton)
--------------------------------------------
* conf/
* controllers/
* models/
* onxshop_dir -> /opt/onxshop/1.7
* public_html/
* templates/
* var/

Download & Install Onxshop
==========================

Via Debian APT repository

### 1. Install Onxshop archive public key
`$ wget -O - https://onxshop.com/debian/conf/signing_key.pub | apt-key add -`

### 2. Create APT source record
`$ echo "deb http://onxshop.com/debian/ jessie main" > /etc/apt/sources.list.d/onxshop.list`

### 3. Install Onxshop
`$ aptitude update && aptitude install onxshop`

### 4. Create a website
`$ sudo onxshop create yoursite.com`

More Information
================

To install without using Debian package follow docs/INSTALL procedure. At this time Onxshop is only optimised for Debian GNU/Linux operating system. You can [find Debian consultant](http://www.debian.org/consultants) near you and ask him to install Onxshop for you.

For more information visit [Onxshop.com](http://onxshop.com/).
