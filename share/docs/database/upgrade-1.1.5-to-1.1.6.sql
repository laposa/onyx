UPDATE common_node SET content = regexp_replace(content, 'thumbnail/80/var/files', 'thumbnail/75/var/files', 'g');
UPDATE common_node SET teaser = regexp_replace(teaser, 'thumbnail/80/var/files', 'thumbnail/75/var/files', 'g');
UPDATE ecommerce_product SET teaser = regexp_replace(teaser, 'thumbnail/80/var/files', 'thumbnail/75/var/files', 'g');
UPDATE ecommerce_product SET description = regexp_replace(description, 'thumbnail/80/var/files', 'thumbnail/75/var/files', 'g');
