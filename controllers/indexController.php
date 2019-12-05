<?php
namespace controllers;
use controllers\Connection;
use controllers\connectionController;
use PDO;

class indexController
{
    private $db;
    public function __construct(Connection $db)
    {
        $this->db = $db->make();
    }
    public function index()
    {
        $connection = new connectionController();
        $tableFirstPart='<tr>
                        <th>Performer</th>
                        <th>Email</th>
                        <th>Created_at</th>
                        <th>Date end</th>
                        <th>Task name</th>
                        <th>Task description</th>
                        <th>Delete</th>
                    </tr>';
            $tableMiddlePart = '';
            foreach ($connection->getData() as $data) {
                $tableMiddlePart .= '<tr><td>' . $data['fio'] . '<td>' . $data['email'] . '</td>' . '<td>' . date('d.m.Y', strtotime($data['created_at'])) . '</td>'
                    . '<td>' . date('d.m.Y', strtotime($data['date'])) . '</td>' . '<td>' . $data['taskname']
                    . '</td>' . '<td>' . $data['taskdescription'] . '</td>'
                    . '<td><button id=delete'.' name='."{$data['id']}".'>delete</button></td></tr>';
            }
            return $tableFirstPart.$tableMiddlePart;
    }
    public function add($data)
    {
        $date = str_replace('.', '-', $data['datepicker']);
        $date_time = date('Y-m-d', strtotime($date));
        $query = "INSERT INTO `tasks` (`fio`, `email`,`date`,`taskname`,`taskdescription`)
        VALUES ('{$data['performerName']}', '{$data['email']}', '{$date_time}', '{$data['taskName']}', '{$data['taskDescription']}')";
        $this->db->prepare($query)->execute();
//        $result = $this->db->prepare($query)->execute();
//        return $result;
        return true;
    }
    public function delete($id)
    {
        $query = "DELETE FROM `tasks` WHERE id=$id";
        $result = $this->db->prepare($query)->execute();
        return true;
    }
    public function mailSend()
    {
        $to = $_POST['email'];
        $subject = "You have a new task";
        $message = '<p><b>Task name:</b>' . $_POST['taskName'] . '</p></br><p><b>Task description: </b>' . $_POST['taskDescription']
            . '</p></br><p><b>Task deadline:</b>' . $_POST['datepicker'] . '</p></br>';
        $headers = "Content-type: text/html; charset=windows-1251 \r\n";
        $headers .= "From: <boss@example.com>\r\n";
        $headers .= "Reply-To: project.manager@example.com\r\n";
        mail($to, $subject, $message, $headers);
    }
    public function sortDate()
    {
        $date = $_GET['sortDate'];
        $query = $this->db->query("SELECT * FROM `tasks` ORDER BY $date DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function searchData()
    {
        $part1 = '\'%' . $_POST['searchFio'] . '%\'';
        $part2 = '\'%' . $_POST['searchTaskName'] . '%\'';
        $query = $this->db->query("SELECT * FROM `tasks` WHERE fio LIKE $part1
         AND taskname LIKE $part2");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}