#!/bin/bash -e
# Norbert @ Laposa Ltd, 2012/02/05 
# Very simple Onxshop websites management script

ACTION=$1
DOMAIN=$2

#####################
# prepare functions #
#####################

usage() {
cat <<EOF
$0 ACTION FQDN

Creates Onxshop installation depending on required fully
qualified domain name(FQDN).

Example: You want your website to be served from http://example.com
Then run:
sudo onxshop create example.com

EOF
}

setup_variables() {
ONXSHOP_VERSION="1.7"
ONXSHOP_VERSION_DB=$(echo $ONXSHOP_VERSION | sed 's,\.,_,g')
#USERNAME="test4"
HOME_DIRECTORY="/srv/$DOMAIN"
}

test_input() {
if ! [ $ACTION ]; then
	die "ACTION not provided"
fi

if ! [ $DOMAIN ]; then
	die "DOMAIN not provided"
fi
}

create_new_installation() {
	determine_username_from_domainname
	random_password
	copy_files
	setup_database
	change_config
	create_vhost
	show_result
}

# Universal function for bailing out
die() { 
usage
echo -e "*** $1\n*** See http://onxshop.com/"; exit 1; 
}

random_password() {
# Generate a random password without Perl
MATRIX="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
LENGTH="8"
while [ "${n:=1}" -le "$LENGTH" ]
do
    PASSWORD="$PASSWORD${MATRIX:$(($RANDOM%${#MATRIX})):1}"
	let n+=1
done
}

copy_files() {
if [ -d $HOME_DIRECTORY ]; then
	die "Directory $HOME_DIRECTORY already exists"
fi
cp -a /opt/onxshop/$ONXSHOP_VERSION/project_skeleton/ $HOME_DIRECTORY
rm $HOME_DIRECTORY/onxshop_dir && ln -s /opt/onxshop/$ONXSHOP_VERSION/ $HOME_DIRECTORY/onxshop_dir
chmod a+w -R $HOME_DIRECTORY/var/
}

setup_database() {
DB_NAME="${USERNAME}-${ONXSHOP_VERSION_DB}"
sudo -u postgres psql template1 -c "CREATE USER $USERNAME WITH CREATEDB NOCREATEUSER PASSWORD '$PASSWORD'"
sudo -u postgres psql template1 -c "CREATE DATABASE \"$DB_NAME\" WITH OWNER=\"$USERNAME\" ENCODING='UTF8'"
export PGPASSWORD=${PASSWORD}
psql -U ${USERNAME} -h localhost $DB_NAME < /opt/onxshop/$ONXSHOP_VERSION/docs/database/template_en.sql
psql -U ${USERNAME} -h localhost $DB_NAME -c "UPDATE common_configuration SET value='$USERNAME' WHERE property='title'";
}

change_config() {
DEPLOYMENT_FILE="$HOME_DIRECTORY/conf/deployment.php"
sed -i "s/define('ONXSHOP_DB_USER', '')/define('ONXSHOP_DB_USER', '$USERNAME')/g" $DEPLOYMENT_FILE
sed -i "s/define('ONXSHOP_DB_PASSWORD', '')/define('ONXSHOP_DB_PASSWORD', '$PASSWORD')/g" $DEPLOYMENT_FILE
sed -i "s/define('ONXSHOP_DB_NAME', '')/define('ONXSHOP_DB_NAME', '$DB_NAME')/g" $DEPLOYMENT_FILE
}

create_vhost() {
VHOST_FILE="/etc/apache2/sites-available/${DOMAIN}"
if [ -f ${VHOST_FILE} ]; then
    die "Vhost file ${VHOST_FILE} already exists"
fi
echo "<VirtualHost *:80>
	ServerName ${DOMAIN}
	VirtualDocumentRoot ${HOME_DIRECTORY}/public_html
</VirtualHost>" > ${VHOST_FILE} || die "Couldn't add vhost file"
a2ensite ${DOMAIN} && /etc/init.d/apache2 reload
}

test_domain_name_is_valid() {
if [ $DOMAIN ] ; then
ping -c 1 `echo $DOMAIN | sed 's/:[0-9]\+//'` || exit 1
else
   die "provide domain name"
   exit 0
fi
}

determine_username_from_domainname() {
USERNAME=$(echo $DOMAIN | sed 's,\.,,g;s,-,,g')
echo Constructed database name and user: $USERNAME from $DOMAIN
}

show_result() {
echo "Your Onxshop website is installed in: $HOME_DIRECTORY
To edit the website use
URL: http://${DOMAIN}/edit
Username: ${USERNAME}
Password: ${PASSWORD}
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
setup_variables
test_input
#test_domain_name_is_valid
process_action


