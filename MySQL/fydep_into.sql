USE `fydepdb`;

DELETE FROM users;
INSERT INTO users(username,`password`,hidden_pass) 
VALUES('Test','111','111'), ('Test2','111','111');

DELETE FROM folders;
INSERT INTO folders(id,name,user,hidden) 
VALUES('1','Folder1','Test',0),('2','Folder11','Test',0),('3','Folder2','Test2',0),('4','Folder22','Test2',0),('5','Folder222','Test2',0);

INSERT INTO feeds(id,id_folder,name,upd_time,link,rss_link,last_date_post,enabled,deleted) 
VALUES
('1','1','Feed1Folder1',4,'http://www.google.es','http://www.google.es',DEFAULT, 1,0),
('2','1','Feed2Folder1',4,'http://www.google.es','http://www.google.es',DEFAULT, 1,0),
('3','2','Feed1Folder1',4,'http://www.google.es','http://www.google.es',DEFAULT, 1,0),
('4','2','Feed2Folder11',4,'http://www.google.es','http://www.google.es',DEFAULT, 1,0),
('5','3','Feed1Folder2',4,'http://www.google.es','http://www.google.es',DEFAULT, 1,0),
('6','3','Feed2Folder2',4,'http://www.google.es','http://www.google.es',DEFAULT, 1,0),
('7','4','Feed1Folder22',4,'http://www.google.es','http://www.google.es',DEFAULT, 1,0),
('8','4','Feed2Folder22',4,'http://www.google.es','http://www.google.es',DEFAULT, 1,0),
('9','5','Feed1Folder222',4,'http://www.google.es','http://www.google.es',DEFAULT, 1,0);

-- INSERT INTO posts(id,id_feed,title,description,link,unread,favorite,`date`) 
-- VALUES();

-- INSERT INTO tags(id,user,tag_name) 
-- VALUES();

-- INSERT INTO post_tags(id_post,id_tag) 
-- VALUES();

-- INSERT INTO post_tags(id_post,id_tag) 
-- VALUES();
