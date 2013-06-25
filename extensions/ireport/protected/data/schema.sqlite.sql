
CREATE TABLE IF NOT EXISTS sample1 (
  no INTEGER PRIMARY KEY AUTOINCREMENT,
  date date NOT NULL,
  itemname varchar(30) NOT NULL,
  qty int(11) NOT NULL,
  uom varchar(10) NOT NULL
) ;



INSERT INTO sample1 (no, date, itemname, qty, uom) VALUES (1, '2009-08-11', 'Sample 1', 10, 'PCS');
INSERT INTO sample1 (no, date, itemname, qty, uom) VALUES (2, '2009-08-26', '???', 2, 'PCS');
INSERT INTO sample1 (no, date, itemname, qty, uom) VALUES (3, '2009-08-15', 'LCD Monitor', 1, 'PCS');
INSERT INTO sample1 (no, date, itemname, qty, uom) VALUES (4, '2009-08-11', 'test item 3', 3, 'pcs');
INSERT INTO sample1 (no, date, itemname, qty, uom) VALUES (6, '2009-08-11', 'Again, sample data', 8, 'day');


CREATE TABLE IF NOT EXISTS sample2 (
  date date NOT NULL,
  docno varchar(20) NOT NULL,
  companyname varchar(30) NOT NULL,
  amount decimal(12,2) NOT NULL,
  terms varchar(20) NOT NULL,
  address text NOT NULL,
  id INTEGER PRIMARY KEY AUTOINCREMENT
) ;


INSERT INTO sample2 (date, docno, companyname, amount, terms, address, id) VALUES
('2009-08-12', 'PO1001', 'Company 1', 100.00, 'C.O.D', '222, Street XXX,\r\nXXXX, XXXX,\r\nMalaysia', 1);
INSERT INTO sample2 (date, docno, companyname, amount, terms, address, id) VALUES
('2009-08-22', 'PO1002', 'Company 2', 300.00, '30 Days', '11, Street YYYY,\r\nYYYYY YYYYY\r\nSingapore', 2);


CREATE TABLE IF NOT EXISTS sample2line (
  no int(11) NOT NULL,
  itemname varchar(40) NOT NULL,
  qty int(11) NOT NULL,
  unitprice decimal(12,2) NOT NULL,
  uom varchar(10) NOT NULL,
  amount decimal(12,2) NOT NULL,
  headerid int(11) NOT NULL,
  lineid INTEGER PRIMARY KEY AUTOINCREMENT,
  linedesc text NOT NULL
) ;


INSERT INTO sample2line (no, itemname, qty, unitprice, uom, amount, headerid, lineid, linedesc) VALUES
(1, 'LCD Monitor', 3, 300.00, 'PCS', 900.00, 1, 1, '* Samsung (SN:12345)\r\n* HP (SN: 2323434)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* Samsung (SN:12345)\r\n* HP (SN: 2323434)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* Samsung (SN:12345)\r\n* HP (SN: 2323434)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* Samsung (SN:12345)\r\n* HP (SN: 2323434)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* Samsung (SN:12345)\r\n* HP (SN: 2323434)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* Samsung (SN:12345)\r\n* HP (SN: 2323434)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)\r\n* ACER (SN:xxxxx)');
INSERT INTO sample2line (no, itemname, qty, unitprice, uom, amount, headerid, lineid, linedesc) VALUES
(2, 'Optical Mouse', 4, 1.00, 'PCS', 4.00, 1, 2, '* 2nd hand');
INSERT INTO sample2line (no, itemname, qty, unitprice, uom, amount, headerid, lineid, linedesc) VALUES
(1, 'Notebook', 1, 1000.00, 'PCS', 1000.00, 2, 3, '');


