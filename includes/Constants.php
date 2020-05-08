<?php

    define('DB_HOST', 'localhost');
    define('DB_USER', 'findmycoso');
    define('DB_PASSWORD', 'findmycoso');
    define('DB_NAME', 'findmycoso');

    define('USER_CREATED', 101);
    define('USER_EXISTS', 102);
    define('USER_FAILURE', 103);

    define('USER_AUTHENTICATED', 201);
    define('USER_NOT_FOUND', 202);
    define('USER_PASSWORD_MISMATCH', 203);

    define('PASSWORD_CHANGED', 301);
    define('PASSWORD_MISMATCH', 302);
    define('PASSWORD_NOT_CHANGED', 303);

    define('DEVICE_ADDED', 401);
    define('DEVICE_FAILURE', 402);
    define('DEVICE_ALREADY_REGISTERED', 403);
    define('DEVICE_NOT_FOUND', 404);
    define('DEVICE_UPDATED', 405);
    define('DEVICE_UPDATE_FAILURE', 406);
    define('DEVICE_ADDED_TO_FAVORITES', 407);

    define('POSITION_ADDED', 501);
    define('POSITION_FAILURE', 502);

    define('PASSWORD_RESETTED', 601);
    define('PASSWORD_FAULT', 602);
    define('EMAIL_FAULT', 603);

