<?php
use Opis\Database\Database;
use Opis\Database\Connection;

$connection = new Connection(
    'mysql:host=localhost;dbname=test',
    'username',
    'password'
);

$db = new Database($connection);