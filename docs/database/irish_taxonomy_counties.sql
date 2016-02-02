BEGIN;

--
-- Root Category
--
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Province', E'Counties of Ireland', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), NULL, 0, 1);
INSERT INTO "common_configuration"("node_id", "object", "property", "value", "description", "apply_to_children")
VALUES (0, E'global', E'province_taxonomy_tree_id', currval('common_taxonomy_label_id_seq')::text, E'Counties of Ireland', 0);

CREATE TEMP TABLE ids 
ON COMMIT DROP
AS SELECT currval('common_taxonomy_label_id_seq') AS root_id, 0 AS connacht_id, 0 AS leinser_id, 0 AS munster_id, 0 AS ulster_id;

--
-- Provinces
--
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Connacht', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT root_id FROM ids), 0, 1);
UPDATE ids SET connacht_id = currval('common_taxonomy_tree_id_seq');

INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Leinster', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT root_id FROM ids), 0, 1);
UPDATE ids SET leinser_id = currval('common_taxonomy_tree_id_seq');

INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Munster', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT root_id FROM ids), 0, 1);
UPDATE ids SET munster_id = currval('common_taxonomy_tree_id_seq');

INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Ulster', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT root_id FROM ids), 0, 1);
UPDATE ids SET ulster_id = currval('common_taxonomy_tree_id_seq');

--
-- Counties
--
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Galway', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT connacht_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Leitrim', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT connacht_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Mayo', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT connacht_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Roscommon', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT connacht_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Sligo', E'',0,1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT connacht_id FROM ids), 0, 1);

INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Carlow', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Dublin', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Kildare', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Kilkenny', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Laois', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Longford', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Louth', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Meath', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Offaly', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Westmeath', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Wexford', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Wicklow', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT leinser_id FROM ids), 0, 1);

INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Clare', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT munster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Cork', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT munster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Kerry', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT munster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Limerick', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT munster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Tipperary', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT munster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Waterford', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT munster_id FROM ids), 0, 1);

INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Antrim', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT ulster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Armagh', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT ulster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Cavan', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT ulster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Derry', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT ulster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Donegal', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT ulster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Down', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT ulster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Fermanagh', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT ulster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Monaghan', E'', 0, 1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT ulster_id FROM ids), 0, 1);
INSERT INTO "common_taxonomy_label" ("title", "description", "priority", "publish") VALUES (E'Tyrone', E'',0,1);
INSERT INTO "common_taxonomy_tree" ("label_id", "parent", "priority", "publish") VALUES (currval('common_taxonomy_label_id_seq'), (SELECT ulster_id FROM ids), 0, 1);

COMMIT;
