<?php

namespace controllers;

use PDO;

class connectionController
{
    public function getData()
    {
        $db = new QueryBuilder(Connection::make());
        $tasks = $db->select('tasks');
        return $tasks;
    }
}

class Connection
{
    public static function make()
    {
        return $db = new PDO("mysql:host=localhost; dbname=task-maker", 'root', '');
    }
}

class QueryBuilder
{
    private $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function select($table)
    {
        $statement = $this->db->query("SELECT * FROM $table");
        $res = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }
}