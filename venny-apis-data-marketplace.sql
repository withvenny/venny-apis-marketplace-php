-- public.catalogs definition

-- Drop table

-- DROP TABLE public.catalogs;

CREATE TABLE public.catalogs (
	id int4 NOT NULL DEFAULT nextval('catalogs_sequence'::regclass),
	catalog_id varchar(30) NOT NULL,
	catalog_attributes json NULL,
	catalog_online bool NOT NULL DEFAULT true,
	catalog_public int4 NOT NULL DEFAULT 1,
	catalog_name varchar(255) NOT NULL,
	catalog_description text NOT NULL,
	catalog_slug varchar(255) NOT NULL,
	catalog_images jsonb NULL,
	partner_id varchar(30) NOT NULL,
	app_id varchar(30) NOT NULL,
	event_id varchar(30) NOT NULL,
	process_id varchar(30) NOT NULL,
	time_started timestamptz NOT NULL DEFAULT now(),
	time_updated timestamptz NOT NULL DEFAULT now(),
	time_finished timestamptz NOT NULL DEFAULT now(),
	active int4 NOT NULL DEFAULT 1,
	CONSTRAINT catalogs_catalog_id_key UNIQUE (catalog_id),
	CONSTRAINT catalogs_pkey PRIMARY KEY (catalog_id)
);
CREATE INDEX idx_catalogs ON public.catalogs USING btree (catalog_id);

-- Permissions

ALTER TABLE public.catalogs OWNER TO crsvtfvfkltccq;
GRANT ALL ON TABLE public.catalogs TO crsvtfvfkltccq;


-- public.category definition

-- Drop table

-- DROP TABLE public.category;

CREATE TABLE public.category (
	id int4 NOT NULL DEFAULT nextval('category_sequence'::regclass),
	category_id varchar(30) NOT NULL,
	category_attributes json NULL,
	category_online bool NOT NULL DEFAULT true,
	category_public int4 NOT NULL DEFAULT 1,
	category_name varchar(255) NOT NULL,
	category_description text NOT NULL,
	category_slug varchar(255) NOT NULL,
	category_images jsonb NULL,
	catalog_id varchar(30) NOT NULL,
	partner_id varchar(30) NOT NULL,
	app_id varchar(30) NOT NULL,
	event_id varchar(30) NOT NULL,
	process_id varchar(30) NOT NULL,
	time_started timestamptz NOT NULL DEFAULT now(),
	time_updated timestamptz NOT NULL DEFAULT now(),
	time_finished timestamptz NOT NULL DEFAULT now(),
	active int4 NOT NULL DEFAULT 1,
	category_parent varchar(30) NULL,
	CONSTRAINT categories_category_id_key UNIQUE (category_id),
	CONSTRAINT category_catalog_id_fkey FOREIGN KEY (catalog_id) REFERENCES catalogs(catalog_id)
);
CREATE INDEX idx_category ON public.category USING btree (category_id);

-- Permissions

ALTER TABLE public.category OWNER TO crsvtfvfkltccq;
GRANT ALL ON TABLE public.category TO crsvtfvfkltccq;


-- public.products definition

-- Drop table

-- DROP TABLE public.products;

CREATE TABLE public.products (
	id int4 NOT NULL DEFAULT nextval('products_sequence'::regclass),
	product_id varchar(30) NOT NULL,
	product_attributes json NULL,
	product_online bool NOT NULL DEFAULT true,
	product_public int4 NOT NULL DEFAULT 1,
	product_name varchar(255) NOT NULL,
	product_description text NOT NULL,
	product_slug varchar(255) NOT NULL,
	product_images jsonb NULL,
	category_id varchar(30) NOT NULL,
	partner_id varchar(30) NOT NULL,
	app_id varchar(30) NOT NULL,
	event_id varchar(30) NOT NULL,
	process_id varchar(30) NOT NULL,
	time_started timestamptz NOT NULL DEFAULT now(),
	time_updated timestamptz NOT NULL DEFAULT now(),
	time_finished timestamptz NOT NULL DEFAULT now(),
	active int4 NOT NULL DEFAULT 1,
	product_brand varchar(255) NULL,
	CONSTRAINT products_product_id_key UNIQUE (product_id),
	CONSTRAINT products_slug_constraint UNIQUE (product_slug),
	CONSTRAINT products_category_id_fkey FOREIGN KEY (category_id) REFERENCES category(category_id)
);
CREATE INDEX idx_products ON public.products USING btree (product_id);

-- Permissions

ALTER TABLE public.products OWNER TO crsvtfvfkltccq;
GRANT ALL ON TABLE public.products TO crsvtfvfkltccq;


-- public.items definition

-- Drop table

-- DROP TABLE public.items;

