BEGIN;

CREATE TABLE client_group (
    id serial NOT NULL PRIMARY KEY,
    name varchar(255) ,
    description text ,
    search_filter text ,
    other_data text
);

ALTER TABLE client_customer ADD COLUMN group_id smallint;
ALTER TABLE client_customer ADD FOREIGN KEY (group_id) REFERENCES client_group ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE client_customer SET password = md5(password);

ALTER TABLE common_node ADD COLUMN display_permission_group_acl text;
ALTER TABLE common_uri_mapping ADD UNIQUE (public_uri);

CREATE TABLE education_survey (
	id serial PRIMARY KEY NOT NULL,
	title varchar(255) NOT NULL,
	description text,
	created timestamp(0) without time zone DEFAULT now() NOT NULL,
	modified timestamp(0) without time zone DEFAULT now(),
	priority smallint DEFAULT 0,
	publish smallint DEFAULT 0
);

CREATE TABLE education_survey_question (
	id serial PRIMARY KEY NOT NULL,
	survey_id int NOT NULL REFERENCES education_survey ON UPDATE CASCADE ON DELETE CASCADE,
	parent int REFERENCES education_survey_question ON UPDATE CASCADE ON DELETE CASCADE,
	step smallint DEFAULT 1,
	title varchar(255) NOT NULL,
	description text,
	mandatory smallint DEFAULT 1,
	type varchar(255) NOT NULL,
	priority smallint DEFAULT 0,
	publish smallint DEFAULT 1
);

CREATE TABLE education_survey_question_answer (
	id serial PRIMARY KEY NOT NULL,
	question_id int NOT NULL REFERENCES education_survey_question ON UPDATE CASCADE ON DELETE CASCADE,
	title text NOT NULL,
	description text,
	is_correct smallint, 
	points smallint,
	priority smallint DEFAULT 0,
	publish smallint DEFAULT 1
);

CREATE TABLE education_survey_entry (
	id serial PRIMARY KEY NOT NULL,
	survey_id int NOT NULL REFERENCES education_survey ON UPDATE CASCADE ON DELETE RESTRICT,
	customer_id int NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
	relation_subject text,
	created timestamp(0) without time zone DEFAULT now() NOT NULL,
	modified timestamp(0) without time zone DEFAULT now(),
	publish smallint DEFAULT 0,
	UNIQUE (survey_id, customer_id, relation_subject)
);

CREATE TABLE education_survey_entry_answer (
	id serial PRIMARY KEY NOT NULL,
	survey_entry_id int NOT NULL REFERENCES education_survey_entry ON UPDATE CASCADE ON DELETE CASCADE,
	question_id int NOT NULL REFERENCES education_survey_question ON UPDATE CASCADE ON DELETE RESTRICT,
	question_answer_id int REFERENCES education_survey_question_answer ON UPDATE CASCADE ON DELETE RESTRICT,
	value text,
	created timestamp(0) without time zone DEFAULT now() NOT NULL,
	modified timestamp(0) without time zone DEFAULT now(),
	publish smallint DEFAULT 0
);

ALTER TABLE common_comment ADD COLUMN relation_subject text;

ALTER TABLE common_email_form RENAME TO common_email;
ALTER SEQUENCE common_email_form_id_seq RENAME TO common_email_id_seq;
ALTER INDEX common_email_form_pkey RENAME TO common_email_pkey;
ALTER TABLE ecommerce_product_review ADD COLUMN relation_subject text;
ALTER TABLE ecommerce_product_variety ADD COLUMN reward_points integer;
ALTER TABLE ecommerce_product_variety ADD COLUMN subtitle varchar(255);
ALTER TABLE ecommerce_invoice ADD COLUMN voucher_discount decimal(12,5);
ALTER TABLE ecommerce_promotion ADD COLUMN generated_by_order_id integer REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE ecommerce_order ADD COLUMN created timestamp(0) without time zone DEFAULT now();
ALTER TABLE ecommerce_order ADD COLUMN modified timestamp(0) without time zone DEFAULT now();
ALTER TABLE ecommerce_order_log ADD COLUMN description text;
ALTER TABLE ecommerce_order_log ADD COLUMN other_data text;

UPDATE ecommerce_invoice
SET voucher_discount = ecommerce_basket.discount_net
FROM ecommerce_basket
LEFT OUTER JOIN ecommerce_order ON (ecommerce_order.basket_id = ecommerce_basket.id)
WHERE ecommerce_invoice.order_id = ecommerce_order.id AND ecommerce_basket.discount_net > 0;

UPDATE ecommerce_invoice
SET goods_net = goods_net + voucher_discount
WHERE voucher_discount > 0;

UPDATE ecommerce_order
SET created = ecommerce_order_log.datetime 
FROM ecommerce_order_log
WHERE ecommerce_order_log.status = 0 AND ecommerce_order_log.order_id = ecommerce_order.id;

UPDATE ecommerce_order
SET modified = ecommerce_order_log.datetime 
FROM ecommerce_order_log
WHERE ecommerce_order_log.status = 1 AND ecommerce_order_log.order_id = ecommerce_order.id;

UPDATE ecommerce_order
SET modified = ecommerce_order_log.datetime 
FROM ecommerce_order_log
WHERE ecommerce_order_log.status = 2 AND ecommerce_order_log.order_id = ecommerce_order.id;

COMMIT;

