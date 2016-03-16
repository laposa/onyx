#!/bin/bash -e
# Norbert @ Laposa Ltd, 2012-2015 
# Very simple Onxshop websites management script

####################
# input parameters #
####################

ACTION=$1 # mandatory
HOSTNAME=$2 # mandatory
DB_TEMPLATE_FILE=$3 # optional
PROJECT_SKELETON_DIR=$4 # optional
PROJECT_DIR=$5 # optional
#DB_USERNAME=$6 # optional
#DB_PASSWORD=$7 # optional

#####################
# prepare functions #
#####################

usage() {
cat <<EOF
$0 ACTION HOSTNAME [DB_TEMPLATE_FILE] [PROJECT_SKELETON_DIR] [PROJECT_DIR]

Creates Onxshop installation and vhost file depending on required hostname.

Example: You want your website to be served from http://example.com
Then run:
sudo onxshop create example.com

EOF
}

setup_variables() {
ONXSHOP_VERSION="1.7"
ONXSHOP_VERSION_DB=$(echo $ONXSHOP_VERSION | sed 's,\.,_,g')
if ! [ $DB_USERNAME ]; then
	determine_username_from_domainname
fi
if ! [ $PROJECT_DIR ]; then
	PROJECT_DIR="/srv/$HOSTNAME"
fi
if ! [ $DB_TEMPLATE_FILE ]; then
	DB_TEMPLATE_FILE=/opt/onxshop/${ONXSHOP_VERSION}/docs/database/template_en.sql
fi
if ! [ $PROJECT_SKELETON_DIR ]; then
	PROJECT_SKELETON_DIR=/opt/onxshop/$ONXSHOP_VERSION/project_skeleton/
fi

echo "
Variables are set to:

ACTION=$ACTION
HOSTNAME=$HOSTNAME
DB_TEMPLATE_FILE=$DB_TEMPLATE_FILE
PROJECT_SKELETON_DIR=$PROJECT_SKELETON_DIR
PROJECT_DIR=$PROJECT_DIR
DB_USERNAME=$DB_USERNAME
"

}

test_input() {
if ! [ $ACTION ]; then
	die "ACTION not provided"
fi

if ! [ $HOSTNAME ]; then
	die "HOSTNAME not provided"
fi
}

create_new_installation() {
	random_password
	copy_files
	setup_database
	change_config
#	create_vhost
	show_result
}

# Universal function for bailing out
die() { 
usage
echo -e "*** $1\n*** See https//onxshop.com/"; exit 1; 
}

random_password() {
# using pwgen if installed
# DB_PASSWORD="`pwgen -N 1`"

# Generate a random password without Perl
MATRIX="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
LENGTH="8"
while [ "${n:=1}" -le "$LENGTH" ]
do
    DB_PASSWORD="$DB_PASSWORD${MATRIX:$(($RANDOM%${#MATRIX})):1}"
	let n+=1
done
}

copy_files() {
if [ -d $PROJECT_DIR ]; then
	die "Directory $PROJECT_DIR already exists"
fi
cp -a $PROJECT_SKELETON_DIR $PROJECT_DIR
rm $PROJECT_DIR/onxshop_dir && ln -s /opt/onxshop/$ONXSHOP_VERSION/ $PROJECT_DIR/onxshop_dir
chmod a+w -R $PROJECT_DIR/var/
}

setup_database() {
DB_NAME="${DB_USERNAME}-${ONXSHOP_VERSION_DB}"
# TODO create user only if doesn't exist
sudo -u postgres psql template1 -c "CREATE USER $DB_USERNAME WITH CREATEDB NOCREATEUSER PASSWORD '$DB_PASSWORD'"
sudo -u postgres psql template1 -c "CREATE DATABASE \"$DB_NAME\" WITH OWNER=\"$DB_USERNAME\" ENCODING='UTF8'"
export PGPASSWORD=${DB_PASSWORD}
psql -U ${DB_USERNAME} -h localhost $DB_NAME < $DB_TEMPLATE_FILE 
psql -U ${DB_USERNAME} -h localhost $DB_NAME -c "UPDATE common_configuration SET value='$DB_USERNAME' WHERE property='title'";
}

change_config() {
DEPLOYMENT_FILE="$PROJECT_DIR/conf/deployment.php"
sed -i "s/define('ONXSHOP_DB_USER', '')/define('ONXSHOP_DB_USER', '$DB_USERNAME')/g" $DEPLOYMENT_FILE
sed -i "s/define('ONXSHOP_DB_PASSWORD', '')/define('ONXSHOP_DB_PASSWORD', '$DB_PASSWORD')/g" $DEPLOYMENT_FILE
sed -i "s/define('ONXSHOP_DB_NAME', '')/define('ONXSHOP_DB_NAME', '$DB_NAME')/g" $DEPLOYMENT_FILE
}

create_vhost() {
VHOST_FILE="/etc/apache2/sites-available/${HOSTNAME}.conf"
if [ -f ${VHOST_FILE} ]; then
    die "Vhost file ${VHOST_FILE} already exists"
fi
echo "<VirtualHost *:80>
	ServerName ${HOSTNAME}
	VirtualDocumentRoot ${PROJECT_DIR}/public_html
</VirtualHost>" > ${VHOST_FILE} || die "Couldn't add vhost file"
a2ensite ${HOSTNAME} && service apache2 reload
}

test_hostname_is_valid() {
if [ $HOSTNAME ] ; then
ping -c 1 `echo $HOSTNAME | sed 's/:[0-9]\+//'` || exit 1
else
   die "provide domain name"
   exit 0
fi
}

determine_username_from_domainname() {
DB_USERNAME=$(echo $HOSTNAME | sed 's,\.,,g;s,-,,g')
echo Constructed database name and user: $DB_USERNAME from $HOSTNAME
}

show_result() {
echo "Your Onxshop website is installed in: $PROJECT_DIR
To edit the website use
URL: https://${HOSTNAME}/edit
Username: ${DB_USERNAME}
Password: ${DB_PASSWORD}
"
}

process_action() {
case "$ACTION" in
  create)
    create_new_installation
    ;;
  backup)
    die "not implemented"
    ;;
  '')
    die "Sorry, no ACTION provided"
    ;;
  *)
    die "Sorry, unknown ACTION"
    ;;
esac
}

##########################
## start procedure here ##
##########################
test_input
setup_variables
#test_hostname_is_valid
process_action


