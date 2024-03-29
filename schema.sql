CREATE TABLE `User`
(
    `ID`           varchar(36)                                       NOT NULL,
    `Username`     varchar(30) UNIQUE                                NOT NULL,
    `Password`     varchar(256)                                      NOT NULL,
    `Name`         varchar(50)                                       NOT NULL,
    `User_Type`    enum ('ADMIN', 'MANAGER', 'EMPLOYEE', 'CUSTOMER') NOT NULL,
    `Phone_Number` varchar(10)                                       NOT NULL,
    `DOB`          date                                              NOT NULL,
    `Address`      varchar(100)                                      NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE `Employee`
(
    `ID`        varchar(36) NOT NULL,
    `Branch_ID` varchar(36) NOT NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`ID`) REFERENCES `User` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `Branch`
(
    `ID`         varchar(36)  NOT NULL,
    `Name`       varchar(50)  NOT NULL,
    `Address`    varchar(100) NOT NULL,
    `Manager_ID` varchar(36)  NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`Manager_ID`) REFERENCES `Employee` (`ID`)
);

ALTER TABLE `Employee` ADD FOREIGN KEY (`Branch_ID`) REFERENCES `Branch`(`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

CREATE TABLE `Savings_Plan`
(
    `ID`              int            NOT NULL AUTO_INCREMENT,
    `Name`            varchar(20)    NOT NULL,
    `Interest_Rate`   int            NOT NULL,
    `Minimum_Balance` decimal(15, 2) NOT NULL,
    `Minimum_Age`     int            NULL,
    `Maximum_Age`     int            NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE `Account`
(
    `Account_Number` varchar(20)                 NOT NULL,
    `User_ID`        varchar(36)                 NOT NULL,
    `Created_Time`   timestamp                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Branch_ID`      varchar(36)                 NOT NULL,
    `Account_Type`   enum ('CURRENT', 'SAVINGS') NOT NULL,
    `Amount`         decimal(15, 2)              NOT NULL,
    PRIMARY KEY (`Account_Number`),
    FOREIGN KEY (`Branch_ID`) REFERENCES `Branch` (`ID`),
    FOREIGN KEY (`User_ID`) REFERENCES `User` (`ID`),
    CHECK (`Amount` >= 0)
);

CREATE TABLE `Savings`
(
    `Account_Number` varchar(20) NOT NULL,
    `Plan_ID`        int         NOT NULL,
    PRIMARY KEY (`Account_Number`),
    FOREIGN KEY (`Account_Number`) REFERENCES `Account` (`Account_Number`),
    FOREIGN KEY (`Plan_ID`) REFERENCES `Savings_Plan` (`ID`)
);

CREATE TABLE `Transaction`
(
    `Transaction_ID` varchar(36)                                           NOT NULL,
    `From`           varchar(20)                                           NULL     DEFAULT NULL,
    `To`             varchar(20)                                           NULL     DEFAULT NULL,
    `Type`           enum ('WITHDRAWAL', 'TRANSFER', 'DEPOSIT', 'PAYMENT') NOT NULL,
    `Amount`         decimal(15, 2)                                        NOT NULL,
    `Created_Time`   timestamp                                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Description`    text                                                  NULL     DEFAULT NULL,
    PRIMARY KEY (`Transaction_ID`),
    FOREIGN KEY (`From`) REFERENCES `Account` (`Account_Number`),
    FOREIGN KEY (`To`) REFERENCES `Account` (`Account_Number`),
    CHECK (`Amount` > 0)
);

CREATE TABLE `FD_Plan`
(
    `ID`            int NOT NULL AUTO_INCREMENT,
    `Duration`      int NOT NULL,
    `Interest_Rate` int NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE `FD`
(
    `ID`             varchar(36)    NOT NULL,
    `Account_Number` varchar(20)    NOT NULL,
    `Plan_ID`        int            NOT NULL,
    `Amount`         decimal(15, 2) NOT NULL,
    `Created_Time`   timestamp      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`Plan_ID`) REFERENCES `FD_Plan` (`ID`),
    FOREIGN KEY (`Account_Number`) REFERENCES `Account` (`Account_Number`),
    CHECK (`Amount` > 0)
);

CREATE TABLE `Loan_Plan`
(
    `ID`        int NOT NULL AUTO_INCREMENT,
    `Interest_Rate` decimal(5, 2) NOT NULL,
    `Duration` int NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE `Loan`
(
    `ID`           varchar(36)                                      NOT NULL,
    `User_ID`      varchar(36)                                      NOT NULL,
    `Loan_Type`    enum ('PERSONAL', 'BUSINESS')                    NOT NULL,
    `Status`       enum ('CREATED', 'APPROVED', 'REJECTED', 'PAID') NOT NULL,
    `Created_Time` timestamp                                        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Amount`       decimal(15, 2)                                   NOT NULL,
    `Loan_Mode`    enum ('NORMAL', 'ONLINE')                        NOT NULL,
    `Plan_ID`      int                                              NOT NULL,
    `Reason`       text,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`User_ID`) REFERENCES `User` (`ID`),
    FOREIGN KEY (`Plan_ID`) REFERENCES `Loan_Plan` (`ID`),
    CHECK (`Amount` > 0)
);


