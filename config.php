<?php

define('DEV_BASE_URL', $_SERVER['HTTP_HOST'].'/cmsfree');

define('BASE_URL', '');

define('ROOT', $_SERVER['HTTP_HOST'].BASE_URL);

$devConnection = 'host=localhost port=5432 dbname=cmsfree user=postgres password=root';

$connection = 'host=localhost port=5432 dbname=cms.special user=postgres password=YB8OG9DE';

define('CONNECTION_STRING', $devConnection);