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
/*ALTER TABLE client_customer DROP COLUMN company_id;*/
/*ALTER TABLE common_node ADD COLUMN group_permission SMALLINT NOT NULL DEFAULT 0;*/
