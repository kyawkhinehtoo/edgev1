UPDATE customers
JOIN (
    SELECT id, FLOOR(RAND() * (200 - 5 + 1) + 5) AS new_bandwidth
    FROM customers
    WHERE bandwidth = 0
) AS rnd
ON customers.id = rnd.id
SET customers.bandwidth = rnd.new_bandwidth;