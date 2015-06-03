---
--- Be careful. This deletes all delivery settings and you need to set them up again from scratch!
--- Default zones will be set up as well. If you want custom zones, do not run the INSERT part.
---

BEGIN;

CREATE TABLE ecommerce_delivery_carrier_rate (
	id serial PRIMARY KEY NOT NULL,
	carrier_id int REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE,
	weight_from numeric(12,5) DEFAULT 0,
	weight_to numeric(12,5) DEFAULT 0,
	price numeric(12,5)
);

CREATE INDEX ecommerce_delivery_carrier_rate_carrier_id_idx ON ecommerce_delivery_carrier_rate USING btree (carrier_id);
CREATE INDEX ecommerce_delivery_carrier_rate_weight_idx ON ecommerce_delivery_carrier_rate USING btree (weight_from, weight_to);

COMMIT;

BEGIN;

DROP TABLE ecommerce_delivery_carrier_zone_price;

ALTER TABLE ecommerce_delivery_carrier_zone
DROP COLUMN carrier_id;

ALTER TABLE ecommerce_delivery_carrier
DROP COLUMN limit_list_countries,
DROP COLUMN limit_list_products,
DROP COLUMN limit_list_product_types,
DROP COLUMN limit_order_value,
DROP COLUMN fixed_value,
DROP COLUMN fixed_percentage,
DROP COLUMN free_delivery_map;

INSERT INTO ecommerce_delivery_carrier_zone (name) VALUES ('Legacy');
UPDATE ecommerce_delivery_carrier_zone SET name = 'Legacy' WHERE id = 1;
DELETE FROM ecommerce_delivery_carrier_zone WHERE id > 1;
SELECT setval('ecommerce_delivery_carrier_zone_id_seq', (SELECT MAX(id) FROM ecommerce_delivery_carrier_zone));

ALTER TABLE ecommerce_delivery_carrier
ADD COLUMN zone_id integer,
ADD COLUMN order_value_from numeric(12,5),
ADD COLUMN order_value_to numeric(12,5);

UPDATE ecommerce_delivery_carrier SET zone_id = 1;

ALTER TABLE "ecommerce_delivery_carrier"
ADD FOREIGN KEY ("zone_id") REFERENCES "ecommerce_delivery_carrier_zone" ("id") ON DELETE CASCADE ON UPDATE CASCADE;

DROP TABLE ecommerce_delivery_carrier_zone_to_country;
CREATE TABLE ecommerce_delivery_carrier_zone_to_country (
	id serial PRIMARY KEY,
	country_id int NOT NULL REFERENCES international_country ON UPDATE CASCADE ON DELETE CASCADE,
	zone_id int NOT NULL REFERENCES ecommerce_delivery_carrier_zone ON UPDATE CASCADE ON DELETE CASCADE
);
ALTER TABLE ecommerce_delivery_carrier_zone_to_country ADD CONSTRAINT country_id_zone_id_key UNIQUE (country_id, zone_id);

INSERT INTO ecommerce_delivery_carrier_zone (id, name) VALUES
(2, 'UK'),
(3, 'Europe'),
(4, 'World');

INSERT INTO ecommerce_delivery_carrier_zone_to_country (zone_id, country_id) VALUES
(2, 222),  --- United Kingdom

