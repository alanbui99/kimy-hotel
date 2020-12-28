-- Hotel management system - Thang (Alan) Bui

-- 1. How many rooms are booked/occupied today?
SELECT COUNT(*) occupied 
FROM occupancies 
WHERE Date = CURRENT_DATE()


-- 2. How many check-ins are expected today?
SELECT COUNT(*) expected 
FROM reservations 
WHERE StartDate = CURDATE();

-- 3. How many check-outs are expected today?
SELECT COUNT(*) expected 
FROM reservations 
WHERE EndDate = CURDATE();

-- 4. How much revenue is expected for today?
SELECT SUM(rt.Rate) revenue
FROM occupancies o, roomtypes rt, rooms r
WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code
AND o.Date = CURRENT_DATE();


-- 5. Which rooms are available for a specific room type on a specific date?
SELECT ro.Number
FROM rooms ro, roomtypes rt
WHERE ro.TypeCode = rt.Code
AND rt.Style = '$type'
AND ro.Number NOT IN (
    SELECT r.Number
    FROM occupancies o, rooms r
    WHERE o.RoomNo = r.Number
    AND o.Date IN ($date)
);

-- 6. How many reservations each booking sources created?
SELECT Source, COUNT(*) count 
FROM reservations 
GROUP BY Source

-- 7. What are all reservations(happening now or in the future) a specific customer (given their first & last name) has scheduled?
SELECT r.ID, r.StartDate, r.EndDate, r.BookedAt, r.NumGuests, o.RoomNo, r.Status
FROM customers c, reservations r, occupancies o
WHERE c.ID = r.CustomerID AND o.ResID = r.ID
AND Fname = '$firstName' AND Lname='$lastName'
AND EndDate >= CURDATE()
AND r.Status NOT IN ('checked-out', 'canceled') 
GROUP BY r.ID;


-- 8. How much money is a specific reservation worth (group by )?
SELECT SUM(rt.Rate) 
FROM occupancies o, rooms r, roomtypes rt
WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code
AND o.ResID = $resID;

-- 9. How many rooms have been sold for each room type?

SELECT rt.Style, COUNT(*) Count
FROM occupancies o, rooms r, roomtypes rt
WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code
GROUP BY rt.Style
ORDER BY rt.Style;

-- 10. What is the average revenue per customer?
SELECT ROUND(SUM(rt.Rate)/(SELECT COUNT(*) FROM customers), 0) AS revPerCus
FROM occupancies o, rooms r, roomtypes rt
WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code;

-- 11. What is the average number of stays per customer?
SELECT ROUND((SELECT COUNT(*) FROM reservations)/(SELECT COUNT(*) FROM customers), 2) staysPerCus;

-- 12. What is the average length per stay?
SELECT ROUND((SELECT COUNT(*) FROM occupancies)/(SELECT COUNT(*) FROM reservations), 2) lenPerStay;

-- 13. What is the number of return customers?
SELECT COUNT(*) 'return' 
FROM customers 
WHERE ID IN (
    SELECT CustomerID 
    FROM reservations 
    GROUP BY CustomerID 
    HAVING COUNT(*) > 1
);

-- 14. What are the names, number of stays and revenue created for all customers?
SELECT c.ID 'ID', c.Fname 'First name', c.Lname 'Last name', COUNT(r.ID)'Stays', SUM(rt.Rate) 'Revenue created'
FROM customers c, reservations r, occupancies o, rooms ro, roomtypes rt
WHERE c.ID = r.CustomerID AND r.ID = o.ResID AND o.RoomNo = ro.Number AND ro.TypeCode = rt.Code
GROUP BY c.ID;                

-- 15. What is the total revenue generated up until this point?
SELECT SUM(rt.Rate) totalRev
FROM occupancies o, rooms r, roomtypes rt
WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code

-- 16. What are the status and occupant's information (if any) of all rooms on a specific date?
SELECT r.Number, o.Status, c.Fname, c.Lname
FROM rooms r 
LEFT JOIN occupancies o 
ON r.Number = o.RoomNo AND o.Date = '$date' 
LEFT JOIN reservations re 
ON re.ID = o.ResID
LEFT JOIN customers c
ON c.ID = re.CustomerID
ORDER BY r.Number;

-- 17. What is the average number of rooms occupied per day in a certain month?
SELECT ROUND(COUNT(*)/DAY(LAST_DAY('$month-01')), 1) count 
FROM occupancies 
WHERE Date LIKE '$month%';

-- 18. What is the total revenue in a specific day/ month?
SELECT ROUND(SUM(rt.Rate), 0) sum
FROM occupancies o, rooms r, roomtypes rt 
WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code 
AND o.Date LIKE '$date/month%';    

-- 19. How much revenue each type of room has generated?
SELECT rt.Style src, ROUND(SUM(rt.Rate), 0) val 
FROM occupancies o, rooms r, roomtypes rt
WHERE o.RoomNo = r.Number and r.TypeCode = rt.Code
GROUP BY rt.Code;

-- 20. How much revenue each booking source has generated?
SELECT re.Source src, ROUND(SUM(rt.Rate), 0) val 
FROM occupancies o, rooms r, roomtypes rt,  reservations re
WHERE o.RoomNo = r.Number and r.TypeCode = rt.Code AND re.ID = o.ResID
GROUP BY re.Source;

-- 21. What is the average revenue per stay in a specific day/month?
SELECT ROUND(SUM(rt.Rate)/COUNT(DISTINCT o.ResID), 0) val
FROM occupancies o, rooms r, roomtypes rt 
WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code 
AND o.Date LIKE '$date/month%';    

-- 22. What is the average revenue per room in a specific day/month?
SELECT ROUND(SUM(rt.Rate)/(SELECT COUNT(*) FROM rooms), 0) val
FROM occupancies o, rooms r, roomtypes rt 
WHERE o.RoomNo = r.Number AND r.TypeCode = rt.Code 
AND o.Date LIKE '$date/month%';    

