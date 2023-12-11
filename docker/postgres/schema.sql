-- Define ENUM types
CREATE TYPE permissionlevel AS ENUM ('READ', 'WRITE');
CREATE TYPE userrole AS ENUM ('NORMAL', 'ADMIN');

-- Create TABLE files
CREATE TABLE file
(
    "file_id" UUID PRIMARY KEY,
    "name"    VARCHAR(255) NOT NULL
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
    FOREIGN KEY (profile_picture_id) REFERENCES file (file_id) ON DELETE SET NULL
);


-- Create TABLE groups
CREATE TABLE linkgroup
(
    link_group_id UUID PRIMARY KEY,
    user_id       UUID        NOT NULL,
    name          VARCHAR(50) NOT NULL,
    date_created  TIMESTAMP   NOT NULL DEFAULT now(),
    FOREIGN KEY (user_id) REFERENCES linkyuser (user_id)
);

-- Create TABLE links
CREATE TABLE link
(
    link_id       UUID PRIMARY KEY,
    link_group_id UUID        NOT NULL,
    title         VARCHAR(50) NOT NULL,
    url           TEXT        NOT NULL,
    FOREIGN KEY (link_group_id) REFERENCES linkgroup (link_group_id)
);

-- Create TABLE group_shares
CREATE TABLE linkgroupshare
(
    link_group_share_id UUID PRIMARY KEY,
    user_id             UUID            NOT NULL,
    link_group_id       UUID            NOT NULL,
    permission          permissionlevel NOT NULL DEFAULT 'READ'::permissionlevel,
    date_created        TIMESTAMP       NOT NULL DEFAULT now(),
    FOREIGN KEY (user_id) REFERENCES linkyuser (user_id),
    FOREIGN KEY (link_group_id) REFERENCES linkgroup (link_group_id)
);



