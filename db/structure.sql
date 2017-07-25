DROP TABLE IF EXISTS `amount_transaction`;
DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
    `id`            INT(11)         NOT NULL AUTO_INCREMENT COMMENT 'User UID',
    `username`      VARCHAR(255)    NOT NULL COMMENT 'Username',
    `password`      VARCHAR(255)    NOT NULL COMMENT 'User password hash',
    `balance`       BIGINT          NOT NULL DEFAULT 0,

    PRIMARY KEY (`id`),
    UNIQUE KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `amount_transaction` (
    `id`            INT(11)         NOT NULL AUTO_INCREMENT COMMENT 'Transaction UID',
    `user_id`       INT(11)         NOT NULL COMMENT 'User Id',
    `amount`        BIGINT          NOT NULL COMMENT 'Transaction amount',
    `balance`       BIGINT          NOT NULL DEFAULT 0 COMMENT 'Account Balance after transaction',
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT current_timestamp ON UPDATE current_timestamp,

    PRIMARY KEY (`id`),
    INDEX (`user_id`, `updated_at`),
    FOREIGN KEY `amount__user`(`user_id`) REFERENCES `user`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP PROCEDURE IF EXISTS `user_make_amount`;

DELIMITER //
CREATE PROCEDURE `user_make_amount` (req_user_id INT, req_amount BIGINT, OUT resp_status INT, OUT resp_balance BIGINT, OUT resp_error VARCHAR(255)) 
    LANGUAGE SQL 
    SQL SECURITY INVOKER
    COMMENT 'A procedure for comitting amount transactions' 
BEGIN 
    DECLARE current_balance FLOAT; 

    DECLARE EXIT HANDLER FOR SQLEXCEPTION, SQLWARNING
    BEGIN
        SET resp_status = 0;
        SET resp_balance = null;
        SET resp_error = '';
        
        ROLLBACK;
    END;
    
    SET resp_status = 0;
    SET resp_balance = null;
    SET resp_error = '';
    SET current_balance = null;
    

    START TRANSACTION;
        IF EXISTS (SELECT `username` FROM `user` WHERE `id`=req_user_id FOR UPDATE) THEN
            SELECT `balance` INTO current_balance FROM `amount_transaction` WHERE `user_id`=req_user_id ORDER BY `updated_at` DESC LIMIT 1;
            IF (current_balance IS NULL) THEN
                IF (req_amount > 0) THEN
                    SET resp_status = 1;
                    SET resp_balance = req_amount;
                ELSE
                    SET resp_error = 'Negative balance after transaction';
                END IF;
            ELSE
                IF (current_balance + req_amount >= 0) THEN
                    SET resp_status = 1;
                    SET resp_balance = current_balance + req_amount;
                ELSE
                    SET resp_error = 'Negative balance after transaction';
                END IF;
            END IF;
        ELSE
            SET resp_error = 'Failed to find user with given id';
        END IF;
        
        IF (1 = resp_status) THEN
            INSERT INTO `amount_transaction` (`user_id`, `amount`, `balance`) VALUES (req_user_id, req_amount, resp_balance);
            UPDATE `user` SET `balance` = resp_balance WHERE `id`=req_user_id;
        END IF;
    COMMIT;
END //
/* CALL user_make_amount(4, -1000 ,@res, @balance, @error); SELECT @res AS 'res', @balance AS 'balance', @error AS 'error'; // */
