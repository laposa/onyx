#!/bin/sh
# Norbert Laposa, 2006, 2010, 2011

DEVELOPMENT_VERSION="/opt/onxshop-dev/";

DEPLOY_VERSION="onxshop-testing";
BASE_DIR="/opt/";
FULL_PATH=$BASE_DIR$DEPLOY_VERSION;

#backup
DATE=`date -u +%F_%H%M%S`;
BACKUP_PATH="${BASE_DIR}onxshop-testing-changes/${DATE}";

# first agregate SQL to one file
#echo "------------------------------------------------";
#echo "COMPILING ${DEVELOPMENT_VERSION}docs/database/DB_ALL.sql";
#echo "------------------------------------------------";
#
#cat "${DEVELOPMENT_VERSION}docs/database/DB_1COMMON.sql" > "${DEVELOPMENT_VERSION}docs/database/DB_ALL.sql"
#cat "${DEVELOPMENT_VERSION}docs/database/DB_2INTERNATIONAL.sql" >> "${DEVELOPMENT_VERSION}docs/database/DB_ALL.sql"
#cat "${DEVELOPMENT_VERSION}docs/database/DB_3CLIENT.sql" >> "${DEVELOPMENT_VERSION}docs/database/DB_ALL.sql"
#cat "${DEVELOPMENT_VERSION}docs/database/DB_4ECOMMERCE.sql" >> "${DEVELOPMENT_VERSION}docs/database/DB_ALL.sql"
#cat "${DEVELOPMENT_VERSION}docs/database/DB_5COMMON_OTHER.sql" >> "${DEVELOPMENT_VERSION}docs/database/DB_ALL.sql"
#cat "${DEVELOPMENT_VERSION}docs/database/DB_6SHIPPING_ROYAL_MAIL.sql" >> "${DEVELOPMENT_VERSION}docs/database/DB_ALL.sql"
#cat "${DEVELOPMENT_VERSION}docs/database/DB_7CORE_NODES.sql" >> "${DEVELOPMENT_VERSION}docs/database/DB_ALL.sql"
#cat "${DEVELOPMENT_VERSION}docs/database/DB_8INDEXES.sql" >> "${DEVELOPMENT_VERSION}docs/database/DB_ALL.sql"


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
	${DEVELOPMENT_VERSION}* \
	$FULL_PATH


SCREEN_CSS="${FULL_PATH}/share/css/default/screen.css";
echo "------------------------------------------------";
echo "COMPILING ${SCREEN_CSS}";
echo "------------------------------------------------";
cat ${FULL_PATH}/share/css/default/src/global.css > ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/onxshop.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/onxshop_ecommerce.css >> ${SCREEN_CSS};

