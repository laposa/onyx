SET client_encoding = 'UNICODE';
BEGIN;

CREATE TABLE common_comment ( 
	id serial PRIMARY KEY NOT NULL,
	parent int REFERENCES common_comment ON UPDATE CASCADE ON DELETE CASCADE,
	node_id int REFERENCES common_node ON UPDATE CASCADE ON DELETE RESTRICT,
	title varchar(255) ,
	content text ,
	author_name varchar(255) ,
	author_email varchar(255) ,
	author_website varchar(255) ,
	author_ip_address varchar(255),
	customer_id int NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
	created timestamp(0) default now(),
	publish smallint,
	rating default 0

);

CREATE TABLE common_session ( 

	id serial NOT NULL PRIMARY KEY,
	session_id varchar(32) ,
	session_data text ,
	customer_id int REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE, 
	created timestamp(0) without time zone,
	modified timestamp(0) without time zone,
	ip_address varchar(255),
	php_auth_user varchar(255),
	http_referer text,
	http_user_agent varchar(255)

);

CREATE TABLE common_session_archive ( 

	id serial NOT NULL PRIMARY KEY,
	session_id varchar(32) ,
	session_data text ,
	customer_id int REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE, 
	created timestamp(0) without time zone,
	modified timestamp(0) without time zone,
	ip_address varchar(255),
	php_auth_user varchar(255),
	http_referer text,
	http_user_agent varchar(255)

);


COMMIT;
