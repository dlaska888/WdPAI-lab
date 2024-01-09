-- Define ENUM types
CREATE TYPE permissionlevel AS ENUM ('READ', 'WRITE');
CREATE TYPE userrole AS ENUM ('NORMAL', 'ADMIN');

-- Create TABLE files
CREATE TABLE file
(
    "file_id" UUID PRIMARY KEY,
    "name"    VARCHAR(255) NOT NULL,
    "date_created"  TIMESTAMP   NOT NULL DEFAULT now()
);

-- Create TABLE users
CREATE TABLE linkyuser
(
    user_id           UUID PRIMARY KEY,
    user_name         VARCHAR(50)  NOT NULL UNIQUE,
    email             VARCHAR(150) NOT NULL UNIQUE,
    password_hash     TEXT         NOT NULL,
    email_confirmed   BOOLEAN      NOT NULL DEFAULT FALSE,
    role              userrole     NOT NULL DEFAULT 'NORMAL'::userrole,
    profile_picture_id UUID, 
    refresh_token     TEXT,
    refresh_token_exp TIMESTAMP,
    date_created  TIMESTAMP   NOT NULL DEFAULT now(),
    FOREIGN KEY (profile_picture_id) REFERENCES file (file_id) ON DELETE CASCADE 
);


-- Create TABLE groups
CREATE TABLE linkgroup
(
    link_group_id UUID PRIMARY KEY,
    user_id       UUID        NOT NULL,
    name          VARCHAR(50) NOT NULL,
    date_created  TIMESTAMP   NOT NULL DEFAULT now(),
    FOREIGN KEY (user_id) REFERENCES linkyuser (user_id) ON DELETE CASCADE 
);

-- Create TABLE links
CREATE TABLE link
(
    link_id       UUID PRIMARY KEY,
    link_group_id UUID        NOT NULL,
    title         VARCHAR(50) NOT NULL,
    url           TEXT        NOT NULL,
    date_created  TIMESTAMP   NOT NULL DEFAULT now(),
    FOREIGN KEY (link_group_id) REFERENCES linkgroup (link_group_id) ON DELETE CASCADE 
);

-- Create TABLE group_shares
CREATE TABLE linkgroupshare
(
    link_group_share_id UUID PRIMARY KEY,
    user_id             UUID            NOT NULL,
    link_group_id       UUID            NOT NULL,
    permission          permissionlevel NOT NULL DEFAULT 'READ'::permissionlevel,
    date_created        TIMESTAMP       NOT NULL DEFAULT now(),
    FOREIGN KEY (user_id) REFERENCES linkyuser (user_id) ON DELETE CASCADE ,
    FOREIGN KEY (link_group_id) REFERENCES linkgroup (link_group_id) ON DELETE CASCADE 
);


--
-- Data for Name: linkyuser; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.linkyuser VALUES ('910a3b30-7f08-419a-896b-9f0f8379881b', 'user1', 'user1@gmail.com', '$2y$10$MB7sZ4YgaaQU1BYFwwS7qOwl9va6smSNveQzAgB5JsSpPK/ou0e8G', false, 'NORMAL', NULL, NULL, NULL, '2024-01-08 23:47:57');
INSERT INTO public.linkyuser VALUES ('0d621589-3581-45b1-a2fa-f5d5879fb358', 'user2', 'user2@gmail.com', '$2y$10$gCIT8MBv2I0wGVu9DAWZNudxZoCU44RKhs0i7Bb.UG08sUAbh5QxK', false, 'NORMAL', NULL, NULL, NULL, '2024-01-08 23:57:27');


--
-- Data for Name: linkgroup; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.linkgroup VALUES ('77f38a68-789f-419c-bbd1-d6c6d9787b0f', '910a3b30-7f08-419a-896b-9f0f8379881b', 'Articles', '2024-01-08 23:49:37');
INSERT INTO public.linkgroup VALUES ('ba6352c8-8199-455d-8e74-30bf585d1da2', '0d621589-3581-45b1-a2fa-f5d5879fb358', 'PC Parts', '2024-01-08 23:57:27');
INSERT INTO public.linkgroup VALUES ('c5059261-aec0-470f-85ae-80a182e5b5d4', '910a3b30-7f08-419a-896b-9f0f8379881b', 'Recipes', '2024-01-08 23:47:57');


