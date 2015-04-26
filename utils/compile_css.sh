#!/bin/sh
FULL_PATH="."
SCREEN_CSS="${FULL_PATH}/share/css/default/screen.css";

echo "------------------------------------------------";
echo "COMPILING ${SCREEN_CSS}";
echo "------------------------------------------------";
cat ${FULL_PATH}/share/css/default/src/global.css > ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/layout.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/onxshop.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/onxshop_ecommerce.css >> ${SCREEN_CSS};
