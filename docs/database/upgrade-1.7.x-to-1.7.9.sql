BEGIN;

ALTER TABLE client_customer ALTER facebook_id TYPE character varying(255);
ALTER TABLE client_customer ALTER twitter_id TYPE character varying(255);
ALTER TABLE client_customer ALTER google_id TYPE character varying(255);

COMMIT;