(3, 2),    --- Albania
(3, 5),    --- Andorra
(3, 11),   --- Armenia
(3, 14),   --- Austria
(3, 15),   --- Azerbaijan
(3, 20),   --- Belarus
(3, 21),   --- Belgium
(3, 33),   --- Bulgaria
(3, 53),   --- Croatia
(3, 55),   --- Cyprus
(3, 56),   --- Czech Republic
(3, 57),   --- Denmark
(3, 67),   --- Estonia
(3, 72),   --- Finland
(3, 73),   --- France
(3, 80),   --- Georgia
(3, 81),   --- Germany
(3, 83),   --- Gibraltar
(3, 84),   --- Greece
(3, 85),   --- Greenland
(3, 97),   --- Hungary
(3, 98),   --- Iceland
(3, 103),  --- Ireland
(3, 105),  --- Italy
(3, 109),  --- Kazakhstan
(3, 115),  --- Kyrgyzstan
(3, 117),  --- Latvia
(3, 122),  --- Liechtenstein
(3, 123),  --- Lithuania
(3, 124),  --- Luxembourg
(3, 126),  --- Macedonia
(3, 74),   --- Madeira
(3, 132),  --- Malta
(3, 140),  --- Moldova
(3, 141),  --- Monaco
(3, 240),  --- Montenegro
(3, 150),  --- Netherlands
(3, 160),  --- Norway
(3, 170),  --- Poland
(3, 171),  --- Portugal
(3, 175),  --- Romania
(3, 176),  --- Russia
(3, 182),  --- San Marino
(3, 241),  --- Serbia
(3, 189),  --- Slovakia (Slovak Republic)
(3, 190),  --- Slovenia
(3, 195),  --- Spain
(3, 203),  --- Sweden
(3, 204),  --- Switzerland
(3, 207),  --- Tajikistan
(3, 215),  --- Turkey
(3, 216),  --- Turkmenistan
(3, 220),  --- Ukraine
(3, 226),  --- Uzbekistan
(3, 228),  --- Vatican City State (Holy See)


