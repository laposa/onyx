#!/bin/bash
# Norbert Laposa, 2007
# Hugo Dvorak, 2014-2017

# help
echo
echo "Usage: ${0} [YES] [file/dir (use to sync only a specific file or subdirectory)]"
echo

# arguments
CONFIRM="${1}"
FILE=""

# allow syncing only specific file/subdir
if [ $# -eq 2 ]; then
	FILE="${2}"
	if [ ! -f $FILE ] && [ ! -d $FILE ] ; then
	    echo "$FILE is an invalid path"
	    exit 1
	fi
fi

# setting local environment
PROJECT_LOCAL_PATH="./${FILE}"

# setting remote environment
PROJECT_REMOTE_USERNAME="username";
PROJECT_REMOTE_SERVER="example.com";
PROJECT_REMOTE_BASE_DIR="/home/example/example.com/";
PROJECT_REMOTE_FULL_PATH="${PROJECT_REMOTE_USERNAME}@${PROJECT_REMOTE_SERVER}:${PROJECT_REMOTE_BASE_DIR}${FILE}"
PROJECT_REMOTE_FULL_PUBLIC_DIR="${PROJECT_REMOTE_FULL_PATH}"

# backup
DATE=`date -u +%F_%H%M%S`
BACKUP_PATH="${PROJECT_REMOTE_BASE_DIR}_resources/backup/${DATE}"

# dry run?
if [ "$CONFIRM" != "YES" ]; then
	echo "Add parameter YES, if you want to write changes."
	echo
	echo "Target path: $PROJECT_REMOTE_FULL_PUBLIC_DIR"
	NOPROCESS="-n"
else
	echo "Syncing $PROJECT_LOCAL_PATH with $PROJECT_REMOTE_FULL_PUBLIC_DIR"
	echo "Backup in $BACKUP_PATH"
fi
echo

# process rsync
rsync --recursive --backup --backup-dir=$BACKUP_PATH --times --perms -e 'ssh -p 22' --links --safe-links --delete-after --compress --progress --whole-file ${NOPROCESS}\
	--exclude '*.marks' \
	--exclude '._*' \
	--exclude 'temp_CACHED_*' \
	--exclude '*.cache' \
	--exclude '.htpasswd' \
	--exclude '.htaccess' \
	--exclude '.DS*' \
	--exclude 'Thumbs.db' \
	--exclude 'var/' \
	--exclude '_resources/' \
	--exclude 'rsync*.sh' \
	--exclude 'conf/payment/' \
	--exclude '.git/' \
	--exclude 'deployment.php' \
	--exclude 'onxshop_dir' \
	--exclude 'fix_permissions.sh' \
	--exclude '.gitignore' \
	$PROJECT_LOCAL_PATH \
	$PROJECT_REMOTE_FULL_PUBLIC_DIR
