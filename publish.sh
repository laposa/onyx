#!/bin/sh
# this script prepares production version of Onxshop
# 1. all CSS in one file
# 2. default  link in project_skeleton/onxshop_dir symlink to /opt/onxshop-latest
# Norbert Laposa, 2006, 2010, 2011

DEVELOPMENT_VERSION="/opt/onxshop/dev/";

DEPLOY_VERSION="testing";
BASE_DIR="/opt/onxshop/";
FULL_PATH=$BASE_DIR$DEPLOY_VERSION;

#backup
DATE=`date -u +%F_%H%M%S`;
BACKUP_PATH="${BASE_DIR}onxshop-testing-changes/${DATE}";



echo "------------------------------------------------";
echo "PUBLISHING $DEVELOPMENT_VERSION TO $FULL_PATH";
echo "------------------------------------------------";

rsync --recursive --backup --backup-dir=$BACKUP_PATH --times --cvs-exclude --delete-after --links --safe-links --compress --progress --whole-file ${1}\
	--exclude '._*' \
	--exclude '.*.swp' \
	--exclude '.DS*' \
	--exclude 'Thumbs.db' \
	--exclude 'publish.sh' \
	--exclude 'rsync.sh' \
	--exclude 'opt/_rubish/' \
	--exclude 'ONXSHOP_VERSION' \
	--exclude '.git/' \
	--exclude 'project_skeleton/onxshop_dir' \
	${DEVELOPMENT_VERSION}* \
	$FULL_PATH


SCREEN_CSS="${FULL_PATH}/share/css/default/screen.css";
echo "------------------------------------------------";
echo "COMPILING ${SCREEN_CSS}";
echo "------------------------------------------------";
cat ${FULL_PATH}/share/css/default/src/global.css > ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/layout.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/onxshop.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/onxshop_ecommerce.css >> ${SCREEN_CSS};

#echo "------------------------------------------------";
#echo "CREATING onxshop_dir SYMLINK IN PROJECT SKELETON";
#echo "------------------------------------------------";
#ln -s /opt/onxshop-latest $FULL_PATH/project_skeleton/onxshop_dir

