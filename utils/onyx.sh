#!/bin/bash -e

## Onyx.sh
## Norbert @ Laposa Ltd, 2012-2018
##
## Very simple Onyx websites management script.
## Creates Onyx installation and vhost file depending on required hostname.
## Usage:
##
##     @script.name ACTION HOSTNAME [options]
##
## Example: You want your website to be served from http://example.com
## Then run:
## sudo @script.name create example.com
##
##
## Options:
##     -h, --help                        All client scripts have this, it can be omitted.
##         --template=VALUE              Project skeleton template, e.g. base_with_blog, default is: base
##         --db-template-file=VALUE      Database template
##         --project-skeleton-dir=VALUE  Project skeleton to be used
##         --project-dir=VALUE           Folder where will be application created
##         --db-hostname=VALUE           Database hostname to be used for connection
##         --db-username=VALUE           Database username to be used or created if doesn't exists
##         --db-password=VALUE           Database password to be used
##         --db-name=VALUE               Database name to be created
##         --vhost                       Create Apache vhost file and enable it
##         --ssl                         Create SSL certificate (Let's Encrypt via Certbot)
##         --create-db-user              Create database user
##         --create-db                   Create database

script_dir=$(dirname `realpath "$BASH_SOURCE"`)
source "${script_dir}/easyoptions.sh" || exit

# Arguments
#for argument in "${arguments[@]}"; do
#    echo "Argument specified: $argument"
#done

# Boolean and parameter options
#[[ -n "$template"  ]] && echo "Option specified: --template is $template"
#[[ -n "$db_template_file"  ]] && echo "Option specified: --db-template-file is $db_template_file"
#[[ -n "$project_skeleton_dir"  ]] && echo "Option specified: --project-skeleton-dir is $project_skeleton_dir"
#[[ -n "$project_dir"  ]] && echo "Option specified: --project-dir is $project_dir"
#[[ -n "$db_hostname"  ]] && echo "Option specified: --db-hostname is $db_hostname"
#[[ -n "$db_username"  ]] && echo "Option specified: --db-username is $db_username"
#[[ -n "$db_password"  ]] && echo "Option specified: --db-password is $db_password"
#[[ -n "$db_name"  ]] && echo "Option specified: --db-name is $db_name"
#[[ -n "$vhost"  ]] && echo "Option specified: --vhost"
#[[ -n "$ssl"  ]] && echo "Option specified: --ssl"
#[[ -n "$create_db_user"  ]] && echo "Option specified: --create-db-user"
#[[ -n "$create_db"  ]] && echo "Option specified: --create-db"

# input parameters #

action=${arguments[0]} # mandatory
hostname=${arguments[1]} # mandatory

# prepare functions #

setup_variables() {
onyx_version="1.8"
onyx_version_db=$(echo $onyx_version | sed 's,\.,_,g')
if ! [ $db_hostname ]; then
    db_hostname='localhost';
fi
if ! [ $db_username ]; then
    determine_username_from_domainname
fi
if ! [ $db_name ]; then
    db_name="${db_username}-${onyx_version_db}"
fi
if ! [ $project_dir ]; then
    project_dir="/srv/$hostname"
fi
if ! [ $template ]; then
    template=base
fi
if ! [ $db_template_file ]; then
    db_template_file=/opt/onyx/${onyx_version}/project_skeleton/${template}.sql
fi
if ! [ $project_skeleton_dir ]; then
    project_skeleton_dir=/opt/onyx/$onyx_version/project_skeleton/${template}/
fi

echo "
Variables are set to:

action=$action
hostname=$hostname
template=$template
db_template_file=$db_template_file
project_skeleton_dir=$project_skeleton_dir
project_dir=$project_dir
db_hostname=$db_hostname
db_username=$db_username
db_name=$db_name
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
    if [ -n "$create_db_user"  ] ; then
        create_database_user
    fi
    if [ -n "$create_db"  ] ; then
        create_database
    fi
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
echo -e "*** $1\n*** See https://onxshop.com/";
exit 1; 
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
rm $project_dir/onyx && ln -s /opt/onyx/$onyx_version/ $project_dir/onyx
chmod a+w -R $project_dir/var/
}

create_database_user() {
# TODO create user only if doesn't exist, only works on localhost
sudo -u postgres psql template1 -c "CREATE USER $db_username WITH CREATEDB PASSWORD '$db_password'"
}

create_database() {
export PGPASSWORD=${db_password}
psql -U $db_username -h $db_hostname template1 -c "CREATE DATABASE \"$db_name\" WITH OWNER=\"$db_username\" ENCODING='UTF8'"
}

setup_database() {
export PGPASSWORD=${db_password}
psql -U $db_username -h $db_hostname $db_name < $db_template_file 
psql -U $db_username -h $db_hostname $db_name -c "UPDATE common_configuration SET value='$db_username' WHERE property='title'";
}

change_config() {
deployment_file="$project_dir/conf/deployment.php"
sed -i "s/define('ONYX_DB_USER', '.*')/define('ONYX_DB_USER', '$db_username')/g" $deployment_file
sed -i "s/define('ONYX_DB_PASSWORD', '.*')/define('ONYX_DB_PASSWORD', '$db_password')/g" $deployment_file
sed -i "s/define('ONYX_DB_NAME', '.*')/define('ONYX_DB_NAME', '$db_name')/g" $deployment_file
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

echo "Your Onyx website is installed in: $project_dir
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


