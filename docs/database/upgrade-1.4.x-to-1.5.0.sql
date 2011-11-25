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

/*stage2 PREPARTION*/

ALTER TABLE ecommerce_product_variety ADD COLUMN reward_points;

CREATE TABLE ecommerce_credit_note (
    id serial NOT NULL PRIMARY KEY,
    customer_id INTEGER REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
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

/*stage3 PREPARATION: other cleaning*/
ALTER TABLE client_customer DROP COLUMN company_id;
ALTER TABLE ecommerce_invoice ADD COLUMN payment_discount;
ALTER TABLE ecommerce_promotion ADD COLUMN generated_by_order_id INTEGER REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE ecommerce_order_log ADD COLUMN description text, other_data text;

/*rename DB common_email_form to common_email*/