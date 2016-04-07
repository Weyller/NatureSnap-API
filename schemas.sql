CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email TEXT NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(60) NOT NULL,
    latest_login TEXT NOT NULL,
    ip_address TEXT NOT NULL,
    PRIMARY KEY (user_id)
);
CREATE TABLE groups (
    group_id INT(11) NOT NULL AUTO_INCREMENT,
    group_name VARCHAR(100)  NOT NULL,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (group_id)
);
CREATE TABLE group_photos (
    gplink_id INT(11) NOT NULL AUTO_INCREMENT,
    group_id INT(11) NOT NULL,
    photo_id INT(11) NOT NULL,
    PRIMARY KEY (gplink_id)
);
CREATE TABLE photos (
    photo_id INT(11) NOT NULL AUTO_INCREMENT,
    image_title TEXT NOT NULL,
    user_id INT(11) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(250) NOT NULL,
    timestamp text NOT NULL,
    PRIMARY KEY (photo_id)
);
CREATE TABLE location (
    photo_id INT(11) NOT NULL,
    location TEXT NOT NULL,
    name TEXT NOT NULL,
    city TEXT NOT NULL,
    state TEXT NOT NULL,
    country TEXT NOT NULL,
    PRIMARY KEY (photo_id)
);
CREATE TABLE rating (
    rating_id INT(11) NOT NULL AUTO_INCREMENT,
    photo_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    rating INT(11) NOT NULL,
    PRIMARY KEY (rating_id)
);
CREATE TABLE comment (
    comment_id INT(11) NOT NULL AUTO_INCREMENT,
    photo_id INT(11) NOT NULL,
    friend_id INT(11) NOT NULL,
    comment INT(11) NOT NULL,
    timestamp TEXT NOT NULL,
    PRIMARY KEY (comment_id)
);
CREATE TABLE views (
    photo_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    num_of_views INT(11) NOT NULL,
    PRIMARY KEY (photo_id)
);
