SET client_encoding = 'UNICODE';

BEGIN;

/*common_node*/
INSERT INTO common_node (
id, title, node_type, layout_template, parent, parent_container, priority, teaser, content, description, keywords, page_title, head, body_attributes, modified, created, publish, display_in, author, uri_title, display_permission, other_data
) VALUES (
'0', 'Root', 'site', 'default', '0', '0', 0, '', '', '', '', '', '', '', now(), now(), 0, '', '0', '', 0, ''
);

/*common_configuration*/
INSERT INTO common_configuration VALUES (1, 0, 'global', 'title', 'White Label', '');
INSERT INTO common_configuration VALUES (2, 0, 'global', 'author_content', 'White Label, http://www.example.com/', '');
INSERT INTO common_configuration VALUES (3, 0, 'global', 'credit', '<a href=\"http://onxshop.com\"><span>Ecommerce System</span></a> by <a href=\"http://laposa.co.uk\"><span>Laposa</span></a>', '');
INSERT INTO common_configuration VALUES (4, 0, 'global', 'html_title_suffix', ' - White Label', '');
INSERT INTO common_configuration VALUES (5, 0, 'global', 'locale', 'en_GB.UTF-8', '');
INSERT INTO common_configuration VALUES (6, 0, 'global', 'default_currency', 'GBP', '');
INSERT INTO common_configuration VALUES (7, 0, 'global', 'admin_email', 'norbert@laposa.co.uk', '');

SELECT pg_catalog.setval('common_configuration_id_seq', (SELECT max(id) FROM common_configuration), true);

/*common_image*/

/*common_file*/

/*common_print_article*/

/*common_uri_mapping*/

/*common_email_form*/

/*common_taxonomy_label*/
INSERT INTO common_taxonomy_label VALUES (0, 'Root', '', 0);
INSERT INTO common_taxonomy_label VALUES (1, 'Brands', '', 0);
INSERT INTO common_taxonomy_label VALUES (2, 'Products categories', '', 0);
INSERT INTO common_taxonomy_label VALUES (3, 'Blog categories', '', 0);

SELECT pg_catalog.setval('common_taxonomy_label_id_seq', (SELECT max(id) FROM common_taxonomy_label), true);

/*common_taxonomy_tree*/
INSERT INTO common_taxonomy_tree VALUES (0, 0, 0);
INSERT INTO common_taxonomy_tree VALUES (1, 1, 0);
INSERT INTO common_taxonomy_tree VALUES (2, 2, 0);
INSERT INTO common_taxonomy_tree VALUES (3, 3, 0);

SELECT pg_catalog.setval('common_taxonomy_tree_id_seq', (SELECT max(id) FROM common_taxonomy_tree), true);

/*common_taxonomy_label_image*/

/*common_node_taxonomy*/


COMMIT;