CREATE TABLE `Normal_Loan`
(
    `ID`        varchar(36)                          NOT NULL,
    `Account_Number` varchar(20)                     NOT NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`ID`) REFERENCES `Loan` (`ID`),
    FOREIGN KEY (`Account_Number`) REFERENCES `Account` (`Account_Number`)
);

CREATE TABLE `Online_Loan`
(
    `ID`        varchar(36)                 NOT NULL,
    `FD_ID`     varchar(36)                 NOT NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`ID`) REFERENCES `Loan` (`ID`),
    FOREIGN KEY (`FD_ID`) REFERENCES `FD` (`ID`)
);

CREATE TABLE `Installment`
(
    `ID`      varchar(36)              NOT NULL,
    `Loan_ID` varchar(36)              NOT NULL,
    `Year`    int                      NOT NULL,
    `Month`   int                      NOT NULL,
    `Amount`  decimal(15, 2)           NOT NULL,
    `Status`  enum ('CREATED', 'PAID') NOT NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`Loan_ID`) REFERENCES `Loan` (`ID`),
    CHECK (`Amount` > 0)
);

DELIMITER $$
CREATE EVENT fd_interest
    ON SCHEDULE EVERY 1 DAY
    ON COMPLETION PRESERVE
    DO
    BEGIN
        DECLARE length INT DEFAULT 0;
        DECLARE counter INT DEFAULT 0;
        DECLARE month_length INT DEFAULT 30;
        SELECT COUNT(*) FROM FD INTO length;
        SET counter = 0;
        SET month_length = 30;
        WHILE counter < length
            DO
                SELECT F.ID, F.Account_Number, F.Amount, FP.Duration, FP.Interest_Rate, F.Created_Time
                INTO @id, @s, @a, @d, @r, @t
                FROM FD F
                         JOIN FD_Plan FP on F.Plan_ID = FP.ID
                LIMIT counter, 1;

                SELECT TIMESTAMPDIFF(DAY, @t, current_timestamp()) INTO @time_diff;

                IF @time_diff MOD month_length = 0 AND @time_diff <= @d * month_length THEN
                    UPDATE Account SET Amount = Amount + @a * @r / (100 * 12) WHERE Account_Number = @s;
                    IF @time_diff = @d * month_length THEN
                        UPDATE Account SET Amount = Amount + @a WHERE Account_Number = @s;
                        DELETE FROM FD WHERE ID = @id;
                    END IF;
                END IF;
                SET counter = counter + 1;
            END WHILE;
    END $$

CREATE EVENT savings_interest
    ON SCHEDULE EVERY 1 DAY
    ON COMPLETION PRESERVE
    DO
    BEGIN
        DECLARE length INT DEFAULT 0;
        DECLARE counter INT DEFAULT 0;
        DECLARE month_length INT DEFAULT 30;
        SELECT COUNT(*) FROM Savings INTO length;
        SET counter = 0;
        SET month_length = 30;
        WHILE counter < length
            DO
                SELECT S.Account_Number, A.Created_Time, SP.Interest_Rate, A.Amount
                INTO @num, @t, @r, @a
                FROM Savings S
                         JOIN Savings_Plan SP ON S.Plan_ID = SP.ID
                         JOIN Account A ON S.Account_Number = A.Account_Number
                LIMIT counter, 1;

                SELECT TIMESTAMPDIFF(DAY, @t, current_timestamp()) INTO @time_diff;

                IF @time_diff MOD month_length = 0 THEN
                    UPDATE Account SET Amount = Amount + @a * @r / (100 * 12) WHERE Account_Number = @num;
                END IF;
                SET counter = counter + 1;
            END WHILE;
    END $$

CREATE TRIGGER limit_withdrawals
    BEFORE INSERT
    ON `Transaction`
    FOR EACH ROW
BEGIN
    DECLARE maximum_withdrawals INT;
    SET maximum_withdrawals = 5;

    IF (NEW.Type = 'WITHDRAWAL') THEN
        IF (SELECT 1 FROM Account WHERE Account_Number = NEW.From AND Account_Type = 'SAVINGS') THEN
            SELECT COUNT(*)
            INTO @count
            FROM Transaction
            WHERE `From` = NEW.From
              AND Type = 'WITHDRAWAL'
              AND MONTH(Created_Time) = MONTH(NOW());

            IF (@count >= maximum_withdrawals) THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No more withdrawals this month';
            END IF;
        END IF;
    END IF;
END $$

CREATE TRIGGER mark_loan_paid
    AFTER UPDATE
    ON `Installment`
    FOR EACH ROW
BEGIN
    IF (NEW.Status = 'PAID') THEN
        IF (SELECT COUNT(*) FROM Installment WHERE Loan_ID = NEW.Loan_ID AND Status <> 'PAID') = 0 THEN
            UPDATE Loan SET Status = 'PAID' WHERE ID = NEW.Loan_ID;
        END IF;
    END IF;
END $$

DELIMITER ;
