BEGIN;

ALTER TABLE client_customer ALTER facebook_id TYPE character varying(255);
ALTER TABLE client_customer ALTER twitter_id TYPE character varying(255);
ALTER TABLE client_customer ALTER google_id TYPE character varying(255);

ALTER TABLE common_node ADD COLUMN custom_fields jsonb;

CREATE INDEX common_node_custom_fields_idx ON common_node USING gin (custom_fields);

COMMIT;
