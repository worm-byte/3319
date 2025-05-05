-- ---------------------------------
-- SCRIPT 2

-- Part 1 SQL Updates -----------------------------------------------
USE assign2db;
SELECT * FROM menuitem;
UPDATE menuitem SET dishname = 'Pasta alla Norma', veggie = 'Y' WHERE menuitemid = 'MHHH';

SELECT * FROM driver;
SELECT * FROM cusorder;
UPDATE cusorder SET deliveryrating = 3 WHERE driverid = (SELECT driverid FROM driver WHERE firstname = 'Marge');

SELECT * FROM menuitem;
SELECT * FROM cusorder;

-- Part 2 SQL Inserts -----------------------------------------------
SELECT * FROM customer;
INSERT INTO customer (cusid, firstname, lastname, cellnum) VALUES ('EGG1', 'Sonic', 'Hedgehog', '5196783257');

SELECT * FROM driver;
INSERT INTO driver (driverid, firstname, lastname, cellnum) VALUES ('H8TR', 'Shadow', 'Hedgehog', '5196966969');

SELECT * FROM menuitem;
INSERT INTO menuitem (menuitemid, dishname, price, caloriecount, veggie) VALUES ('MHMM', 'Ramen', 16.99, 870, 'N');

SELECT * FROM cusorder;
INSERT INTO cusorder (orderid, deladdress, dateplaced, timeplaced, timedelivered,pickuporder, deliveryrating, driverid,cusid) VALUES ('C444', '1234 Green Hill Zone', '2025-08-28', '18:00:00', '19:00:00', 'N', 5, 'H8TR', 'EGG1');

SELECT * FROM overallorder;
INSERT INTO overallorder (orderid, menuitemid, quantity) VALUES ('C444', 'MHMM', 3);

SELECT * FROM customer;
SELECT * FROM driver;
SELECT * FROM menuitem;
SELECT * FROM cusorder;
SELECT * FROM overallorder;

-- Part 3 SQL Queries -----------------------------------------------
-- Query 1
SELECT lastname FROM driver;

-- Query 2
SELECT DISTINCT lastname FROM driver;

-- Query 3
SELECT * FROM menuitem ORDER BY caloriecount;

-- Query 4
SELECT dishname, caloriecount, price FROM menuitem WHERE veggie = 'Y' ORDER BY price;

-- Query 5
SELECT orderid, deladdress, deliveryrating FROM cusorder WHERE driverid IN (SELECT driverid FROM driver WHERE lastname = 'Simpson');

-- Query 6
SELECT firstname, lastname FROM driver WHERE driverid NOT IN (SELECT driverid FROM cusorder WHERE pickuporder = 'N');

-- Query 7
SELECT c.firstname AS cus_firstname, c.lastname AS cus_lastname, co.orderid, co.dateplaced, d.firstname AS driver_firstname, d.lastname AS driver_lastname
FROM cusorder co
LEFT JOIN customer c ON co.cusid = c.cusid
LEFT JOIN driver d ON co.driverid = d.driverid;

-- Query 8
SELECT o.orderid, c.dateplaced, m.dishname, m.price, o.quantity 
FROM overallorder o
JOIN menuitem m ON o.menuitemid = m.menuitemid
JOIN cusorder c ON o.orderid = c.orderid
ORDER BY o.orderid;

-- Query 9
SELECT firstname, lastname, COUNT(co.orderid) AS delivery_count
FROM driver d
LEFT JOIN cusorder co ON d.driverid = co.driverid
GROUP BY d.firstname, d.lastname
UNION
SELECT firstname, lastname, 0 AS delivery_count
FROM driver
WHERE driverid NOT IN (SELECT driverid FROM cusorder)
ORDER BY delivery_count DESC;

-- Query 10
SELECT co.orderid, mi.dishname, oo.quantity,
    CONCAT('$', FORMAT(mi.price, 2)) AS price_per_item,
    CONCAT('$', FORMAT(oo.quantity * mi.price, 2)) AS total_cost_for_this_item
FROM cusorder co
JOIN overallorder oo ON co.orderid = oo.orderid
JOIN menuitem mi ON oo.menuitemid = mi.menuitemid
WHERE co.deladdress LIKE '20 Main Street'
GROUP BY co.orderid, oo.quantity, mi.price, mi.dishname
ORDER BY co.orderid;

-- Query 11
SELECT c.firstname, c.lastname, co.orderid, mi.dishname
FROM cusorder co
JOIN customer c ON co.cusid = c.cusid
JOIN overallorder oo ON co.orderid = oo.orderid
JOIN menuitem mi ON oo.menuitemid = mi.menuitemid
WHERE co.orderid NOT IN (
    SELECT oo.orderid
    FROM overallorder oo
    JOIN menuitem mi ON oo.menuitemid = mi.menuitemid
    WHERE mi.veggie = 'N'
);

-- Query 12
SELECT 
    c.firstname, 
    c.lastname, 
    SUM(oo.quantity * mi.price) AS overall_total
FROM 
    overallorder oo
JOIN 
    menuitem mi ON oo.menuitemid = mi.menuitemid
JOIN 
    cusorder co ON oo.orderid = co.orderid
JOIN 
    customer c ON co.cusid = c.cusid
GROUP BY 
    oo.orderid, c.firstname, c.lastname
ORDER BY 
    overall_total DESC
LIMIT 1;

-- Query 13
SELECT d.firstname, d.lastname
FROM driver d
WHERE d.driverid NOT IN (
    SELECT DISTINCT co.driverid
    FROM cusorder co
    WHERE co.orderid IN (
        SELECT DISTINCT orderid 
        FROM overallorder 
        WHERE menuitemid = (
            SELECT menuitemid 
            FROM menuitem 
            WHERE dishname = 'Beef Lasagna'
        )
    )
);

-- Query 14
SELECT 
    mi.dishname, mi.menuitemid, 
    COUNT(oo.orderid) AS total_orders, 
    SUM(oo.quantity) AS total_quantity
FROM 
    overallorder oo
JOIN 
    menuitem mi ON oo.menuitemid = mi.menuitemid
GROUP BY 
    mi.dishname, mi.menuitemid
HAVING 
    SUM(oo.quantity) >= 6
ORDER BY 
    total_quantity DESC;

-- Query 15 My Query: Order the drivers by rating
SELECT d.firstname, d.lastname, AVG(co.deliveryrating) AS avg_rating
FROM driver d
JOIN cusorder co ON d.driverid = co.driverid
GROUP BY d.firstname, d.lastname
ORDER BY avg_rating DESC;

-- Part 4 SQL Views/Deletes -----------------------------------------------

CREATE VIEW driverView AS 
SELECT d.driverid, d.firstname, d.lastname, co.deliveryrating, 
    co.orderid, co.timeplaced, co.timedelivered
FROM driver d
JOIN cusorder co ON d.driverid = co.driverid;

SELECT * FROM driverView WHERE TIMESTAMPDIFF(MINUTE, timeplaced, timedelivered) <= 60;

SELECT * FROM driver;
DELETE FROM driver WHERE driverid = 'D666';
SELECT * FROM driver;

SELECT COUNT(*) AS drivercount FROM driver;
DELETE FROM driver WHERE driverid = 'D333';
SELECT COUNT(*) AS drivercount FROM driver;
-- Ned Flanders, D666,  was actually deleted from the database, but Bart Simposon, D333, was not deleted from the database because he had an associated order in the cusorder table.
