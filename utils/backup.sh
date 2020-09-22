#!/bin/bash -e
# Onyx backup using CMS backup facility
# Norbert Laposa, 2013-2015

# Configure
HOSTNAME="localhost"
USERNAME="username@localhost"
PASSWORD="***"
COOKIES_FILE=cookies.txt
BACKUP_URL="https://$HOSTNAME/backoffice/advanced/tools?tool=backup&scope=both"
TOUCH_URL="https://$HOSTNAME/backoffice/"
BACKUP_LOCAL_FILE=$HOSTNAME.tar.gz

# USING WGET
# First establish session and save cookies
#wget --keep-session-cookies --save-cookies $COOKIES_FILE -O /dev/null $TOUCH_URL 
# Now authenticate and download backup
#wget --keep-session-cookies --load-cookies $COOKIES_FILE --http-user=$USERNAME --http-password=$PASSWORD -O $BACKUP_LOCAL_FILE $BACKUP_URL

# USING CURL
# First establish session and save cookies
curl -s -L -c $COOKIES_FILE -b $COOKIES_FILE -o /dev/null $TOUCH_URL
# Now authenticate and download backup
curl -s -L -c $COOKIES_FILE -b $COOKIES_FILE --user $USERNAME:$PASSWORD -o $BACKUP_LOCAL_FILE $BACKUP_URL

