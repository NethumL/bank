CREATE TABLE `Branch`
(
    `ID`      varchar(36)  NOT NULL,
    `Address` varchar(100) NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE `User`
(
    `ID`           varchar(36)                              NOT NULL,
    `Username`     varchar(30) UNIQUE                       NOT NULL,
    `Password`     varchar(256)                             NOT NULL,
    `Name`         varchar(50)                              NOT NULL,
    `User_Type`    enum ('MANAGER', 'EMPLOYEE', 'CUSTOMER') NOT NULL,
    `Phone_Number` varchar(10)                              NOT NULL,
    `DOB`          date                                     NOT NULL,
    `Address`      varchar(100)                             NOT NULL,
    PRIMARY KEY (`ID`)
);

CREATE TABLE `Savings_Plan`
(
    `ID`              int            NOT NULL AUTO_INCREMENT,
    `Interest_Rate`   int            NOT NULL,
    `Minimum_Balance` decimal(15, 2) NOT NULL,
    `Minimum_Age`     int            NOT NULL,
    `Maximum_Age`     int            NOT NULL,
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
    FOREIGN KEY (`User_ID`) REFERENCES `User` (`ID`)
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
    `Transaction_ID` varchar(36)                     NOT NULL,
    `From`           varchar(20)                     NOT NULL,
    `To`             varchar(20)                     NOT NULL,
    `Type`           enum ('WITHDRAWAL', 'TRANSFER') NOT NULL,
    `Amount`         decimal(15, 2)                  NOT NULL,
    `Created_Time`   timestamp                       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Description`    text                            NULL     DEFAULT NULL,
    PRIMARY KEY (`Transaction_ID`),
    FOREIGN KEY (`From`) REFERENCES `Account` (`Account_Number`)
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
    FOREIGN KEY (`Plan_ID`) REFERENCES `FD_Plan` (`ID`)
);

CREATE TABLE `Loan`
(
    `ID`           varchar(36)                          NOT NULL,
    `User_ID`      varchar(36)                          NOT NULL,
    `Loan_Type`    enum ('PERSONAL', 'BUSINESS')        NOT NULL,
    `Status`       enum ('CREATED', 'APPROVED', 'PAID') NOT NULL,
    `Created_Time` timestamp                            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Amount`    decimal(15,2)                        NOT NULL,
    `Loan_Mode` enum ('NORMAL', 'ONLINE')        NOT NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`User_ID`) REFERENCES `User` (`ID`)
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
    `FD_ID`     varchar(20)                 NOT NULL,
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
    FOREIGN KEY (`Loan_ID`) REFERENCES `Loan` (`ID`)
);