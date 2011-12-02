/*stage1 COMMITED*/
CREATE TABLE client_group (
    id serial NOT NULL PRIMARY KEY,
    name varchar(255) ,
    description text ,
    search_filter text ,
    other_data text
);

ALTER TABLE client_customer ADD COLUMN group_id SMALLINT;
ALTER TABLE client_customer ADD FOREIGN KEY (group_id) REFERENCES client_group ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE client_customer SET password = md5(password);

ALTER TABLE common_node ADD COLUMN display_permission_group_acl TEXT;

/*stage1b */

BEGIN;

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
COMMIT;
ALTER TABLE common_comment ADD COLUMN relation_subject text;

/*stage2 PREPARTION*/

ALTER TABLE ecommerce_product_review ADD COLUMN relation_subject text;
ALTER TABLE ecommerce_product_variety ADD COLUMN reward_points INTEGER;
ALTER TABLE ecommerce_product_variety ADD COLUMN subtitle varchar(255);
/*JING ONLY: UPDATE ecommerce_product_variety SET subtitle = ean13;
UPDATE ecommerce_product_variety SET ean13 = '';*/

CREATE TABLE ecommerce_credit_note (
    id serial NOT NULL PRIMARY KEY,
    customer_id INTEGER REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
    order_id INTEGER REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
    type cash/points, 
    name varchar(255) ,
    description text ,
    search_filter text ,
    other_data text
);

CREATE TABLE client_referral (
    id serial NOT NULL PRIMARY KEY,
    customer_id
    name varchar(255) ,
    hashtag,
    description text ,
    search_filter text ,
    other_data text
);

CREATE TABLE client_referral_usage (
    id serial NOT NULL PRIMARY KEY,
    customer_id
    name varchar(255) ,
    hashtag,
    description text ,
    search_filter text ,
    other_data text
);

ALTER TABLE ecommerce_invoice ADD COLUMN payment_discount;
ALTER TABLE ecommerce_promotion ADD COLUMN generated_by_order_id INTEGER REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE ecommerce_order_log ADD COLUMN description text, other_data text;

ALTER TABLE common_email_form RENAME TO common_email;