CREATE TABLE public.items (
	id int4 NOT NULL DEFAULT nextval('items_sequence'::regclass),
	item_id varchar(30) NOT NULL,
	item_attributes json NULL,
	item_online bool NOT NULL DEFAULT true,
	item_public int4 NOT NULL DEFAULT 1,
	item_name varchar(255) NOT NULL,
	item_description text NOT NULL,
	item_slug varchar(255) NOT NULL,
	item_images jsonb NULL,
	item_price numeric(5,2) NOT NULL,
	item_sku varchar(30) NOT NULL,
	product_id varchar(30) NOT NULL,
	partner_id varchar(30) NOT NULL,
	app_id varchar(30) NOT NULL,
	event_id varchar(30) NOT NULL,
	process_id varchar(30) NOT NULL,
	time_started timestamptz NOT NULL DEFAULT now(),
	time_updated timestamptz NOT NULL DEFAULT now(),
	time_finished timestamptz NOT NULL DEFAULT now(),
	active int4 NOT NULL DEFAULT 1,
	item_inventory int4 NULL,
	item_manufacturer varchar(255) NULL,
	CONSTRAINT items_item_id_key UNIQUE (item_id),
	CONSTRAINT items_product_id_fkey FOREIGN KEY (product_id) REFERENCES products(product_id)
);
CREATE INDEX idx_items ON public.items USING btree (item_id);

-- Permissions

ALTER TABLE public.items OWNER TO crsvtfvfkltccq;
GRANT ALL ON TABLE public.items TO crsvtfvfkltccq;

