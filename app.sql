CREATE DATABASE user;

CREATE TABLE user.user_info (
	user_id VARCHAR(255) PRIMARY KEY,
	user_password VARCHAR(255) NOT NULL,
	user_name VARCHAR(255) NOT NULL,
	create_date DATETIME NOT NULL
);

CREATE TABLE user.chat (
	chat_name VARCHAR(255) PRIMARY KEY,
	chat_table_name VARCHAR(255)  UNIQUE NOT NULL
);
