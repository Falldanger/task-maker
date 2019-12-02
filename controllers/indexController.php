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

        if (!isset($_GET['del']) && !isset($_GET['sortDate']) && !isset($_POST['submit']) && !isset($_POST['search'])) {
            $this->handler($connection->getData());
        }

        if (isset($_POST['submit'])) {
            $i = 0;
            if (!empty($_POST['performerName'])) {
                if (strlen($_POST['performerName']) < 2) {
                    echo 'Incorrect performer name! ' . '</br>';
                    $i++;
                }
                if (is_numeric($_POST['performerName'])) {
                    echo 'Performer FIO can not be nummeric! ' . '</br>';
                    $i++;
                }
            } else {
                echo 'Performer FIO require! ' . '</br>';
                $i++;
            }
            if (!empty($_POST['email'])) {
                if (strripos($_POST['email'], '@') === false) {
                    echo "The link must contain a character \'@\'" . '</br>';
                    $i++;
                } elseif (!preg_match('/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i', $_POST['email'])) {
                    echo 'Incorrect email! ' . '</br>';
                    $i++;
                }
            } else {
                echo 'Email require!' . '</br>';
                $i++;
            }
            if (!empty($_POST['datepicker'])) {
                if ((strtotime($_POST['datepicker']) + 86399) < time()) {
                    echo 'Date less than current or incorrect! ' . '</br>';
                    $i++;
                }
            } else {
                echo 'Date require!' . '</br>';
                $i++;
            }
            if (!empty($_POST['taskName'])) {
                if ((strlen($_POST['taskName']) < 5) && (strlen($_POST['taskName']) > 255)) {
                    echo 'Task name should be more 4 symbols and less 256' . '</br>';
                    $i++;
                }
                if (is_numeric($_POST['taskName'])) {
                    echo 'Task name can not be nummeric! ' . '</br>';
                    $i++;
                }
            } else {
                echo 'Task name require! ' . '</br>';
                $i++;
            }
            if (!empty($_POST['taskDescription'])) {
                if ((strlen($_POST['taskDescription']) < 5) && strlen($_POST['taskDescription']) > 1000) {
                    echo 'Task description should be more 4 symbols and less 1001' . '</br>';
                    $i++;
                }
                if (is_numeric($_POST['taskDescription'])) {
                    echo 'Description can not be nummeric' . '</br>';
                    $i++;
                }
            } else {
                echo 'Task description require!' . '</br>';
                $i++;
            }
            if ($i < 1) {
                if ($this->add()) {
                    $this->mailSend();//Send mail if task created
                    $this->handler($connection->getData());
                }
            }
        }
        if (isset($_GET['del'])) {
            $this->delete();
            $this->handler($connection->getData());
        }
        if (isset($_GET['sortDate'])) {
            $this->handler($this->sortDate());
        }
        if (isset($_POST['search'])) {
            $this->handler($this->searchData());
        }
    }

    public function add()
    {
        $date = str_replace('.', '-', $_POST['datepicker']);
        $date_time = date('Y-m-d', strtotime($date));

        $query = "INSERT INTO `tasks` (`fio`, `email`,`date`,`taskname`,`taskdescription`)
        VALUES ('{$_POST['performerName']}', '{$_POST['email']}', '{$date_time}', '{$_POST['taskName']}', '{$_POST['taskDescription']}')";

        $result = $this->db->prepare($query)->execute();

        return $result;
    }

    public function delete()
    {
        $id = openssl_decrypt($_GET['del'], 'AES-128-CTR',
            'GeeksforGeeks', 0, '1234567891011121');
        $query = "DELETE FROM `tasks` WHERE id=$id";
        $result = $this->db->prepare($query)->execute();

        return $result;
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

    public function handler($method)
    {
        $tableMiddlePart = '';
        foreach ($method as $data) {
            $secretId = openssl_encrypt($data['id'], 'AES-128-CTR',
                'GeeksforGeeks', 0, '1234567891011121');

            $tableMiddlePart .= '<tr><td>' . $data['fio'] . '<td>' . $data['email'] . '</td>' . '<td>' . date('d.m.Y', strtotime($data['created_at'])) . '</td>'
                . '<td>' . date('d.m.Y', strtotime($data['date'])) . '</td>' . '<td>' . $data['taskname']
                . '</td>' . '<td>' . $data['taskdescription'] . '</td>'
                . "<td><a href='?del={$secretId}' id='delete'>delete</a></td>" . '</tr>';
        }
        $table = '<table id="success">
                <tr>
                    <th>Performer</th>
                    <th>Email</th>
                    <th>Created_at</th>
                    <th>Date end</th>
                    <th>Task name</th>
                    <th>Task description</th>
                    <th>Delete</th>
                </tr>' . $tableMiddlePart . '</table>';
        echo $table;
    }
}