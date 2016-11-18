#!/bin/bash -e

## Onxshop.sh
## Norbert @ Laposa Ltd, 2012-2016
##
## Very simple Onxshop websites management script.
## Creates Onxshop installation and vhost file depending on required hostname.
## Usage:
##
##     @script.name ACTION HOSTNAME [options]
##
## Example: You want your website to be served from http://example.com
## Then run:
## sudo onxshop create example.com
##
##
## Options:
##     -h, --help                        All client scripts have this, it can be omitted.
##         --db-template-file=VALUE      Database template
##         --project-skeleton-dir=VALUE  Project skeleton to be used
##         --project-dir=VALUE           Folder where will be application created
##         --db-username=VALUE           Database username to be used or created if doesn't exists
##         --db-password=VALUE           Database password to be used
##         --vhost                       Create Apache vhost file and enable it
##         --ssl                         Create SSL certificate (Let's Encrypt via Certbot)

script_dir=$(dirname "$BASH_SOURCE")
source "${script_dir}/easyoptions.sh" || exit

# Arguments
#for argument in "${arguments[@]}"; do
#    echo "Argument specified: $argument"
#done

# Boolean and parameter options
#[[ -n "$db_template_file"  ]] && echo "Option specified: --db-template-file is $db_template_file"
#[[ -n "$project_skeleton_dir"  ]] && echo "Option specified: --project-skeleton-dir is $project_skeleton_dir"
#[[ -n "$project_dir"  ]] && echo "Option specified: --project-dir is $project_dir"
#[[ -n "$db_username"  ]] && echo "Option specified: --db-username is $db_username"
#[[ -n "$db_password"  ]] && echo "Option specified: --db-password is $db_password"
#[[ -n "$vhost"  ]] && echo "Option specified: --vhost"
#[[ -n "$ssl"  ]] && echo "Option specified: --ssl"

# input parameters #

action=${arguments[0]} # mandatory
hostname=${arguments[1]} # mandatory

# prepare functions #

setup_variables() {
onxshop_version="1.7"
onxshop_version_db=$(echo $onxshop_version | sed 's,\.,_,g')
if ! [ $db_username ]; then
	determine_username_from_domainname
fi
if ! [ $project_dir ]; then
	project_dir="/srv/$hostname"
fi
if ! [ $db_template_file ]; then
	db_template_file=/opt/onxshop/${onxshop_version}/project_skeleton/base_with_blog.sql
fi
if ! [ $project_skeleton_dir ]; then
	project_skeleton_dir=/opt/onxshop/$onxshop_version/project_skeleton/base_with_blog/
fi

echo "
Variables are set to:

action=$action
hostname=$hostname
db_template_file=$db_template_file
project_skeleton_dir=$project_skeleton_dir
project_dir=$project_dir
db_username=$db_username
"

}

test_input() {
if ! [ $action ]; then
	die "action not provided"
fi

if ! [ $hostname ]; then
	die "hostname not provided"
fi
}

create_new_installation() {
	get_password
	copy_files
	setup_database
	change_config
	if [ -n "$vhost"  ] ; then
		create_vhost
	fi
	show_result
}

# Universal function for bailing out
die() {
$0 -h
echo -e "*** $1\n*** See https//onxshop.com/"; exit 1; 
}

get_password() {
if ! [ $db_password ]; then
	random_password
fi
}

random_password() {
# using pwgen if installed
# db_password="`pwgen -N 1`"

# Generate a random password without Perl
MATRIX="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
LENGTH="8"
while [ "${n:=1}" -le "$LENGTH" ]
do
    db_password="$db_password${MATRIX:$(($RANDOM%${#MATRIX})):1}"
	let n+=1
done
}

copy_files() {
if [ -d $project_dir ]; then
	die "Directory $project_dir already exists"
fi
cp -a $project_skeleton_dir $project_dir
rm $project_dir/onxshop_dir && ln -s /opt/onxshop/$onxshop_version/ $project_dir/onxshop_dir
chmod a+w -R $project_dir/var/
}

setup_database() {
db_name="${db_username}-${onxshop_version_db}"
# TODO create user only if doesn't exist
sudo -u postgres psql template1 -c "CREATE USER $db_username WITH CREATEDB NOCREATEUSER PASSWORD '$db_password'"
sudo -u postgres psql template1 -c "CREATE DATABASE \"$db_name\" WITH OWNER=\"$db_username\" ENCODING='UTF8'"
export PGPASSWORD=${db_password}
psql -U ${db_username} -h localhost $db_name < $db_template_file 
psql -U ${db_username} -h localhost $db_name -c "UPDATE common_configuration SET value='$db_username' WHERE property='title'";
}

change_config() {
deployment_file="$project_dir/conf/deployment.php"
sed -i "s/define('ONXSHOP_DB_USER', '.*')/define('ONXSHOP_DB_USER', '$db_username')/g" $deployment_file
sed -i "s/define('ONXSHOP_DB_PASSWORD', '.*')/define('ONXSHOP_DB_PASSWORD', '$db_password')/g" $deployment_file
sed -i "s/define('ONXSHOP_DB_NAME', '.*')/define('ONXSHOP_DB_NAME', '$db_name')/g" $deployment_file
}

create_vhost() {
vhost_file="/etc/apache2/sites-available/${hostname}.conf"
if [ -f ${vhost_file} ]; then
    die "Vhost file ${vhost_file} already exists"
fi
echo "<VirtualHost *:80>
	ServerName ${hostname}
	VirtualDocumentRoot ${project_dir}/public_html
</VirtualHost>" > ${vhost_file} || die "Couldn't add vhost file ${vhost_file}"

a2ensite ${hostname} && service apache2 reload

if [ -n "$ssl"  ] ; then
	if which certbot >/dev/null; then
    	echo 'found certbot, executing'
    	certbot --apache --redirect --non-interactive --text -d ${hostname} 2>&1
	else
		echo "Couldn't create SSL certificate, please insteall Certbot"
	fi
fi

}

test_hostname_is_valid() {
if [ $hostname ] ; then
ping -c 1 `echo $hostname | sed 's/:[0-9]\+//'` || exit 1
else
   die "provide a valid hostname"
   exit 0
fi
}

determine_username_from_domainname() {
db_username=$(echo $hostname | sed 's,\.,,g;s,-,,g')
echo Constructed database name and user: $db_username from $hostname
}

show_result() {

if [ -n "$ssl"  ] ; then 
	protocol='https';
else 
	protocol='http';
fi

echo "Your Onxshop website is installed in: $project_dir
To edit the website use
URL: ${protocol}://${hostname}/edit
Username: ${db_username}
Password: ${db_password}
"
}

process_action() {
case "$action" in
  create)
    create_new_installation
    ;;
  backup)
    die "not implemented"
    ;;
  '')
    die "Sorry, no action provided"
    ;;
  *)
    die "Sorry, unknown action"
    ;;
esac
}

# start procedure here ##
test_input
setup_variables
#test_hostname_is_valid
process_action


