BEGIN;

ALTER TABLE common_node ADD COLUMN custom_fields jsonb;

CREATE INDEX common_node_custom_fields_idx ON common_node USING gin (custom_fields);

UPDATE common_node SET node_controller = lower(node_controller);

UPDATE common_node SET node_controller = 'image_gallery' WHERE node_controller = 'picture' AND node_group='content';

ALTER TABLE common_node RENAME COLUMN teaser TO strapline;

COMMIT;
