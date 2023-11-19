-- Define ENUM types
CREATE TYPE permission_level AS ENUM ('read', 'write');
CREATE TYPE user_role AS ENUM ('normal', 'admin');

-- Create TABLE users
CREATE TABLE users
(
    user_id           SERIAL PRIMARY KEY,
    user_name         VARCHAR(50)  NOT NULL,
    email             VARCHAR(150) NOT NULL,
    password_hash     TEXT         NOT NULL,
    email_confirmed   BOOLEAN NOT NULL DEFAULT FALSE,
    role              user_role NOT NULL DEFAULT 'normal'::user_role,
    refresh_token     TEXT,
    refresh_token_exp TIMESTAMP
);

-- Create TABLE groups
CREATE TABLE groups
(
    group_id     SERIAL PRIMARY KEY,
    user_id      INT         NOT NULL,
    name         VARCHAR(50) NOT NULL,
    date_created TIMESTAMP   NOT NULL DEFAULT now(),
    FOREIGN KEY (user_id) REFERENCES users (user_id)
);

-- Create TABLE links
CREATE TABLE links
(
    link_id  SERIAL PRIMARY KEY,
    group_id INT         NOT NULL,
    title    VARCHAR(50) NOT NULL,
    url      TEXT        NOT NULL,
    FOREIGN KEY (group_id) REFERENCES groups (group_id)
);

-- Create TABLE group_shares
CREATE TABLE group_shares
(
    group_share_id SERIAL PRIMARY KEY,
    user_id        INT              NOT NULL,
    group_id       INT              NOT NULL,
    permission     permission_level NOT NULL DEFAULT 'read'::permission_level,
    date_created   TIMESTAMP        NOT NULL DEFAULT now(),
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (group_id) REFERENCES groups (group_id)
);

