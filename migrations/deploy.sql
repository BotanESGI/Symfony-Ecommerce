-- Adminer 4.8.1 PostgreSQL 16.6 dump
CREATE SEQUENCE address_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."address" (
                                    "id" integer DEFAULT nextval('address_id_seq') NOT NULL,
                                    "user_id" integer NOT NULL,
                                    "street" character varying(255) NOT NULL,
                                    "city" character varying(255) NOT NULL,
                                    "postal_code" character varying(10) NOT NULL,
                                    CONSTRAINT "address_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "idx_d4e6f81a76ed395" ON "public"."address" USING btree ("user_id");

TRUNCATE "address";
INSERT INTO "address" ("id", "user_id", "street", "city", "postal_code") VALUES
                                                                             (1,	1,	'Rue 0 Principale',	'Ville 0',	'75000'),
                                                                             (2,	2,	'Rue 1 Principale',	'Ville 1',	'75001'),
                                                                             (3,	3,	'Rue 2 Principale',	'Ville 2',	'75002'),
                                                                             (4,	4,	'Rue 3 Principale',	'Ville 3',	'75003'),
                                                                             (5,	5,	'Rue 4 Principale',	'Ville 4',	'75004'),
                                                                             (6,	6,	'Rue 5 Principale',	'Ville 5',	'75005'),
                                                                             (7,	7,	'Rue 6 Principale',	'Ville 6',	'75006'),
                                                                             (8,	8,	'Rue 7 Principale',	'Ville 7',	'75007'),
                                                                             (9,	9,	'Rue 8 Principale',	'Ville 8',	'75008'),
                                                                             (10,	10,	'Rue 9 Principale',	'Ville 9',	'75009');

CREATE SEQUENCE cart_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."cart" (
                                 "id" integer DEFAULT nextval('cart_id_seq') NOT NULL,
                                 "user_id" integer NOT NULL,
                                 CONSTRAINT "cart_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "idx_ba388b7a76ed395" ON "public"."cart" USING btree ("user_id");

TRUNCATE "cart";

CREATE SEQUENCE cart_item_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."cart_item" (
                                      "id" integer DEFAULT nextval('cart_item_id_seq') NOT NULL,
                                      "cart_id" integer NOT NULL,
                                      "product_id" integer NOT NULL,
                                      "quantity" integer NOT NULL,
                                      CONSTRAINT "cart_item_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "idx_f0fe25271ad5cdbf" ON "public"."cart_item" USING btree ("cart_id");

CREATE INDEX "idx_f0fe25274584665a" ON "public"."cart_item" USING btree ("product_id");

TRUNCATE "cart_item";

CREATE SEQUENCE category_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."category" (
                                     "id" integer DEFAULT nextval('category_id_seq') NOT NULL,
                                     "name" character varying(255) NOT NULL,
                                     "color" character varying(7),
                                     CONSTRAINT "category_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

TRUNCATE "category";
INSERT INTO "category" ("id", "name", "color") VALUES
                                                   (1,	'Électronique',	'#FF5733'),
                                                   (2,	'Appareils ménagers',	'#33FF57'),
                                                   (3,	'Livres',	'#3357FF'),
                                                   (4,	'Jouets',	'#FF33A1'),
                                                   (5,	'Mode',	'#FFC300'),
                                                   (6,	'Sports',	'#581845'),
                                                   (7,	'Jardin',	'#DAF7A6'),
                                                   (8,	'Musique',	'#C70039'),
                                                   (9,	'Bien-être',	'#900C3F'),
                                                   (10,	'Voyage',	'#2E86C1'),
                                                   (11,	'Éducation',	'#FF5733');

CREATE TABLE "public"."digital_product" (
                                            "id" integer NOT NULL,
                                            "download_link" character varying(255) NOT NULL,
                                            "filesize" integer,
                                            "filetype" character varying(50),
                                            CONSTRAINT "digital_product_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

TRUNCATE "digital_product";
INSERT INTO "digital_product" ("id", "download_link", "filesize", "filetype") VALUES
                                                                                  (20,	'https://ebook.com/ebook_cuisine.pdf',	4,	'.PDF'),
                                                                                  (21,	'https://jeu.com/downloads/jeu_video.exe',	2,	'.EXE'),
                                                                                  (22,	'https://logiciel.com/downloads/logiciel-montage-vidéo.zip',	3,	'.ZIP'),
                                                                                  (23,	'https://album.com/downloads/album-musique.mp3',	5,	'.MP3'),
                                                                                  (24,	'https://cours.com/downloads/cours-programmation.pdf',	5,	'.PDF'),
                                                                                  (25,	'https://application.com/downloads/application_meditation.apk',	1,	'.APK'),
                                                                                  (26,	'https://template.com/downloads/template_site.html',	4,	'.HTML'),
                                                                                  (27,	'https://formation.com/downloads/video_formation_marketing_digital.mp4',	4,	'.MP4');

CREATE TABLE "public"."doctrine_migration_versions" (
                                                        "version" character varying(191) NOT NULL,
                                                        "executed_at" timestamp(0),
                                                        "execution_time" integer,
                                                        CONSTRAINT "doctrine_migration_versions_pkey" PRIMARY KEY ("version")
) WITH (oids = false);

TRUNCATE "doctrine_migration_versions";
INSERT INTO "doctrine_migration_versions" ("version", "executed_at", "execution_time") VALUES
                                                                                           ('DoctrineMigrations\Version20250120224337',	'2025-02-06 23:19:35',	434),
                                                                                           ('DoctrineMigrations\Version20250121084325',	'2025-02-06 23:19:35',	3),
                                                                                           ('DoctrineMigrations\Version20250121110303',	'2025-02-06 23:19:35',	2),
                                                                                           ('DoctrineMigrations\Version20250121123416',	'2025-02-06 23:19:35',	10),
                                                                                           ('DoctrineMigrations\Version20250121141720',	'2025-02-06 23:19:35',	14),
                                                                                           ('DoctrineMigrations\Version20250121175646',	'2025-02-06 23:19:36',	1),
                                                                                           ('DoctrineMigrations\Version20250121204614',	'2025-02-06 23:19:36',	5),
                                                                                           ('DoctrineMigrations\Version20250123103857',	'2025-02-06 23:19:36',	2),
                                                                                           ('DoctrineMigrations\Version20250123150613',	'2025-02-06 23:19:36',	7),
                                                                                           ('DoctrineMigrations\Version20250123153026',	'2025-02-06 23:19:36',	9),
                                                                                           ('DoctrineMigrations\Version20250127001347',	'2025-02-06 23:19:36',	9),
                                                                                           ('DoctrineMigrations\Version20250128193008',	'2025-02-06 23:19:36',	5),
                                                                                           ('DoctrineMigrations\Version20250130141301',	'2025-02-06 23:19:36',	1),
                                                                                           ('DoctrineMigrations\Version20250130162923',	'2025-02-06 23:19:36',	7),
                                                                                           ('DoctrineMigrations\Version20250204161345',	'2025-02-06 23:19:36',	2);

CREATE SEQUENCE invoice_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."invoice" (
                                    "id" integer DEFAULT nextval('invoice_id_seq') NOT NULL,
                                    "user_id" integer NOT NULL,
                                    "total_amount" double precision NOT NULL,
                                    "pdf_path" character varying(255),
                                    "order_id" integer,
                                    CONSTRAINT "invoice_pkey" PRIMARY KEY ("id"),
                                    CONSTRAINT "uniq_906517448d9f6d38" UNIQUE ("order_id")
) WITH (oids = false);

CREATE INDEX "idx_90651744a76ed395" ON "public"."invoice" USING btree ("user_id");

TRUNCATE "invoice";
INSERT INTO "invoice" ("id", "user_id", "total_amount", "pdf_path", "order_id") VALUES
                                                                                    (1,	1,	291,	'/path/to/invoices/invoice_1.pdf',	1),
                                                                                    (2,	2,	803,	'/path/to/invoices/invoice_2.pdf',	2),
                                                                                    (3,	3,	501,	'/path/to/invoices/invoice_3.pdf',	3),
                                                                                    (4,	4,	291,	'/path/to/invoices/invoice_4.pdf',	4),
                                                                                    (5,	5,	293,	'/path/to/invoices/invoice_5.pdf',	5),
                                                                                    (6,	6,	455,	'/path/to/invoices/invoice_6.pdf',	6),
                                                                                    (7,	7,	673,	'/path/to/invoices/invoice_7.pdf',	7),
                                                                                    (8,	8,	511,	'/path/to/invoices/invoice_8.pdf',	8),
                                                                                    (9,	9,	360,	'/path/to/invoices/invoice_9.pdf',	9),
                                                                                    (10,	10,	183,	'/path/to/invoices/invoice_10.pdf',	10);

CREATE SEQUENCE order_item_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."order_item" (
                                       "id" integer DEFAULT nextval('order_item_id_seq') NOT NULL,
                                       "order_id" integer,
                                       "product_id" integer NOT NULL,
                                       "quantity" integer NOT NULL,
                                       CONSTRAINT "order_item_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "idx_52ea1f094584665a" ON "public"."order_item" USING btree ("product_id");

CREATE INDEX "idx_52ea1f098d9f6d38" ON "public"."order_item" USING btree ("order_id");

TRUNCATE "order_item";
INSERT INTO "order_item" ("id", "order_id", "product_id", "quantity") VALUES
                                                                          (1,	1,	14,	3),
                                                                          (2,	1,	18,	1),
                                                                          (3,	2,	16,	3),
                                                                          (4,	2,	19,	2),
                                                                          (5,	3,	10,	3),
                                                                          (6,	3,	17,	1),
                                                                          (7,	4,	1,	2),
                                                                          (8,	4,	17,	1),
                                                                          (9,	5,	11,	1),
                                                                          (10,	5,	25,	2),
                                                                          (11,	6,	15,	1),
                                                                          (12,	6,	26,	1),
                                                                          (13,	7,	8,	1),
                                                                          (14,	7,	9,	2),
                                                                          (15,	8,	20,	2),
                                                                          (16,	8,	24,	1),
                                                                          (17,	9,	17,	3),
                                                                          (18,	9,	18,	3),
                                                                          (19,	10,	15,	3),
                                                                          (20,	10,	19,	3);

CREATE SEQUENCE orders_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."orders" (
                                   "id" integer DEFAULT nextval('orders_id_seq') NOT NULL,
                                   "user_id" integer NOT NULL,
                                   "total" numeric(10,2) NOT NULL,
                                   "date" timestamp(0) NOT NULL,
                                   "invoice_id" integer,
                                   CONSTRAINT "orders_pkey" PRIMARY KEY ("id"),
                                   CONSTRAINT "uniq_e52ffdee2989f1fd" UNIQUE ("invoice_id")
) WITH (oids = false);

CREATE INDEX "idx_e52ffdeea76ed395" ON "public"."orders" USING btree ("user_id");

COMMENT ON COLUMN "public"."orders"."date" IS '(DC2Type:datetime_immutable)';

TRUNCATE "orders";
INSERT INTO "orders" ("id", "user_id", "total", "date", "invoice_id") VALUES
                                                                          (1,	1,	291.00,	'2025-02-06 23:19:37',	1),
                                                                          (2,	2,	803.00,	'2025-02-06 23:19:38',	2),
                                                                          (3,	3,	501.00,	'2025-02-06 23:19:38',	3),
                                                                          (4,	4,	291.00,	'2025-02-06 23:19:38',	4),
                                                                          (5,	5,	293.00,	'2025-02-06 23:19:38',	5),
                                                                          (6,	6,	455.00,	'2025-02-06 23:19:38',	6),
                                                                          (7,	7,	673.00,	'2025-02-06 23:19:38',	7),
                                                                          (8,	8,	511.00,	'2025-02-06 23:19:38',	8),
                                                                          (9,	9,	360.00,	'2025-02-06 23:19:38',	9),
                                                                          (10,	10,	183.00,	'2025-02-06 23:19:38',	10);

CREATE TABLE "public"."physical_product" (
                                             "id" integer NOT NULL,
                                             "characteristics" json,
                                             CONSTRAINT "physical_product_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

TRUNCATE "physical_product";
INSERT INTO "physical_product" ("id", "characteristics") VALUES
                                                             (1,	'{"poids":"0.3 kg","dimensions":"20x15x8 cm","autonomie_batterie":"20 heures","annulation_de_bruit":"Oui","temps_de_charge":"2 heures","port\u00e9e":"10 m\u00e8tres","couleur":"Noir"}'),
                                                             (2,	'{"poids":"2 kg","dimensions":"35x24x2 cm","autonomie_batterie":"10 heures","processeur":"Intel i7","ram":"16 Go","stockage":"512 Go SSD","couleur":"Argent"}'),
                                                             (3,	'{"poids":"0.2 kg","dimensions":"15x7x0.8 cm","autonomie_batterie":"24 heures","cam\u00e9ra":"48 MP","stockage":"128 Go","couleur":"Bleu"}'),
                                                             (4,	'{"poids":"0.1 kg","dimensions":"4x4x1 cm","autonomie_batterie":"7 jours","r\u00e9sistance_\u00e0_l_eau":"5 ATM","suivi_de_la_sant\u00e9":"Fr\u00e9quence cardiaque, sommeil","couleur":"Noir"}'),
                                                             (5,	'{"poids":"0.5 kg","dimensions":"25x20x2 cm","nombre_de_pages":"200 pages","type_de_couverture":"Broch\u00e9","genre":"Cuisine","couleur":"Multicolore"}'),
                                                             (6,	'{"poids":"0.4 kg","dimensions":"20x15x2 cm","nombre_de_pages":"300 pages","type_de_couverture":"Reli\u00e9","genre":"Aventure","couleur":"Multicolore"}'),
                                                             (7,	'{"poids":"0.3 kg","dimensions":"20x15x2 cm","nombre_de_pages":"250 pages","type_de_couverture":"Broch\u00e9","genre":"Science-fiction","couleur":"Multicolore"}'),
                                                             (8,	'{"poids":"1 kg","dimensions":"30x20x5 cm","mat\u00e9riau":"Plastique","nombre_de_pieces":"150 pi\u00e8ces","age_recommande":"\u00c0 partir de 4 ans","color":"Multicolore"}'),
                                                             (9,	'{"poids":"0.3 kg","dimensions":"25x10x5 cm","mat\u00e9riau":"Tissu","\u00e2ge_recommand\u00e9":"\u00c0 partir de 3 ans","color":"Rose"}'),
                                                             (10,	'{"poids":"1 kg","dimensions":"50x40x5 cm","mat\u00e9riau":"Carton","nombre_de_pieces":"1000 pi\u00e8ces","niveau_de_difficult\u00e9":"Difficile","color":"Multicolore"}'),
                                                             (11,	'{"poids":"0.2 kg","dimensions":"30x20x1 cm","mat\u00e9riau":"Coton","tailles_disponibles":"S, M, L, XL","color":"Blanc"}'),
                                                             (12,	'{"poids":"0.5 kg","dimensions":"40x30x2 cm","mat\u00e9riau":"Denim","tailles_disponibles":"S, M, L, XL","color":"Bleu"}'),
                                                             (13,	'{"poids":"1 kg","dimensions":"50x40x2 cm","mat\u00e9riau":"Cuir","tailles_disponibles":"S, M, L, XL","color":"Noir"}'),
                                                             (14,	'{"poids":"0.3 kg","dimensions":"68x27x5 cm","mat\u00e9riau":"Graphite","niveau_de_joueur":"Interm\u00e9diaire","color":"Rouge"}'),
                                                             (15,	'{"poids":"0.4 kg","dimensions":"22 cm de diam\u00e8tre","mat\u00e9riau":"Cuir synth\u00e9tique","niveau_de_jeu":"Amateur","color":"Noir et blanc"}'),
                                                             (16,	'{"poids":"1 kg","dimensions":"180x60x0.5 cm","mat\u00e9riau":"PVC","\u00e9paisseur":"0.5 cm","color":"Violet"}'),
                                                             (17,	'{"poids":"1.5 kg","dimensions":"40x15x15 cm","mat\u00e9riau":"Plastique","puissance":"600 W","color":"Noir"}'),
                                                             (18,	'{"poids":"15 kg","dimensions":"150x90x75 cm","mat\u00e9riau":"Bois","capacite":"6 personnes","color":"Brun"}'),
                                                             (19,	'{"poids":"12 kg","dimensions":"175x60x100 cm","mat\u00e9riau":"Aluminium","vitesse":"21 vitesses","color":"Vert"}');

CREATE SEQUENCE product_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."product" (
                                    "id" integer DEFAULT nextval('product_id_seq') NOT NULL,
                                    "name" character varying(255) NOT NULL,
                                    "description" text NOT NULL,
                                    "price" double precision NOT NULL,
                                    "image" character varying(255),
                                    "product_type" character varying(255) NOT NULL,
                                    "default_category_id" integer NOT NULL,
                                    "created_at" timestamp(0),
                                    CONSTRAINT "product_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "idx_d34a04adc6b58e54" ON "public"."product" USING btree ("default_category_id");

TRUNCATE "product";
INSERT INTO "product" ("id", "name", "description", "price", "image", "product_type", "default_category_id", "created_at") VALUES
                                                                                                                               (1,	'Casque audio',	'Casque audio sans fil avec réduction de bruit.',	150,	'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fd/Marshall_Monitor_Headphone_-_5._details_%282013-05-08_08.38.08_by_Dave_Kobrehel%29.jpg/1920px-Marshall_Monitor_Headphone_-_5._details_%282013-05-08_08.38.08_by_Dave_Kobrehel%29.jpg',	'physical',	1,	'2025-02-06 23:19:37'),
                                                                                                                               (2,	'Ordinateur portable',	'Ordinateur portable puissant pour les professionnels.',	1200,	'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Samsung_QX-511_%282%29.JPG/1920px-Samsung_QX-511_%282%29.JPG',	'physical',	1,	'2025-02-06 23:19:37'),
                                                                                                                               (3,	'Smartphone',	'Smartphone dernière génération avec un écran AMOLED.',	800,	'https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Samsung_Galaxy_S10%2B.jpg/1024px-Samsung_Galaxy_S10%2B.jpg',	'physical',	1,	'2025-02-06 23:19:37'),
                                                                                                                               (4,	'Montre connectée',	'Montre connectée avec suivi de la santé.',	200,	'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Smartwatch-828786.jpg/1920px-Smartwatch-828786.jpg',	'physical',	1,	'2025-02-06 23:19:37'),
                                                                                                                               (5,	'Livre de cuisine',	'Recettes délicieuses pour toute la famille.',	25,	'https://upload.wikimedia.org/wikipedia/commons/f/f8/COCINAR_CON_EL_LIBRO.png',	'physical',	3,	'2025-02-06 23:19:37'),
                                                                                                                               (6,	'Roman d''aventure',	'Un voyage épique à travers des mondes inconnus.',	15,	'https://upload.wikimedia.org/wikipedia/commons/8/86/Verne_Tour_du_Monde.jpg',	'physical',	3,	'2025-02-06 23:19:37'),
                                                                                                                               (7,	'Livre de science-fiction',	'Exploration des futurs possibles et des technologies avancées.',	20,	'https://marketplace.canva.com/EADzX_z4AWg/1/0/1003w/canva-fonc%C3%A9-bleu-science-fiction-livre-couverture-smN23L-N62g.jpg',	'physical',	3,	'2025-02-06 23:19:37'),
                                                                                                                               (8,	'Jeu de construction',	'Jeu de construction créatif pour enfants.',	50,	'https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Constri.JPG/1920px-Constri.JPG',	'physical',	4,	'2025-02-06 23:19:37'),
                                                                                                                               (9,	'Poupée',	'Poupée en tissu douce pour enfants.',	30,	'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Pierotti_wax_doll_from_Frederic_Aldis%2C_London%2C_01%2C_sitting_doll%2C_vested.jpg/1024px-Pierotti_wax_doll_from_Frederic_Aldis%2C_London%2C_01%2C_sitting_doll%2C_vested.jpg',	'physical',	4,	'2025-02-06 23:19:37'),
                                                                                                                               (10,	'Puzzle 1000 pièces',	'Un puzzle de 1000 pièces pour les amateurs de défis.',	25,	'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Museum_Ravensburger_14.jpg/1280px-Museum_Ravensburger_14.jpg',	'physical',	4,	'2025-02-06 23:19:37'),
                                                                                                                               (11,	'T-shirt',	'T-shirt en coton doux, disponible en plusieurs tailles.',	20,	'https://upload.wikimedia.org/wikipedia/commons/2/24/Blue_Tshirt.jpg',	'physical',	5,	'2025-02-06 23:19:37'),
                                                                                                                               (12,	'Jean',	'Jean confortable avec une coupe moderne.',	50,	'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Levi%27s_501_raw_jeans.jpg/800px-Levi%27s_501_raw_jeans.jpg',	'physical',	5,	'2025-02-06 23:19:37'),
                                                                                                                               (13,	'Veste en cuir',	'Veste en cuir élégante pour hommes et femmes.',	150,	'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/New-613-Schott_Perfecto-cut-out-and-shaded.jpg/640px-New-613-Schott_Perfecto-cut-out-and-shaded.jpg',	'physical',	5,	'2025-02-06 23:19:37'),
                                                                                                                               (14,	'Raquette de tennis',	'Raquette légère en graphite.',	100,	'https://upload.wikimedia.org/wikipedia/commons/thumb/5/59/Tennis_racket_and_ball.JPG/255px-Tennis_racket_and_ball.JPG',	'physical',	6,	'2025-02-06 23:19:37'),
                                                                                                                               (15,	'Ballon de football',	'Ballon de football de haute qualité.',	30,	'https://upload.wikimedia.org/wikipedia/commons/1/1d/Football_Pallo_valmiina-cropped.jpg',	'physical',	6,	'2025-02-06 23:19:37'),
                                                                                                                               (16,	'Tapis de yoga',	'Tapis de yoga antidérapant pour le confort.',	40,	'https://upload.wikimedia.org/wikipedia/commons/0/06/Ardha-Nav%C4%81sana.JPG',	'physical',	6,	'2025-02-06 23:19:37'),
                                                                                                                               (17,	'Mixeur',	'Mixeur puissant pour préparer des smoothies.',	70,	'https://upload.wikimedia.org/wikipedia/commons/0/06/ElectricBlender.jpg',	'physical',	2,	'2025-02-06 23:19:37'),
                                                                                                                               (18,	'Table de jardin',	'Table en bois pour le jardin.',	300,	'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Table_pliante.jpg/1920px-Table_pliante.jpg',	'physical',	7,	'2025-02-06 23:19:37'),
                                                                                                                               (19,	'Vélo de montagne',	'Vélo robuste pour les terrains difficiles.',	500,	'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/V%C3%A9lo_enfant_%C3%A0_courroie_et_cadre_aluminium.jpg/360px-V%C3%A9lo_enfant_%C3%A0_courroie_et_cadre_aluminium.jpg',	'physical',	9,	'2025-02-06 23:19:37'),
                                                                                                                               (20,	'E-book de cuisine',	'Recettes de cuisine en format numérique.',	10,	'https://marketplace.canva.com/EADzX1V0eZo/1/0/1003w/canva-rouge-et-blanc-livre-de-cuisine-livre-couverture--84IPJPPzYE.jpg',	'digital',	3,	'2025-02-06 23:19:37'),
                                                                                                                               (21,	'Jeu vidéo',	'Jeu vidéo d''aventure sur PC.',	40,	'https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Vg_history_icon_alt.svg/1280px-Vg_history_icon_alt.svg.png',	'digital',	1,	'2025-02-06 23:19:37'),
                                                                                                                               (22,	'Logiciel de montage vidéo',	'Logiciel puissant pour le montage vidéo.',	60,	'https://static-cse.canva.com/blob/1145314/Montagevideo.jpg',	'digital',	1,	'2025-02-06 23:19:37'),
                                                                                                                               (23,	'Album de musique numérique',	'Téléchargez votre album préféré en format MP3.',	15,	'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Audio_a.svg/2560px-Audio_a.svg.png',	'digital',	8,	'2025-02-06 23:19:37'),
                                                                                                                               (24,	'Cours en ligne de programmation',	'Apprenez la programmation avec ce cours en ligne.',	50,	'https://upload.wikimedia.org/wikipedia/commons/a/a4/JavaScript_code.png',	'digital',	11,	'2025-02-06 23:19:37'),
                                                                                                                               (25,	'Application de méditation',	'Application numérique pour la méditation et la pleine conscience.',	25,	'https://www.conseil-d-assureur.fr/wp-content/uploads/2022/02/app-meditation-petit-bambou.jpg',	'digital',	9,	'2025-02-06 23:19:37'),
                                                                                                                               (26,	'Template de site web',	'Template professionnel pour créer un site web facilement.',	30,	'https://marketplace.canva.com/EAE6WTyrSQ0/2/0/1600w/canva-light-beige-sleek-and-simple-blogger-personal-website--7Q4-7tyJj4.jpg',	'digital',	1,	'2025-02-06 23:19:37'),
                                                                                                                               (27,	'Video formation en marketing digital',	'Apprenez les stratégies de marketing digital.',	75,	'https://media.licdn.com/dms/image/v2/C4D12AQFwOmSy4XaXbg/article-cover_image-shrink_600_2000/article-cover_image-shrink_600_2000/0/1621165601697?e=2147483647&v=beta&t=3EdRZZOvjZ3vAoEkEZPl9vwjXHPU88lQD8LEVrbX2mY',	'digital',	11,	'2025-02-06 23:19:37');

CREATE TABLE "public"."product_category" (
                                             "product_id" integer NOT NULL,
                                             "category_id" integer NOT NULL,
                                             CONSTRAINT "product_category_pkey" PRIMARY KEY ("product_id", "category_id")
) WITH (oids = false);

CREATE INDEX "idx_cdfc735612469de2" ON "public"."product_category" USING btree ("category_id");

CREATE INDEX "idx_cdfc73564584665a" ON "public"."product_category" USING btree ("product_id");

TRUNCATE "product_category";
INSERT INTO "product_category" ("product_id", "category_id") VALUES
                                                                 (1,	1),
                                                                 (1,	8),
                                                                 (2,	1),
                                                                 (2,	11),
                                                                 (3,	1),
                                                                 (4,	1),
                                                                 (5,	3),
                                                                 (5,	11),
                                                                 (6,	3),
                                                                 (6,	11),
                                                                 (7,	3),
                                                                 (7,	11),
                                                                 (8,	4),
                                                                 (8,	11),
                                                                 (9,	4),
                                                                 (10,	4),
                                                                 (10,	11),
                                                                 (11,	5),
                                                                 (12,	5),
                                                                 (13,	5),
                                                                 (14,	6),
                                                                 (15,	6),
                                                                 (16,	6),
                                                                 (16,	9),
                                                                 (17,	2),
                                                                 (18,	7),
                                                                 (19,	6),
                                                                 (19,	9),
                                                                 (20,	3),
                                                                 (20,	11),
                                                                 (21,	1),
                                                                 (22,	1),
                                                                 (23,	8),
                                                                 (24,	11),
                                                                 (25,	9),
                                                                 (26,	1),
                                                                 (27,	11);

CREATE TABLE "public"."product_tag" (
                                        "product_id" integer NOT NULL,
                                        "tag_id" integer NOT NULL,
                                        CONSTRAINT "product_tag_pkey" PRIMARY KEY ("product_id", "tag_id")
) WITH (oids = false);

CREATE INDEX "idx_e3a6e39c4584665a" ON "public"."product_tag" USING btree ("product_id");

CREATE INDEX "idx_e3a6e39cbad26311" ON "public"."product_tag" USING btree ("tag_id");

TRUNCATE "product_tag";
INSERT INTO "product_tag" ("product_id", "tag_id") VALUES
                                                       (1,	1),
                                                       (1,	2),
                                                       (1,	3),
                                                       (1,	4),
                                                       (1,	5),
                                                       (1,	6),
                                                       (2,	1),
                                                       (2,	2),
                                                       (2,	4),
                                                       (3,	1),
                                                       (3,	2),
                                                       (3,	3),
                                                       (3,	5),
                                                       (3,	6),
                                                       (4,	1),
                                                       (4,	3),
                                                       (5,	1),
                                                       (5,	6),
                                                       (6,	2),
                                                       (6,	3),
                                                       (6,	5),
                                                       (7,	4),
                                                       (7,	5),
                                                       (8,	1),
                                                       (8,	2),
                                                       (8,	3),
                                                       (8,	4),
                                                       (8,	5),
                                                       (9,	1),
                                                       (9,	5),
                                                       (9,	6),
                                                       (10,	1),
                                                       (10,	4),
                                                       (10,	5),
                                                       (10,	6),
                                                       (11,	1),
                                                       (11,	3),
                                                       (11,	4),
                                                       (11,	5),
                                                       (11,	6),
                                                       (12,	1),
                                                       (12,	2),
                                                       (12,	3),
                                                       (12,	4),
                                                       (12,	5),
                                                       (12,	6),
                                                       (13,	1),
                                                       (13,	2),
                                                       (13,	3),
                                                       (13,	4),
                                                       (13,	5),
                                                       (13,	6),
                                                       (14,	2),
                                                       (14,	4),
                                                       (14,	5),
                                                       (14,	6),
                                                       (15,	1),
                                                       (15,	5),
                                                       (15,	6),
                                                       (16,	5),
                                                       (17,	6),
                                                       (18,	2),
                                                       (18,	5),
                                                       (19,	5),
                                                       (20,	6),
                                                       (21,	1),
                                                       (21,	2),
                                                       (21,	4),
                                                       (21,	5),
                                                       (22,	1),
                                                       (22,	2),
                                                       (22,	4),
                                                       (22,	5),
                                                       (22,	6),
                                                       (23,	1),
                                                       (23,	2),
                                                       (23,	3),
                                                       (23,	4),
                                                       (23,	5),
                                                       (23,	6),
                                                       (24,	2),
                                                       (25,	2),
                                                       (25,	5),
                                                       (26,	5),
                                                       (26,	6),
                                                       (27,	3),
                                                       (27,	5),
                                                       (27,	6);

CREATE SEQUENCE review_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."review" (
                                   "id" integer DEFAULT nextval('review_id_seq') NOT NULL,
                                   "user_id" integer NOT NULL,
                                   "product_id" integer NOT NULL,
                                   "content" text NOT NULL,
                                   "rating" integer NOT NULL,
                                   "status" character varying(255) NOT NULL,
                                   "date_publication" timestamp(0) NOT NULL,
                                   CONSTRAINT "review_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "idx_794381c64584665a" ON "public"."review" USING btree ("product_id");

CREATE INDEX "idx_794381c6a76ed395" ON "public"."review" USING btree ("user_id");

TRUNCATE "review";
INSERT INTO "review" ("id", "user_id", "product_id", "content", "rating", "status", "date_publication") VALUES
                                                                                                            (1,	1,	1,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (2,	2,	1,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (3,	3,	1,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (4,	4,	1,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (5,	5,	1,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (6,	6,	1,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (7,	7,	1,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (8,	8,	1,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (9,	9,	1,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (10,	10,	1,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (11,	1,	2,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (12,	2,	2,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (13,	3,	2,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (14,	4,	2,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (15,	5,	2,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (16,	6,	2,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (17,	7,	2,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (18,	8,	2,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (19,	9,	2,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (20,	10,	2,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (21,	1,	3,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (22,	2,	3,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (23,	3,	3,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (24,	4,	3,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (25,	5,	3,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (26,	6,	3,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (27,	7,	3,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (28,	8,	3,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (29,	9,	3,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (30,	10,	3,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (31,	1,	4,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (32,	2,	4,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (33,	3,	4,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (34,	4,	4,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (35,	5,	4,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (36,	6,	4,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (37,	7,	4,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (38,	8,	4,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (39,	9,	4,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (40,	10,	4,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (41,	1,	5,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (42,	2,	5,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (43,	3,	5,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (44,	4,	5,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (45,	5,	5,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (46,	6,	5,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (47,	7,	5,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (48,	8,	5,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (49,	9,	5,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (50,	10,	5,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (51,	1,	6,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (52,	2,	6,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (53,	3,	6,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (54,	4,	6,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (55,	5,	6,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (56,	6,	6,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (57,	7,	6,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (58,	8,	6,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (59,	9,	6,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (60,	10,	6,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (61,	1,	7,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (62,	2,	7,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (63,	3,	7,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (64,	4,	7,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (65,	5,	7,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (66,	6,	7,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (67,	7,	7,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (68,	8,	7,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (69,	9,	7,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (70,	10,	7,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (71,	1,	8,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (72,	2,	8,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (73,	3,	8,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (74,	4,	8,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (75,	5,	8,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (76,	6,	8,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (77,	7,	8,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (78,	8,	8,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (79,	9,	8,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (80,	10,	8,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (81,	1,	9,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (82,	2,	9,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (83,	3,	9,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (84,	4,	9,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (85,	5,	9,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (86,	6,	9,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (87,	7,	9,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (88,	8,	9,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (89,	9,	9,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (90,	10,	9,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (91,	1,	10,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (92,	2,	10,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (93,	3,	10,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (94,	4,	10,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (95,	5,	10,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (96,	6,	10,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (97,	7,	10,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (98,	8,	10,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (99,	9,	10,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (100,	10,	10,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (101,	1,	11,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (102,	2,	11,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (103,	3,	11,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (104,	4,	11,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (105,	5,	11,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (106,	6,	11,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (107,	7,	11,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (108,	8,	11,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (109,	9,	11,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (110,	10,	11,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (111,	1,	12,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (112,	2,	12,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (113,	3,	12,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (114,	4,	12,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (115,	5,	12,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (116,	6,	12,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (117,	7,	12,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (118,	8,	12,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (119,	9,	12,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (120,	10,	12,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (121,	1,	13,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (122,	2,	13,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (123,	3,	13,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (124,	4,	13,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (125,	5,	13,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (126,	6,	13,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (127,	7,	13,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (128,	8,	13,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (129,	9,	13,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (130,	10,	13,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (131,	1,	14,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (132,	2,	14,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (133,	3,	14,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (134,	4,	14,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (135,	5,	14,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (136,	6,	14,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (137,	7,	14,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (138,	8,	14,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (139,	9,	14,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (140,	10,	14,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (141,	1,	15,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (142,	2,	15,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (143,	3,	15,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (144,	4,	15,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (145,	5,	15,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (146,	6,	15,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (147,	7,	15,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (148,	8,	15,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (149,	9,	15,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (150,	10,	15,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (151,	1,	16,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (152,	2,	16,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (153,	3,	16,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (154,	4,	16,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (155,	5,	16,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (156,	6,	16,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (157,	7,	16,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (158,	8,	16,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (159,	9,	16,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (160,	10,	16,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (161,	1,	17,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (162,	2,	17,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (163,	3,	17,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (164,	4,	17,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (165,	5,	17,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (166,	6,	17,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (167,	7,	17,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (168,	8,	17,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (169,	9,	17,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (170,	10,	17,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (171,	1,	18,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (172,	2,	18,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (173,	3,	18,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (174,	4,	18,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (175,	5,	18,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (176,	6,	18,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (177,	7,	18,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (178,	8,	18,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (179,	9,	18,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (180,	10,	18,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (181,	1,	19,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (182,	2,	19,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (183,	3,	19,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (184,	4,	19,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (185,	5,	19,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (186,	6,	19,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (187,	7,	19,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (188,	8,	19,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (189,	9,	19,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (190,	10,	19,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (191,	1,	20,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (192,	2,	20,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (193,	3,	20,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (194,	4,	20,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (195,	5,	20,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (196,	6,	20,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (197,	7,	20,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (198,	8,	20,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (199,	9,	20,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (200,	10,	20,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (201,	1,	21,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (202,	2,	21,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (203,	3,	21,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (204,	4,	21,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (205,	5,	21,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (206,	6,	21,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (207,	7,	21,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (208,	8,	21,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (209,	9,	21,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (210,	10,	21,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (211,	1,	22,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (212,	2,	22,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (213,	3,	22,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (214,	4,	22,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (215,	5,	22,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (216,	6,	22,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (217,	7,	22,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (218,	8,	22,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (219,	9,	22,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (220,	10,	22,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (221,	1,	23,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (222,	2,	23,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (223,	3,	23,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (224,	4,	23,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (225,	5,	23,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (226,	6,	23,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (227,	7,	23,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (228,	8,	23,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (229,	9,	23,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (230,	10,	23,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (231,	1,	24,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (232,	2,	24,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (233,	3,	24,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (234,	4,	24,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (235,	5,	24,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (236,	6,	24,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (237,	7,	24,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (238,	8,	24,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (239,	9,	24,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (240,	10,	24,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (241,	1,	25,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (242,	2,	25,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (243,	3,	25,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (244,	4,	25,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (245,	5,	25,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (246,	6,	25,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (247,	7,	25,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (248,	8,	25,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (249,	9,	25,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (250,	10,	25,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (251,	1,	26,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (252,	2,	26,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (253,	3,	26,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (254,	4,	26,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (255,	5,	26,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (256,	6,	26,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (257,	7,	26,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (258,	8,	26,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (259,	9,	26,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (260,	10,	26,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (261,	1,	27,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (262,	2,	27,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (263,	3,	27,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (264,	4,	27,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (265,	5,	27,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (266,	6,	27,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (267,	7,	27,	'Produit excellent, je recommande !',	4,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (268,	8,	27,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (269,	9,	27,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37'),
                                                                                                            (270,	10,	27,	'Produit excellent, je recommande !',	5,	'VALIDATED',	'2025-02-06 23:19:37');

CREATE SEQUENCE tag_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."tag" (
                                "id" integer DEFAULT nextval('tag_id_seq') NOT NULL,
                                "name" character varying(255) NOT NULL,
                                "color" character varying(7),
                                CONSTRAINT "tag_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

TRUNCATE "tag";
INSERT INTO "tag" ("id", "name", "color") VALUES
                                              (1,	'SOLDE',	'#FF5733'),
                                              (2,	'BLACK FRIDAY',	'#FF5733'),
                                              (3,	'OPÉRATION SPÉCIALE',	'#33FF57'),
                                              (4,	'NOUVEAUTÉ',	'#E67E22'),
                                              (5,	'PROMO',	'#3357FF'),
                                              (6,	'EXCLUSIVITÉ',	'#33FF57');

CREATE SEQUENCE user_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."user" (
                                 "id" integer DEFAULT nextval('user_id_seq') NOT NULL,
                                 "email" character varying(255) NOT NULL,
                                 "password" character varying(255) NOT NULL,
                                 "roles" json NOT NULL,
                                 "name" character varying(255) NOT NULL,
                                 "lastname" character varying(255) NOT NULL,
                                 "reset_token" character varying(255),
                                 "confirmation_token" character varying(255),
                                 "is_verified" boolean NOT NULL,
                                 CONSTRAINT "uniq_8d93d649e7927c74" UNIQUE ("email"),
                                 CONSTRAINT "user_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

TRUNCATE "user";
INSERT INTO "user" ("id", "email", "password", "roles", "name", "lastname", "reset_token", "confirmation_token", "is_verified") VALUES
                                                                                                                                    (1,	'utilisateur0@exemple.com',	'$2y$10$vX7cVDwDqgutm9iqPQtHQ.aVo4RCc3hDcR09.YRF5rt9O78js4yi6',	'["ROLE_USER"]',	'Botan',	'ESGI',	NULL,	NULL,	't'),
                                                                                                                                    (2,	'utilisateur1@exemple.com',	'$2y$10$trl2sCGXQdZpG7MrqdDi2uQQ88X2TRpZe0LkE1Kzsp5EgH4tCLBdi',	'["ROLE_USER"]',	'Jane',	'Doe',	NULL,	NULL,	't'),
                                                                                                                                    (3,	'utilisateur2@exemple.com',	'$2y$10$wkVnyparTNZGTUMzx09iNOJJgo9SF9cT2GWRDpDSkY1NQY37r0FFW',	'["ROLE_USER"]',	'Alice',	'Durand',	NULL,	NULL,	't'),
                                                                                                                                    (4,	'utilisateur3@exemple.com',	'$2y$10$NIJ2C4aRXSRDrb0S1SoEjOXaV3wNT2OG0naxvN/vrrN8Ebm.g8PI.',	'["ROLE_USER"]',	'Bob',	'Martin',	NULL,	NULL,	't'),
                                                                                                                                    (5,	'utilisateur4@exemple.com',	'$2y$10$.4bVC7GH8zdYbyikAnX4OebEg7fqZe5SRIfSLz4CwnBNTGgo0OsPa',	'["ROLE_USER"]',	'Émilie',	'Dupont',	NULL,	NULL,	't'),
                                                                                                                                    (6,	'utilisateur5@exemple.com',	'$2y$10$JvgWOwQnS7IoNtzLAoRfleg9IELH/Pgm3mOTqjMNE3cPTmf5xVMWq',	'["ROLE_USER"]',	'Michel',	'Bernard',	NULL,	NULL,	't'),
                                                                                                                                    (7,	'utilisateur6@exemple.com',	'$2y$10$D9hzA5HIugMax.BPa6pZOegYG4rDVC2DxMP2syZ7PGgKFqTV8h5H.',	'["ROLE_USER"]',	'Sarah',	'Lemoine',	NULL,	NULL,	't'),
                                                                                                                                    (8,	'utilisateur7@exemple.com',	'$2y$10$OidRZobHbufBzkg6rRivTuGGLkrOO7M8mAAOa72jvLLe8tWAtxWRa',	'["ROLE_USER"]',	'David',	'Giraud',	NULL,	NULL,	't'),
                                                                                                                                    (9,	'utilisateur8@exemple.com',	'$2y$10$ENQWopt9AHzAH2KJ5l4u1ej.jwxVIRPz4weCl4SAJRfYeF2SgU4b.',	'["ROLE_USER"]',	'Laura',	'Roux',	NULL,	NULL,	't'),
                                                                                                                                    (10,	'utilisateur9@exemple.com',	'$2y$10$8TAXsnJCXDYGTMoC0JD.kOLzKPImQvzuSisKseGVL0M5XluPiksrC',	'["ROLE_USER"]',	'Christophe',	'Moreau',	NULL,	NULL,	't'),
                                                                                                                                    (11,	'admin@exemple.com',	'$2y$10$INyElqmY6VMUNveLUoZ7eOfsdWxVhHJb1pOHwK3jNjSD6L52tVTl6',	'["ROLE_ADMIN"]',	'Administrateur',	'Principal',	NULL,	NULL,	't'),
                                                                                                                                    (12,	'banni@exemple.com',	'$2y$10$o3Fr6Sw.9LpoB27cAzsc/Oy5VUsIOSfZqw6SN4z48HUj/wRIDjKEK',	'["ROLE_BANNED"]',	'Utilisateur',	'Banni',	NULL,	NULL,	't');

ALTER TABLE ONLY "public"."address" ADD CONSTRAINT "fk_d4e6f81a76ed395" FOREIGN KEY (user_id) REFERENCES "user"(id) NOT DEFERRABLE;

ALTER TABLE ONLY "public"."cart" ADD CONSTRAINT "fk_ba388b7a76ed395" FOREIGN KEY (user_id) REFERENCES "user"(id) NOT DEFERRABLE;

ALTER TABLE ONLY "public"."cart_item" ADD CONSTRAINT "fk_f0fe25271ad5cdbf" FOREIGN KEY (cart_id) REFERENCES cart(id) NOT DEFERRABLE;
ALTER TABLE ONLY "public"."cart_item" ADD CONSTRAINT "fk_f0fe25274584665a" FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."digital_product" ADD CONSTRAINT "fk_f3c8dfa7bf396750" FOREIGN KEY (id) REFERENCES product(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."invoice" ADD CONSTRAINT "fk_906517448d9f6d38" FOREIGN KEY (order_id) REFERENCES orders(id) NOT DEFERRABLE;
ALTER TABLE ONLY "public"."invoice" ADD CONSTRAINT "fk_90651744a76ed395" FOREIGN KEY (user_id) REFERENCES "user"(id) NOT DEFERRABLE;

ALTER TABLE ONLY "public"."order_item" ADD CONSTRAINT "fk_52ea1f094584665a" FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE NOT DEFERRABLE;
ALTER TABLE ONLY "public"."order_item" ADD CONSTRAINT "fk_52ea1f098d9f6d38" FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."orders" ADD CONSTRAINT "fk_e52ffdee2989f1fd" FOREIGN KEY (invoice_id) REFERENCES invoice(id) NOT DEFERRABLE;
ALTER TABLE ONLY "public"."orders" ADD CONSTRAINT "fk_e52ffdeea76ed395" FOREIGN KEY (user_id) REFERENCES "user"(id) NOT DEFERRABLE;

ALTER TABLE ONLY "public"."physical_product" ADD CONSTRAINT "fk_c7f2ac80bf396750" FOREIGN KEY (id) REFERENCES product(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."product" ADD CONSTRAINT "fk_d34a04adc6b58e54" FOREIGN KEY (default_category_id) REFERENCES category(id) NOT DEFERRABLE;

ALTER TABLE ONLY "public"."product_category" ADD CONSTRAINT "fk_cdfc735612469de2" FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE NOT DEFERRABLE;
ALTER TABLE ONLY "public"."product_category" ADD CONSTRAINT "fk_cdfc73564584665a" FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."product_tag" ADD CONSTRAINT "fk_e3a6e39c4584665a" FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE NOT DEFERRABLE;
ALTER TABLE ONLY "public"."product_tag" ADD CONSTRAINT "fk_e3a6e39cbad26311" FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."review" ADD CONSTRAINT "fk_794381c64584665a" FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE NOT DEFERRABLE;
ALTER TABLE ONLY "public"."review" ADD CONSTRAINT "fk_794381c6a76ed395" FOREIGN KEY (user_id) REFERENCES "user"(id) NOT DEFERRABLE;

-- 2025-02-07 08:30:50.25874+00
