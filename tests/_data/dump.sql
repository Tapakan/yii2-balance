/* Replace this file with actual dump of your database */

CREATE TABLE IF NOT EXISTS balance_history
(
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    value DECIMAL(13,4),
    account_id INT(11),
    order_id INT(11),
    site_id INT(11),
    partner_id INT(11),
    ref INT(19),
    created_at DATETIME
);

CREATE INDEX INDEX_ACCOUNT_ID ON balance_history (account_id);

CREATE TABLE IF NOT EXISTS balance
(
  id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  user_id INT(11),
  value DECIMAL(13,4),
  updated_at DATETIME
);
CREATE UNIQUE INDEX INDEX_UNIQUE_USER_ID ON balance (user_id)