INSERT INTO public.products (product_id,product_attributes,product_online,product_public,product_name,product_description,product_slug,product_images,category_id,partner_id,app_id,event_id,process_id,time_started,time_updated,time_finished,active,product_brand) VALUES
	 ('prd_23e9ab57d87aa',NULL,true,1,'Yikes Garment-dyed slub cotton henley','Henley polo tee','yikes-2garmentdyed-slub-cotton-henley','{}','cat_b6d9166ff8a73','par_jmercer','app_jmercer','obj_83660184af7a7','obj_ecf300adf8e8a','2020-05-30 08:12:16.684326-05','2020-05-30 08:12:16.684326-05','2020-05-30 08:12:16.684326-05',1,'J. Crew'),
	 ('prd_05afb2a63888c',NULL,true,1,'Yikes Garment-dyed slub cotton henley','This is my favorite henley ever','yikes-garmentdyed-slub-cotton-henley','{}','cat_b6d9166ff8a73','par_jmercer','app_jmercer','obj_3987de9e8c313','obj_51e500b0a5132','2020-05-30 08:13:58.155356-05','2020-05-30 08:13:58.155356-05','2020-05-30 08:13:58.155356-05',1,'J. Crew');


	 INSERT INTO public.items (item_id,item_attributes,item_online,item_public,item_name,item_description,item_slug,item_images,item_price,item_sku,product_id,partner_id,app_id,event_id,process_id,time_started,time_updated,time_finished,active,item_inventory,item_manufacturer) VALUES
	 ('itm_1fe47b326947f',NULL,true,1,'Yikes Garment-dyed slub cotton henley (Small)','Size 1','yikes-garmentdyed-slub-cotton-henley-small','{}',25.57,'YIKES-SMALL','prd_05afb2a63888c','par_jmercer','app_jmercer','obj_68081c2c96efb','obj_e4c41cd547003','2020-05-30 09:17:44.676845-05','2020-05-30 09:17:44.676845-05','2020-05-30 09:17:44.676845-05',1,55,'J. Crew'),
	 ('itm_392889d03a698',NULL,true,1,'Yikes Garment-dyed slub cotton henley (Small)','Size 1','yikes-garmentdyed-slub-cotton-henley-small','{}',25.57,'YIKES-SMALL','prd_05afb2a63888c','par_jmercer','app_jmercer','obj_5ed005426cccf','obj_4b640ecf39dfa','2020-05-30 09:18:48.80866-05','2020-05-30 09:18:48.80866-05','2020-05-30 09:18:48.80866-05',1,55,'J. Crew'),
	 ('itm_37ca7580e98ee',NULL,true,1,'Yikes Garment-dyed slub cotton henley (Medium)','Size 2','yikes-garmentdyed-slub-cotton-henley-small','{}',25.57,'YIKES-MEDIUM','prd_05afb2a63888c','par_jmercer','app_jmercer','obj_124d0170019f4','obj_ba402eaf7dc83','2020-05-30 09:21:49.567931-05','2020-05-30 09:21:49.567931-05','2020-05-30 09:21:49.567931-05',1,55,'J. Crew'),
	 ('itm_2f62c76b93356',NULL,true,1,'Yikes Garment-dyed slub cotton henley (Large)','Size 3','yikes-garmentdyed-slub-cotton-henley-small','{}',25.57,'YIKES-LARGE','prd_05afb2a63888c','par_jmercer','app_jmercer','obj_902139534bb81','obj_02bd63b4da39b','2020-05-30 09:22:22.238081-05','2020-05-30 09:22:22.238081-05','2020-05-30 09:22:22.238081-05',1,112,'J. Crew'),
	 ('itm_47d1647dba382',NULL,true,1,'Yikes Garment-dyed slub cotton henley (Large)','Size 3','yikes-garmentdyed-slub-cotton-henley-small','{}',25.57,'YIKES-LARGE','prd_05afb2a63888c','par_jmercer','app_jmercer','obj_1078585ffeb92','obj_b10dbd1479563','2020-05-30 09:22:56.710805-05','2020-05-30 09:22:56.710805-05','2020-05-30 09:22:56.710805-05',1,112,'J. Crew'),
	 ('itm_5a94308e1a18e',NULL,true,1,'Yikes Garment-dyed slub cotton henley (Large)','Size 3','yikes-garmentdyed-slub-cotton-henley-small','{}',21.00,'YIKES-LARGE','prd_05afb2a63888c','par_jmercer','app_jmercer','obj_26042ea492899','obj_b32134d35236a','2020-05-30 09:29:42.673528-05','2020-05-30 09:29:42.673528-05','2020-05-30 09:29:42.673528-05',1,112,'J. Crew');

	 INSERT INTO public.category (category_id,category_attributes,category_online,category_public,category_name,category_description,category_slug,category_images,catalog_id,partner_id,app_id,event_id,process_id,time_started,time_updated,time_finished,active,category_parent) VALUES
	 ('cat_4247cfb112855',NULL,true,1,'Men''s Shirts','These are shirts','mens-shirts','{}','ctg_c98d8cc726992','par_jmercer','app_jmercer','obj_79035e44310da','obj_a47f952461c78','2020-05-30 07:36:39.428843-05','2020-05-30 07:36:39.428843-05','2020-05-30 07:36:39.428843-05',1,NULL),
	 ('cat_029225dc455bb',NULL,true,1,'Women''s Shirts','These are shirts','womens-shirts','{}','ctg_c98d8cc726992','par_jmercer','app_jmercer','obj_5e324b806f5bd','obj_6aef3c0f05b45','2020-05-30 07:38:11.628756-05','2020-05-30 07:38:11.628756-05','2020-05-30 07:38:11.628756-05',1,NULL),
	 ('cat_320c336d5e873',NULL,true,1,'Boy''s Shirts','These are shirts','boys-shirts','{}','ctg_c98d8cc726992','par_jmercer','app_jmercer','obj_909c0646c050f','obj_f578ea869993d','2020-05-30 07:39:07.520738-05','2020-05-30 07:39:07.520738-05','2020-05-30 07:39:07.520738-05',1,NULL),
	 ('cat_50048ac667830',NULL,true,1,'Girl''s Shirts','These are shirts','girls-shirts','{}','ctg_c98d8cc726992','par_jmercer','app_jmercer','obj_f196e9b42454f','obj_eb38b823b05a3','2020-05-30 07:39:17.925266-05','2020-05-30 07:39:17.925266-05','2020-05-30 07:39:17.925266-05',1,NULL),
	 ('cat_fa221f710d690',NULL,true,1,'Summer Shirts','These are shirts','summer-shirts','{}','ctg_c98d8cc726992','par_jmercer','app_jmercer','obj_e6eef2451e3a4','obj_0680bf89c5075','2020-05-30 07:27:43.545163-05','2020-05-30 07:27:43.545163-05','2020-05-30 07:27:43.545163-05',1,'cat_50048ac667830'),
	 ('cat_b6d9166ff8a73',NULL,true,1,'Collection','Our entire collection at your finger tips','collection','{}','ctg_c98d8cc726992','par_jmercer','app_jmercer','obj_401ee8a4eca2d','obj_f357a3c9f170e','2020-05-30 07:56:01.268657-05','2020-05-30 07:56:01.268657-05','2020-05-30 07:56:01.268657-05',1,NULL);

	 INSERT INTO public.catalogs (catalog_id,catalog_attributes,catalog_online,catalog_public,catalog_name,catalog_description,catalog_slug,catalog_images,partner_id,app_id,event_id,process_id,time_started,time_updated,time_finished,active) VALUES
	 ('ctg_c98d8cc726992',NULL,true,1,'Greek by J. Mercer','Lifestyle clothing for all well meaning Greeks.','jmercer','{}','par_jmercer','app_jmercer','obj_d96ac8417b689','obj_83cad4d515f00','2020-05-30 06:53:23.467872-05','2020-05-30 06:53:23.467872-05','2020-05-30 06:53:23.467872-05',1);

	 