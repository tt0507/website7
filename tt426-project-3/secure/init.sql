-- TODO: Put ALL SQL in between `BEGIN TRANSACTION` and `COMMIT`
BEGIN TRANSACTION;

-- TODO: create tables
CREATE TABLE `users` (
    `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `username` TEXT NOT NULL UNIQUE,
    `password` TEXT NOT NULL
);

INSERT INTO users (id, username, password) VALUES (1, 'tt426', '$2y$10$RZ662V2JNlZlSGFVn79jdOxTH3Ieb.SqPqLJxPbn1Upi/TF2HMLJa'); -- Password: ithaca;
INSERT INTO users (id, username, password) VALUES (2, 'tt111', '$2y$10$aj8D8KltX8deJcqQW7DOBeHLYo9jK0gl72r03B/IC2GEh3r3DmxAq'); -- Password: yokohama;
INSERT INTO users (id, username, password) VALUES (3, 'tt507', '$2y$10$L5aZhQwpiEthPlmEdy3kxuijE8.tOaWPSPronl70cRT7kNgLkDKxG'); -- Password: cornell;
INSERT INTO users (id, username, password) VALUES (4, 'tt555', '$2y$10$CHNyhSsO2tCSpxeISUfnXeeVEtuaZKR3un.X0we8qcwzXMzTTlIdm'); -- Password: test; user to check if edit.php (saved file section) works properly;

CREATE TABLE `images` (
    `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `image_name` TEXT NOT NULL,
    `image_ext` TEXT NOT NULL,
    `title` TEXT NOT NULL,
    `users_id` INTEGER NOT NULL
);

INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (1, '1.jpg', 'jpg', 'Poke Bowl', 1); /* #1 */
INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (2, '2.jpg', 'jpg', 'Spaghetti', 1); /* #2 */
INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (3, '3.jpg', 'jpg', 'Tiramisu', 1); /* #3 */
INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (4, '4.jpg', 'jpg', 'Thai Food', 2); /* #4 */
INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (5, '5.jpg', 'jpg', 'Italian Food', 2); /* #5 */
INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (6, '6.jpg', 'jpg', 'Ice cream', 2); /* #6 */
INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (7, '7.jpg', 'jpg', 'Coffee', 3); /* #7 */
INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (8, '8.jpg', 'jpg', 'Budae Jjigae', 3); /* #8 */
INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (9, '9.jpg', 'jpg', 'Pizza', 3); /* #9 */
INSERT INTO images (id, image_name, image_ext, title, users_id) VALUES (10, '10.jpg', 'jpg', 'Sundubu Jjigae', 3); /* #10 */

CREATE TABLE `tags`(
    `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `tag` TEXT NOT NULL UNIQUE
);

INSERT INTO tags (id, tag) VALUES (1, 'Korean'); /* #1 */
INSERT INTO tags (id, tag) VALUES (2, 'Italian'); /* #2 */
INSERT INTO tags (id, tag) VALUES (3, 'American'); /* #3 */
INSERT INTO tags (id, tag) VALUES (4, 'Asian'); /* #4 */
INSERT INTO tags (id, tag) VALUES (5, 'Dessert'); /* #5 */
INSERT INTO tags (id, tag) VALUES (6, 'Thai'); /* #6 */
INSERT INTO tags (id, tag) VALUES (7, 'Meat'); /* #7 */
INSERT INTO tags (id, tag) VALUES (8, 'Vegetable'); /* #8 */
INSERT INTO tags (id, tag) VALUES (9, 'Seafood'); /* #9 */

CREATE TABLE `image_tags` (
    `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `images_id` INTEGER NOT NULL,
    `tag_id` INTEGER NOT NULL
);

INSERT INTO image_tags (id, images_id, tag_id) VALUES (1, 1, 4);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (2, 1, 8);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (3, 1, 9);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (4, 2, 2);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (5, 2, 9);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (6, 3, 2);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (7, 3, 5);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (8, 4, 6);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (9, 4, 7);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (10, 4, 8);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (11, 5, 9);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (12, 6, 5);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (13, 7, 5);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (14, 8, 1);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (15, 8, 7);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (16, 8, 8);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (17, 9, 3);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (18, 10, 1);
INSERT INTO image_tags (id, images_id, tag_id) VALUES (19, 10, 9);

CREATE TABLE `sessions`(
    `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `user_id` INTEGER NOT NULL,
    `session` TEXT NOT NULL UNIQUE
);

-- CREATE TABLE `examples` (
-- 	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
-- 	`name`	TEXT NOT NULL
-- );


-- TODO: initial seed data

-- TODO: FOR HASHED PASSWORDS, LEAVE A COMMENT WITH THE PLAIN TEXT PASSWORD!

-- INSERT INTO `examples` (id,name) VALUES (1, 'example-1');
-- INSERT INTO `examples` (id,name) VALUES (2, 'example-2');

COMMIT;
