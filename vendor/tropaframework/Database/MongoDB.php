<?php
namespace Tropaframework\Database;
require_once __DIR__ . '/MongoDB/functions.php';
require_once __DIR__ . '/autoload.php';

class MongoDB
{
    function connect()
    {
        $client = new \MongoDB\Client(
            'mongodb://10.0.1.233:27017');
        
        return $client;
        
    }
}