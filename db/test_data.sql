INSERT INTO `user`
    (`id`, `username`, `password`, `balance`)
VALUES
    (1, 'user1', '$2y$10$wzJNo0myna7.NXYDeT8s7ONV1Ow6GXq5yP7zpwIw4Ko2rYK6z6NSe', 30000),
    (2, 'user2', '$2y$10$wzJNo0myna7.NXYDeT8s7ONV1Ow6GXq5yP7zpwIw4Ko2rYK6z6NSe', 20000),
    (3, 'user3', '$2y$10$wzJNo0myna7.NXYDeT8s7ONV1Ow6GXq5yP7zpwIw4Ko2rYK6z6NSe', 10000),
    (4, 'user4', '$2y$10$wzJNo0myna7.NXYDeT8s7ONV1Ow6GXq5yP7zpwIw4Ko2rYK6z6NSe', 0),
    (5, 'user5', '$2y$10$wzJNo0myna7.NXYDeT8s7ONV1Ow6GXq5yP7zpwIw4Ko2rYK6z6NSe', -10000);

INSERT INTO `amount_transaction`
    (`user_id`, `amount`, `balance`)
VALUES
    (1, 10000, 10000),
    (1, 10000, 20000),
    (1, 10000, 30000),

    (2, 10000, 10000),
    (2, 5000,  15000),
    (2, 5000,  20000),

    (3, 5000, 5000),
    (3, 5000, 10000),

    (5, 5000, 5000),
    (5, -10000, -5000),
    (5, -5000, -5000);
