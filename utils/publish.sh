#!/bin/sh
# Norbert Laposa, 2006, 2010, 2011, 2013
# 
# this script prepares production version of Onxshop
#
# 1. cleanup
# 2. all CSS in one file
# 3. all js in one file
# 4. change symlink in project_skeleton/
# 

DEVELOPMENT_VERSION="/opt/onxshop/dev/";

DEPLOY_VERSION="1.6-testing";
BASE_DIR="/opt/onxshop/";
FULL_PATH=$BASE_DIR$DEPLOY_VERSION;

echo "------------------------------------------------";
echo "PUBLISHING $DEVELOPMENT_VERSION TO $FULL_PATH";
echo "------------------------------------------------";

# 1. cleanup
rsync --recursive --times --cvs-exclude --delete-after --links --safe-links --compress --progress --whole-file --checksum ${1}\
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

# 2. all CSS in one file
SCREEN_CSS="${FULL_PATH}/share/css/default/screen.css";
echo "------------------------------------------------";
echo "COMPILING ${SCREEN_CSS}";
echo "------------------------------------------------";
cat ${FULL_PATH}/share/css/default/src/global.css > ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/layout.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/onxshop.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/onxshop_ecommerce.css >> ${SCREEN_CSS};

# 3. all JS in one file
echo "------------------------------------------------";
echo "COMPILING share/js/compiled.js";
echo "------------------------------------------------";

cd $DEVELOPMENT_VERSION && utils/compile_js_common.sh 

# 4. change symlink
#echo "------------------------------------------------";
#echo "CREATING onxshop_dir SYMLINK IN PROJECT SKELETON";
#echo "------------------------------------------------";
#ln -s /opt/onxshop/latest $FULL_PATH/project_skeleton/onxshop_dir

