SET client_encoding = 'UNICODE';
BEGIN;
CREATE TABLE client_customer (
    id serial NOT NULL PRIMARY KEY,
    title_before character varying(255),
    first_name character varying(255),
    last_name character varying(255),
    title_after character varying(255),
    email character varying(255),
    "username" character varying(255),
    telephone character varying(255),
    mobilephone character varying(255),
    nickname character varying(255),
    "password" character varying(255),
    company_id integer,
    invoices_address_id integer,
    delivery_address_id integer,
    gender character(1),
    created timestamp(0) without time zone,
    currency_code character(3),
    status smallint,
    newsletter smallint,
    birthday date,
    other_data text,
    modified timestamp(0) without time zone,
	account_type smallint NOT NULL DEFAULT 0,
	agreed_with_latest_t_and_c smallint NOT NULL DEFAULT 0,
	verified_email_address smallint NOT NULL DEFAULT 0
);
INSERT INTO client_customer VALUES (0, '', 'Anonym', 'Anonymouse', '', 'anonym@liquidlight.co.uk', 'anonymouse', '07981168324', '', '', 'laposa', 0, 1, 1, '', now(), 'GBP', 0, 0, now(), '', now());

CREATE TABLE client_company (
    id serial NOT NULL PRIMARY KEY,
    name character varying(255),
    www character varying(255),
    telephone character varying(255),
    fax character varying(255),
    customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
    registration_no character varying(255), 
    vat_no character varying(255),
    other_data text
);

CREATE TABLE client_address (
	id serial NOT NULL PRIMARY KEY,
	customer_id int REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE, 
	country_id int REFERENCES international_country ON UPDATE CASCADE ON DELETE CASCADE,
	name varchar(255) ,
	line_1 varchar(255) ,
	line_2 varchar(255) ,
	line_3 varchar(255) ,
	post_code varchar(255) ,
	city varchar(255) ,
	county varchar(255) ,
	telephone varchar(255) ,
	comment varchar(255) ,
	is_deleted bool  DEFAULT false

);

COMMIT;