(4, 1),    --- Afghanistan
(4, 3),    --- Algeria
(4, 4),    --- American Samoa
(4, 6),    --- Angola
(4, 7),    --- Anguilla
(4, 8),    --- Antarctica
(4, 9),    --- Antigua and Barbuda
(4, 10),   --- Argentina
(4, 12),   --- Aruba
(4, 13),   --- Australia
(4, 16),   --- Bahamas
(4, 17),   --- Bahrain
(4, 18),   --- Bangladesh
(4, 19),   --- Barbados
(4, 22),   --- Belize
(4, 23),   --- Benin
(4, 24),   --- Bermuda
(4, 25),   --- Bhutan
(4, 26),   --- Bolivia
(4, 27),   --- Bosnia and Herzegowina
(4, 28),   --- Botswana
(4, 29),   --- Bouvet Island
(4, 30),   --- Brazil
(4, 31),   --- British Indian Ocean Territory
(4, 32),   --- Brunei Darussalam
(4, 34),   --- Burkina Faso
(4, 35),   --- Burundi
(4, 36),   --- Cambodia
(4, 37),   --- Cameroon
(4, 38),   --- Canada
(4, 39),   --- Cape Verde
(4, 40),   --- Cayman Islands
(4, 41),   --- Central African Republic
(4, 42),   --- Chad
(4, 43),   --- Chile
(4, 44),   --- China
(4, 45),   --- Christmas Island
(4, 46),   --- Cocos (Keeling) Islands
(4, 47),   --- Colombia
(4, 48),   --- Comoros
(4, 49),   --- Congo
(4, 50),   --- Cook Islands
(4, 51),   --- Costa Rica
(4, 52),   --- Cote D'Ivoire
(4, 54),   --- Cuba
(4, 58),   --- Djibouti
(4, 59),   --- Dominica
(4, 60),   --- Dominican Republic
(4, 61),   --- East Timor
(4, 62),   --- Ecuador
(4, 63),   --- Egypt
(4, 64),   --- El Salvador
(4, 65),   --- Equatorial Guinea
(4, 66),   --- Eritrea
(4, 68),   --- Ethiopia
(4, 69),   --- Falkland Islands (Malvinas)
(4, 70),   --- Faroe Islands
(4, 71),   --- Fiji
(4, 75),   --- French Guiana
(4, 76),   --- French Polynesia
(4, 77),   --- French Southern Territories
(4, 78),   --- Gabon
(4, 79),   --- Gambia
(4, 82),   --- Ghana
(4, 86),   --- Grenada
(4, 87),   --- Guadeloupe
(4, 88),   --- Guam
(4, 89),   --- Guatemala
(4, 90),   --- Guinea
(4, 91),   --- Guinea-bissau
(4, 92),   --- Guyana
(4, 93),   --- Haiti
(4, 94),   --- Heard and Mc Donald Islands
(4, 95),   --- Honduras
(4, 96),   --- Hong Kong
(4, 99),   --- India
(4, 100),  --- Indonesia
(4, 101),  --- Iran (Islamic Republic of)
(4, 102),  --- Iraq
(4, 104),  --- Israel
(4, 106),  --- Jamaica
(4, 107),  --- Japan
(4, 108),  --- Jordan
(4, 110),  --- Kenya
(4, 111),  --- Kiribati
(4, 112),  --- Korea, Democratic People's Republic of
(4, 113),  --- Korea, Republic of
(4, 114),  --- Kuwait
(4, 116),  --- Lao People's Democratic Republic
(4, 118),  --- Lebanon
(4, 119),  --- Lesotho
(4, 120),  --- Liberia
(4, 121),  --- Libyan Arab Jamahiriya
(4, 125),  --- Macau
(4, 127),  --- Madagascar
(4, 128),  --- Malawi
(4, 129),  --- Malaysia
(4, 130),  --- Maldives
(4, 131),  --- Mali
(4, 133),  --- Marshall Islands
(4, 134),  --- Martinique
(4, 135),  --- Mauritania
(4, 136),  --- Mauritius
(4, 137),  --- Mayotte
(4, 138),  --- Mexico
(4, 139),  --- Micronesia
(4, 142),  --- Mongolia
(4, 143),  --- Montserrat
(4, 144),  --- Morocco
(4, 145),  --- Mozambique
(4, 146),  --- Myanmar
(4, 147),  --- Namibia
(4, 148),  --- Nauru
(4, 149),  --- Nepal
(4, 151),  --- Netherlands Antilles
(4, 152),  --- New Caledonia
(4, 153),  --- New Zealand
(4, 154),  --- Nicaragua
(4, 155),  --- Niger
(4, 156),  --- Nigeria
(4, 157),  --- Niue
(4, 158),  --- Norfolk Island
(4, 159),  --- Northern Mariana Islands
(4, 161),  --- Oman
(4, 162),  --- Pakistan
(4, 163),  --- Palau
(4, 164),  --- Panama
(4, 165),  --- Papua New Guinea
(4, 166),  --- Paraguay
(4, 167),  --- Peru
(4, 168),  --- Philippines
(4, 169),  --- Pitcairn
(4, 172),  --- Puerto Rico
(4, 173),  --- Qatar
(4, 174),  --- Reunion
(4, 177),  --- Rwanda
(4, 178),  --- Saint Kitts and Nevis
(4, 179),  --- Saint Lucia
(4, 180),  --- Saint Vincent and the Grenadines
(4, 181),  --- Samoa
(4, 183),  --- Sao Tome and Principe
(4, 184),  --- Saudi Arabia
(4, 185),  --- Senegal
(4, 186),  --- Seychelles
(4, 187),  --- Sierra Leone
(4, 188),  --- Singapore
(4, 191),  --- Solomon Islands
(4, 192),  --- Somalia
(4, 193),  --- South Africa
(4, 194),  --- South Georgia and the South Sandwich Islands
(4, 196),  --- Sri Lanka
(4, 197),  --- St. Helena
(4, 198),  --- St. Pierre and Miquelon
(4, 199),  --- Sudan
(4, 200),  --- Suriname
(4, 201),  --- Svalbard and Jan Mayen Islands
(4, 202),  --- Swaziland
(4, 205),  --- Syrian Arab Republic
(4, 206),  --- Taiwan
(4, 208),  --- Tanzania, United Republic of
(4, 209),  --- Thailand
(4, 210),  --- Togo
(4, 211),  --- Tokelau
(4, 212),  --- Tonga
(4, 213),  --- Trinidad and Tobago
(4, 214),  --- Tunisia
(4, 217),  --- Turks and Caicos Islands
(4, 218),  --- Tuvalu
(4, 219),  --- Uganda
(4, 221),  --- United Arab Emirates
(4, 223),  --- United States
(4, 224),  --- United States Minor Outlying Islands
(4, 225),  --- Uruguay
(4, 227),  --- Vanuatu
(4, 229),  --- Venezuela
(4, 230),  --- Viet Nam
(4, 231),  --- Virgin Islands (British)
(4, 232),  --- Virgin Islands (U.S.)
(4, 233),  --- Wallis and Futuna Islands
(4, 234),  --- Western Sahara
(4, 235),  --- Yemen
(4, 236),  --- Yugoslavia
(4, 237),  --- Zaire
(4, 238),  --- Zambia
(4, 239);  --- Zimbabwe


COMMIT;
