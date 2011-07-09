SET client_encoding = 'UNICODE';

BEGIN;
CREATE TABLE common_node (
	id serial NOT NULL PRIMARY KEY,
	title character varying(255) NOT NULL,
	node_group character varying(255) NOT NULL,
	node_controller character varying(255),
	parent integer REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
	parent_container smallint DEFAULT 0 NOT NULL,
	priority integer DEFAULT 0 NOT NULL,
	teaser text,
	content text,
	description text,
	keywords text,
	page_title character varying(255),
	head text,
	body_attributes character varying(255),
	created timestamp(0) without time zone DEFAULT now() NOT NULL,
	modified timestamp(0) without time zone DEFAULT now() NOT NULL,
	publish integer DEFAULT 0 NOT NULL,
	display_in_menu smallint DEFAULT 1 NOT NULL,
	author integer NOT NULL,
	uri_title character varying(255),
	display_permission smallint DEFAULT 0 NOT NULL,
	other_data	text,
	css_class character varying(255) DEFAULT '' NOT NULL,
	layout_style character varying(255) DEFAULT '' NOT NULL,
	component text,
	relations text,
	display_title smallint,
	display_secondary_navigation smallint,
	require_login smallint,
	display_breadcrumb smallint NOT NULL DEFAULT 0,
	browser_title varchar(255) NOT NULL DEFAULT '',
	link_to_node_id integer NOT NULL DEFAULT 0,
	require_ssl smallint NOT NULL DEFAULT 0
);

INSERT INTO "common_node" (
"id", "title", "node_type", "layout_template", "parent", "parent_container", "priority", "teaser", "content", "description", "keywords", "page_title", "head", "body_attributes", "modified", "created", "publish", "display_in", "author", "uri_title", "display_permission", "other_data"
) 
VALUES (
'0', 'Root', 'site', 'default', '0', '0', 0, '', '', '', '', '', '', '', now(), now(), 0, '', '0', '', 0, ''
);

CREATE TABLE common_configuration ( 
	id serial NOT NULL PRIMARY KEY,
	node_id int NOT NULL DEFAULT 0 REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE,
	object varchar(255) ,
	property varchar(255) ,
	value text ,
	description text 

);

INSERT INTO common_configuration VALUES (1, 0, 'global', 'title', 'White Label', '');
INSERT INTO common_configuration VALUES (2, 0, 'global', 'author_content', 'White Label, http://www.example.com/', '');
INSERT INTO common_configuration VALUES (3, 0, 'global', 'credit', '<a href="http://www.onxshop.com"><span>Ecommerce System</span></a> by <a href="http://ln5.co.uk"><span>LN5</span></a>', '');
INSERT INTO common_configuration VALUES (4, 0, 'global', 'html_title_suffix', ' - White Label', '');
INSERT INTO common_configuration VALUES (5, 0, 'global', 'locale', 'en_GB.UTF-8', '');
INSERT INTO common_configuration VALUES (6, 0, 'global', 'default_currency', 'GBP', '');
INSERT INTO common_configuration VALUES (7, 0, 'global', 'admin_email', 'norbert.laposa@gmail.com', '');

SELECT pg_catalog.setval('common_configuration_id_seq', (SELECT max(id) FROM common_configuration), true);

CREATE TABLE common_image (
    id serial NOT NULL PRIMARY KEY,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer
);

CREATE TABLE common_file ( 

	id serial NOT NULL PRIMARY KEY,
	src varchar(255),
	role character varying(255),
	node_id int NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
	title varchar(255) ,
	description text ,
	priority int DEFAULT 0 NOT NULL,
	modified timestamp(0) ,
	author int 

);

CREATE TABLE common_print_article ( 
	id serial PRIMARY KEY,
	src varchar(255) ,
	role varchar(255) ,
	node_id int NOT NULL REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE,
	title varchar(255) NOT NULL,
	description text ,
	priority int DEFAULT 0 NOT NULL,
	modified timestamp(0) ,
	author int,
	type varchar(255) ,
	authors text ,
	issue_number int ,
	page_from int ,
	date date ,
	other text
);


CREATE TABLE common_uri_mapping (
    id serial NOT NULL PRIMARY KEY,
    node_id integer REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
    public_uri text,
    "type" character varying(255)
);


CREATE TABLE common_email_form (
    id serial NOT NULL PRIMARY KEY,
    email_from character varying(255),
    name_from character varying(255),
    subject character varying(255),
    content text,
    template character varying(255),
    email_recipient character varying(255),
    name_recipient character varying(255),
    created timestamp(0) without time zone,
    ip character varying(255)
);

CREATE TABLE common_taxonomy_label ( 
	id serial NOT NULL PRIMARY KEY,
	title varchar(255) NOT NULL ,
	description text ,
	priority int DEFAULT 0 NOT NULL,
	publish integer DEFAULT 1 NOT NULL
);





CREATE TABLE common_taxonomy_tree ( 
  id serial PRIMARY KEY NOT NULL,
  label_id int NOT NULL REFERENCES common_taxonomy_label ON UPDATE CASCADE ON DELETE CASCADE,
  parent int  REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE,
  priority smallint DEFAULT 0 NOT NULL,
  publish smallint DEFAULT 1 NOT NULL

);


CREATE TABLE common_taxonomy_label_image ( 
	id serial NOT NULL PRIMARY KEY,
	src character varying(255),
	role character varying(255),
	node_id int NOT NULL REFERENCES common_taxonomy_label ON UPDATE CASCADE ON DELETE CASCADE,
	title character varying(255),
	description text,
	priority integer DEFAULT 0 NOT NULL,
	modified timestamp(0) without time zone,
	author integer
);


CREATE TABLE common_node_taxonomy ( 
	id serial NOT NULL PRIMARY KEY,
	node_id int NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
	taxonomy_tree_id int NOT NULL REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE
);

ALTER TABLE common_node_taxonomy ADD CONSTRAINT node_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);


INSERT INTO common_taxonomy_label VALUES (0, 'Root', '', 0);
INSERT INTO common_taxonomy_label VALUES (1, 'Brands', '', 0);
INSERT INTO common_taxonomy_label VALUES (2, 'Products categories', '', 0);
INSERT INTO common_taxonomy_label VALUES (3, 'Blog categories', '', 0);

INSERT INTO common_taxonomy_tree VALUES (0, 0, 0);
INSERT INTO common_taxonomy_tree VALUES (1, 1, 0);
INSERT INTO common_taxonomy_tree VALUES (2, 2, 0);
INSERT INTO common_taxonomy_tree VALUES (3, 3, 0);

SELECT pg_catalog.setval('common_taxonomy_label_id_seq', (SELECT max(id) FROM common_taxonomy_label), true);
SELECT pg_catalog.setval('common_taxonomy_tree_id_seq', (SELECT max(id) FROM common_taxonomy_tree), true);
COMMIT;
