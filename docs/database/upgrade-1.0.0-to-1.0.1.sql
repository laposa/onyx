UPDATE common_node SET content = regexp_replace(content, 'a:2:{s:7:"content";s:[0-9]*:"', '') WHERE node_type = 'content' AND layout_template = 'RTE';
UPDATE common_node SET content = regexp_replace(content, '";s:8:"template";s:6:"normal";}', '') WHERE node_type = 'content' AND layout_template = 'RTE';

UPDATE common_node SET content = regexp_replace(content, 'a:2:{s:7:"content";s:[0-9]*:"', '') WHERE node_type = 'page' AND layout_template = 'news';
UPDATE common_node SET content = regexp_replace(content, '";s:8:"template";s:6:"normal";}', '') WHERE node_type = 'page' AND layout_template = 'news';