--
-- Data for Name: link; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.link VALUES ('a1b07db3-cbf2-4fd7-9090-3b13fecd0a3a', 'c5059261-aec0-470f-85ae-80a182e5b5d4', 'Thai Green Curry', 'https://www.recipetineats.com/thai-green-curry/', '2024-01-08 23:48:25');
INSERT INTO public.link VALUES ('66113314-1d01-4bf6-94b9-b21b69601bb2', 'c5059261-aec0-470f-85ae-80a182e5b5d4', 'Pesto Chicken Penne Casserole', 'https://www.allrecipes.com/recipe/216470/pesto-chicken-penne-casserole/', '2024-01-08 23:48:49');
INSERT INTO public.link VALUES ('58d83263-5bf0-46e0-8f29-3efae54f0429', 'c5059261-aec0-470f-85ae-80a182e5b5d4', 'Pancakes', 'https://www.allrecipes.com/recipe/21014/good-old-fashioned-pancakes/', '2024-01-08 23:49:30');
INSERT INTO public.link VALUES ('22784f6b-a793-4904-a9a2-b0019cd19312', '77f38a68-789f-419c-bbd1-d6c6d9787b0f', 'Solar Eclipse 2024', 'https://www.sciencenews.org/article/total-solar-eclipse-sun-science-viewing-2024', '2024-01-08 23:55:09');
INSERT INTO public.link VALUES ('387cf8a9-e170-412d-8c37-7ac5ef91db73', '77f38a68-789f-419c-bbd1-d6c6d9787b0f', 'The Early Universe Was Bananas', 'https://www.nytimes.com/2024/01/05/science/space/astronomy-galaxies-bananas.html', '2024-01-08 23:56:12');
INSERT INTO public.link VALUES ('6133f824-fddd-4d9f-94d6-962203c66850', '77f38a68-789f-419c-bbd1-d6c6d9787b0f', 'New Images of Jupiter’s Moon Io ', 'https://www.nytimes.com/2024/01/04/science/nasa-jupiter-io-moon-pictures.html', '2024-01-08 23:57:01');
INSERT INTO public.link VALUES ('bcd439b3-e08a-48f9-8435-da937513dbfe', 'ba6352c8-8199-455d-8e74-30bf585d1da2', 'AMD Ryzen 9 5900X', 'https://www.amazon.com/AMD-Ryzen-5900X-24-Thread-Processor/dp/B08164VTWH', '2024-01-08 23:59:09');
INSERT INTO public.link VALUES ('cb416885-0d41-4e0b-ae7d-5407723788d5', 'ba6352c8-8199-455d-8e74-30bf585d1da2', 'MSI Gaming GeForce RTX 3080', 'https://www.amazon.com/MSI-RTX-3080-LHR-10G/dp/B09FSWGS7L/ref=sr_1_2?keywords=RTX+3080&qid=1704758477&sr=8-2', '2024-01-09 00:01:43');
INSERT INTO public.link VALUES ('5f6721f5-0848-469d-87a2-800653d9109e', 'ba6352c8-8199-455d-8e74-30bf585d1da2', 'CORSAIR Vengeance LPX 32GB (2 x 16GB) ', 'https://www.newegg.com/corsair-32gb-288-pin-ddr4-sdram/p/N82E16820236541', '2024-01-09 00:03:48');
INSERT INTO public.link VALUES ('d402c383-bbcb-4ebb-8211-d1d254609dfc', 'ba6352c8-8199-455d-8e74-30bf585d1da2', 'Samsung 970 EVO SSD 1TB M.2 NVMe', 'https://www.microcenter.com/product/506238/samsung-970-evo-ssd-1tb-m2-nvme-interface-pcie-30-x4-internal-solid-state-drive-with-v-nand-3-bit-mlc-technology-(mz-v7e1t0bw)', '2024-01-09 00:04:52');
INSERT INTO public.link VALUES ('da65d2e9-bdd6-4640-a937-335ed31a6463', 'ba6352c8-8199-455d-8e74-30bf585d1da2', 'ASUS ROG Strix B550-F Gaming AM4', 'https://www.newegg.com/asus-rog-strix-b550-f-gaming/p/N82E16813119312', '2024-01-09 00:05:35');


--
-- Data for Name: linkgroupshare; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.linkgroupshare VALUES ('aaa27d72-ad0d-4961-a46a-f4a8de6a14df', '910a3b30-7f08-419a-896b-9f0f8379881b', 'ba6352c8-8199-455d-8e74-30bf585d1da2', 'READ', '2024-01-09 00:15:11');
INSERT INTO public.linkgroupshare VALUES ('05e2fc4d-51db-4a2a-80a2-8c21053bdd2d', '0d621589-3581-45b1-a2fa-f5d5879fb358', 'c5059261-aec0-470f-85ae-80a182e5b5d4', 'WRITE', '2024-01-09 00:16:58');