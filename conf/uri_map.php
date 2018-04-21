<?php
/** 
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * this file contains mapping of URLs to Onxshop component requests
 * see also model/uri_mapping for URLs mapped in CMS
 * 
 * syntax as htaccess RewriteRule, only extra root / is necessary
 * on the right hand side is expected either "request" or "controller_request"
 */
 
$uri_map = array(
            
    '^/?$' => '/index.php?request=uri_mapping&translate=/',
    '^/sitemap.xml$' => '/index.php?request=export/xml_googlesitemap',
    '^/imagesxml/([0-9]*)$' => '/index.php?request=export/imagesxml&role=page&node_id=$1',
    '^/api/v([0-9]*).([a-z0-9-_\.]*)/(.*)$' => '/index.php?request=uri_mapping&controller_request=api/v$1_$2/$3&version=v$1_$2',
    '^/api/(.*)$' => '/index.php?request=uri_mapping&controller_request=api/$1',
    
    '^/product/([0-9]*)$' => '/index.php?request=forward&product_id=$1',
    '^/recipe/([0-9]*)$' => '/index.php?request=forward&recipe_id=$1',
    '^/store/([0-9]*)$' => '/index.php?request=forward&store_id=$1',
    '^/client/logout$' => '/index.php?request=component/client/logout',
    
    '^/request/(.*)$' => '/index.php?request=uri_mapping&controller_request=$1',
    '^/edit/?$' => '/index.php?request=bo/fe_edit',
    
    '^/backoffice' => array(
        '^/backoffice/?$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/summary',
        '^/backoffice/my$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/generic.bo/component/client/edit_profile',
        '^/backoffice/pages$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/pages',
        '^/backoffice/pages/([0-9]*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice~id=$1~.bo/pages/pages~id=$1~',
        '^/backoffice/news$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/news',
        '^/backoffice/news/([0-9]*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/news&blog_node_id=$1',
        '^/backoffice/news/edit/([a-z]*)/([0-9]*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/news_edit~id=$2~.bo/component/node_edit~id=$2~',
        '^/backoffice/comments$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/comments',
        '^/backoffice/comments/products$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/comments&products=1',
        '^/backoffice/comments/recipes$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/comments&recipes=1',
        '^/backoffice/surveys$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/surveys',
        '^/backoffice/surveys/([0-9]*)/detail$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/survey_detail&id=$1',
        '^/backoffice/surveys/([0-9]*)/edit$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/survey_edit&id=$1',
        '^/backoffice/marketing$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/marketing',
        '^/backoffice/advanced/media$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/server_browser',
        '^/backoffice/advanced/taxonomy$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/taxonomy.bo/component/taxonomy_edit',
        '^/backoffice/advanced/taxonomy/properties/([0-9]*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/taxonomy~id=$1~.bo/component/taxonomy_edit~id=$1~',
        '^/backoffice/advanced/taxonomy/add/([0-9]*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/taxonomy~id=$1~.bo/component/taxonomy_add~parent=$1~',
        '^/backoffice/stats$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/statistic',
        '^/backoffice/advanced$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/component/advanced_intro',
        '^/backoffice/advanced/configuration$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/configuration',
        '^/backoffice/advanced/currency$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/currency',
        '^/backoffice/advanced/logs$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/logs',
        '^/backoffice/advanced/templates$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/templates',
        '^/backoffice/advanced/database$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/database',
        '^/backoffice/advanced/database/browse$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/database_browse',
        '^/backoffice/advanced/database/browse/model/([a-z\/_\.]*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/database_browse&model=$1',
        '^/backoffice/advanced/database/export$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/database_export',
        '^/backoffice/advanced/database/import$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/database_import',
        '^/backoffice/advanced/tools$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/tools',
        '^/backoffice/advanced/api$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/pages/api',
        '^/backoffice/advanced/search_index$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/component/search_index',
        '^/backoffice/advanced/scheduler$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/component/scheduler',
        '^/backoffice/advanced/seo_manager$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/advanced.bo/component/seo_manager',
        '^/backoffice/stock$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/ecommerce/stock',
        '^/backoffice/products$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/ecommerce/products.bo/component/ecommerce/product_list',
        '^/backoffice/products/product_add$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/product_add_quick',
        '^/backoffice/products/offer_add$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/offer_add',
        '^/backoffice/products/offer_group_add$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/offer_group_edit',
        '^/backoffice/products/offer_group/([0-9]*)/edit$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/offer_group_edit&offer_group_id=$1',
        '^/backoffice/products/([0-9]*)/variety_add$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/product_variety_add&product_id=$1',
        '^/backoffice/products/([0-9]*)/edit$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/product_edit&id=$1',
        '^/backoffice/products/offer/([0-9]*)/edit$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/offer_group_edit&id=$1',
        '^/backoffice/products/variety/([0-9]*)/edit$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/product_variety_edit&id=$1',
        '^/backoffice/recipes$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/ecommerce/recipes.bo/component/ecommerce/recipe_list',
        '^/backoffice/recipes/recipe_add$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/recipe_add',
        '^/backoffice/recipes/([0-9]*)/edit$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/recipe_edit&id=$1',
        '^/backoffice/stores$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/ecommerce/stores.bo/component/ecommerce/store_list',
        '^/backoffice/stores/store_add$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/store_add',
        '^/backoffice/stores/([0-9]*)/edit$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/component/ecommerce/store_edit&id=$1',
        '^/backoffice/orders$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/ecommerce/orders',
        '^/backoffice/orders/([0-9]*)/detail$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/ecommerce/order_detail&id=$1',
        '^/backoffice/stock$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/ecommerce/stock',
        '^/backoffice/customers$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/client/customers',
        '^/backoffice/customers/add$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/client/customer_add',
        '^/backoffice/customers/([0-9]*)/detail$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/backoffice.bo/pages/client/customer_detail&id=$1'
    ),
    
    '^/popup' => array(
        '^/popup_window/(.*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/popup.$1',
        '^/popup/edit/([a-z]*)/([0-9]*)/orig/(.*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/popup.bo/component/node_edit~id=$2~&orig=$3&popup=1',
        '^/popup/properties/([0-9]*)/orig/(.*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/popup.bo/component/node_edit&id=$1&orig=$2&popup=1',
        '^/popup/properties/([0-9]*)/delete$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/popup.bo/component/node_edit~id=$1:delete=1~',
        '^/popup/add/([a-z]*)/([0-9]*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/popup.bo/component/node_add&parent=$2&container=$3&node_group=$1&popup=1',
        '^/popup/add/([a-z]*)/([0-9]*)/([0-9]*)/orig/(.*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/popup.bo/component/node_add&parent=$2&container=$3&node_group=$1&orig=$4&popup=1',
        '^/popup/files/([a-z_]*)/([0-9]*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/popup.bo/component/file_list~relation=$1:node_id=$2~',
        '^/popup/css_edit/([0-9]*)/orig/(.*)$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/popup.bo/component/css_edit~popup=1:orig=$2:id=$1~',
        '^/popup/logs$' => '/index.php?request=sys/html5.bo/backoffice_wrapper.bo/popup.bo/component/logs',
        '^/popupimage/(.*)$' => '/index.php?request=sys/html5.component/popimage&src=$1'
    ),
    
    '^/ajax/add/' => array(
        '^/ajax/add/([a-z]*)/([0-9]*)$' => '/index.php?request=bo/component/node_add&parent=$2&container=$3&node_group=$1&ajax=1&dontforward=1',
        '^/ajax/add/([a-z]*)/([0-9]*)/([0-9]*)/orig/(.*)$' => '/index.php?request=bo/component/node_add&parent=$2&container=$3&node_group=$1&orig=$4&ajax=1&dontforward=1'
    ),
    
    '^/export/' => array(
        '^/export/([a-z]*)/([a-z]*)$' => '/index.php?request=export/$1_$2',
        '^/export/([a-z]*)/([a-z]*)/([0-9]*)$' => '/index.php?request=export/$1_$2&id=$3',
    ),
    
    '^/print' => array(
        '^/print/invoice/(.*)$' => '/index.php?request=sys/html5.node/site/print.component/ecommerce/invoice&id=$1',
        '^/print/invoice_proforma/(.*)$' => '/index.php?request=sys/html5.node/site/print.component/ecommerce/invoice@component/ecommerce/invoice_proforma&id=$1',
        '^/print/gift_card/(.*)$' => '/index.php?request=sys/html5.node/site/print.component/ecommerce/gift_card&order_id=$1'
    )

);
