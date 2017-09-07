CREATE TABLE mysearch_searches (
  mysearchid INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
  keyword    VARCHAR(100)          NOT NULL DEFAULT '',
  datesearch DATETIME              NOT NULL DEFAULT CURRENT_TIMESTAMP,
  uid        MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  ip         VARCHAR(32)           NOT NULL DEFAULT '',
  PRIMARY KEY (mysearchid),
  KEY keyword (keyword, uid),
  KEY uid (uid),
  KEY datesearch (datesearch),
  FULLTEXT KEY keyword_2 (keyword)
)
  ENGINE = MyISAM;

