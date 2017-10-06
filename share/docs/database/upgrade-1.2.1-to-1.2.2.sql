BEGIN;
UPDATE common_node SET layout_template = 'default', publish = 1 WHERE id = 0;
INSERT INTO common_node VALUES (88, 'Global Navigation', 'container', 'default', 0, 0, 0, '', NULL, '', '', '', '', NULL, '2009-08-16 13:05:12', '2009-08-16 13:06:13', 1, 'itself', 1000, '', 0, 'N;', '', '', 'N;', 'N;', 1, 0, NULL, 0, '', 0);
UPDATE common_node SET parent = 88 WHERE id = 15;
COMMIT;
