Onyx
=======
This CMS/eCommerce is unique in strict separation of PHP, HTML, CSS and Javascript code, which makes it easy to customise for any type of web project. State of the art on page editing interface using maximum of flexible layout system will allow you to design in browser.

Multisite design allows to run multiple website and share one Onyx installation (onyx_dir).

Typical web project files (project_skeleton)
--------------------------------------------
* conf/
* controllers/
* models/
* onyx_dir -> /opt/onyx/1.7
* public_html/
* templates/
* var/

Download & Install Onyx
==========================

Via Debian APT repository

### 1. Install Onyx archive public key
`$ wget -O - https://onxshop.com/debian/conf/signing_key.pub | apt-key add -`

### 2. Create APT source record
`$ echo "deb http://onxshop.com/debian/ jessie main" > /etc/apt/sources.list.d/onyx.list`

### 3. Install Onyx
`$ apt-get update && apt-get install onyx-1.7`

### 4. Create a website
`$ sudo onyx-1.7 create test.local.onxshop.com`

More Information
================

To install without using Debian package follow docs/INSTALL procedure. At this time Onyx is only optimised for Debian GNU/Linux operating system. You can [find Debian consultant](http://www.debian.org/consultants) near you and ask him to install Onyx for you.

For more information visit [Onxshop.com](http://onxshop.com/).
