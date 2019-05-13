#!/bin/sh
FULL_PATH="."
COMPILED_JS="${FULL_PATH}/share/js/compiled.js";

echo "------------------------------------------------";
echo "COMPILING ${COMPILED_JS}";
echo "------------------------------------------------";
echo "/* compiled by utils/compile_js.sh */" > ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/jquery.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/jquery-migrate.js >> ${COMPILED_JS}  && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/plugins/jquery.tools.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/plugins/jquery.easing.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/plugins/jquery.form.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/plugins/jquery.jgrowl.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/plugins/jquery.validate.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/plugins/reflection.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/plugins/jquery.form.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/jquery/plugins/jquery.mousewheel.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
#cat ${FULL_PATH}/share/js/jquery/plugins/jquery.fancybox.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/js.cookie.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
cat ${FULL_PATH}/share/js/common.js >> ${COMPILED_JS} && echo "" >> ${COMPILED_JS}
