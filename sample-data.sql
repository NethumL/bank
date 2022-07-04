-- user table

INSERT INTO `user`
VALUES (
           '22c4fd6a-96d4-47f3-9cec-590ac79a9155',
           'nimantha',
           '$2y$10$U6heiACk37qCtIo4kuUcC.zU.vCOVku.ILVcbrEr7M7VeguXE36D2',
           'Nimantha Cooray',
           'MANAGER',
           '0775316852',
           '1999-11-10',
           '172, Chilaw Road, Negombo'
       );

INSERT INTO `user`
VALUES (
           'd160bf33-4a85-43b4-b713-ae846653b611',
           'nethum',
           '$2y$10$FYLCmxJxEU9B.uivnr37yuzMGMnk5/RIeEGWCXnG.yBNZM6KC.5TS',
           'Nethum Lamahewage',
           'EMPLOYEE',
           '0775365895',
           '1999-11-10',
           '122, Ragama Road, Ja Ela'
       );

INSERT INTO `user`
VALUES (
           'ccdf4450-e455-4c4e-b899-85ad8d8fd4e2',
           'bhanuja',
           '$2y$10$4pNFGVBLFQSHTiqgbK1YhOymTa/JR61M2wxZOHOscW13vUyU7Dwze',
           'Bhanuja Sasanka',
           'CUSTOMER',
           '0778956235',
           '1999-05-10',
           '56, Thodu Road, Kurana'
       );

-- branch table
INSERT INTO `branch`
(ID, Name, Address)
VALUES (
            'dc517d80-82cb-47b4-8bf9-3a82410cec8f',
            'Negombo',
            '35, Barley Road, Negombo'
       );

-- account table
INSERT INTO `account`
(Account_Number, User_ID, Branch_ID, Account_Type, Amount)
VALUES (
            'ABCDEFGHIJ1234567890',
            'ccdf4450-e455-4c4e-b899-85ad8d8fd4e2',
            'dc517d80-82cb-47b4-8bf9-3a82410cec8f',
            'SAVINGS',
            150000
       );

-- savings_plan table
INSERT INTO `savings_plan`
(Interest_Rate, Minimum_Balance, Minimum_Age, Maximum_Age)
VALUES (
            8,
            500,
            18,
            55
       );

-- savings table
INSERT INTO `savings`
VALUES (
            'ABCDEFGHIJ1234567890',
            1
       );

-- fd_plan table
INSERT INTO `fd_plan`
(Duration, Interest_Rate)
VALUES (
            12,
            8
       );

-- fd table
INSERT INTO `fd`
(ID, Account_Number, Plan_ID, Amount)
VALUES (
            '56ba812c-4f4a-408e-a3bb-69ad40c43d07',
            'ABCDEFGHIJ1234567890',
            1,
            15000
       );

INSERT INTO `fd`
(ID, Account_Number, Plan_ID, Amount)
VALUES (
           'f15ae975-235e-46a1-80c5-e29e34a62233',
           'ABCDEFGHIJ1234567890',
           1,
           30000
       );

-- loan table
INSERT INTO `loan`
(ID, User_ID, Loan_Type, Status, Amount, Loan_Mode)
VALUES  (
            'd295e09f-a1c6-40e1-b763-cbe601ea5bc1',
            'ccdf4450-e455-4c4e-b899-85ad8d8fd4e2',
            'PERSONAL',
            'APPROVED',
            '7500',
            'ONLINE'
        );

-- online_loan table
INSERT INTO `online_loan`
VALUES (
            'd295e09f-a1c6-40e1-b763-cbe601ea5bc1',
            '56ba812c-4f4a-408e-a3bb-69ad40c43d07'
       );