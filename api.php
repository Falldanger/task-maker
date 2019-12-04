<?php

include_once 'controllers\connectionController.php';
include_once 'controllers\indexController.php';

use controllers\Connection;
use controllers\connectionController;
use controllers\indexController;

$connection = new connectionController();
$manipulations= new indexController(new Connection());

if(isset($_POST['add'])&&$_POST['add']==1){
    $data['performerName']=$_POST['serialize'][0]['value'];
    $data['email']=$_POST['serialize'][1]['value'];
    $data['datepicker']=$_POST['serialize'][2]['value'];
    $data['taskName']=$_POST['serialize'][3]['value'];
    $data['taskDescription']=$_POST['serialize'][4]['value'];


    $test='';
    $response["status"]=200;
    $response["statusText"]='';

    if (!empty($data['performerName'])) {
        if (strlen($data['performerName']) < 2) {
            $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
            $response["statusText"] = "\nIncorrect performer name!";
        }
        if (is_numeric($data['performerName'])) {
            $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
            $response["statusText"] = $response["statusText"]."\nPerformer FIO can not be nummeric!";
        }
    } else {
        $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
        $response["statusText"] = $response["statusText"]."\nPerformer FIO require!";
    }
    if (!empty($data['email'])) {
        if (strripos($data['email'], '@') === false) {
            $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
            $response["statusText"] = $response["statusText"]."\nThe link must contain a character \'@\'";
        } elseif (!preg_match('/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i', $data['email'])) {
            $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
            $response["statusText"] = $response["statusText"]."\nIncorrect email!";
        }
    } else {
        $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
        $response["statusText"] = $response["statusText"]."\nEmail require!";
    }
    if (!empty($data['datepicker'])) {
        if ((strtotime($data['datepicker']) + 86399) < time()) {
            $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
            $response["statusText"] = $response["statusText"]."\nDate less than current or incorrect!";
        }
    } else {
        $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
        $response["statusText"] = $response["statusText"]."\nDate require!";
    }
    if (!empty($data['taskName'])) {
        if ((strlen($data['taskName']) < 5) || (strlen($data['taskName']) > 255)) {
            $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
            $response["statusText"] = $response["statusText"]."\nTask name should be more 4 symbols and less 256!";
        }
        if (is_numeric($data['taskName'])) {
            $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
            $response["statusText"] = $response["statusText"]."\nTask name can not be nummeric!";
        }
    } else {
        $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
        $response["statusText"] = $response["statusText"]."\nTask name require!";
    }
    if (!empty($data['taskDescription'])) {
        if ((strlen($data['taskDescription']) < 5) || strlen($data['taskDescription']) > 1000) {
            $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
            $response["statusText"] = $response["statusText"]."\nTask description should be more 4 symbols and less 1001!";
        }
        if (is_numeric($data['taskDescription'])) {
            $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
            $response["statusText"] = $response["statusText"]."\nDescription can not be nummeric!";
        }
    } else {
        $response["status"] = 422; //Make up your own error codes! Yippee! Fun!
        $response["statusText"] = $response["statusText"]."\nTask description require!";
    }
    if ($response["status"] == 200) {
        if ($manipulations->add($data)) {
            //$manipulations->mailSend();//Send mail if task created
//            $manipulations->handler($connection->getData());
            $test=$connection->getData();
            echo json_encode($test);
        }
    }
    if ($response["status"] == 422) {
        echo json_encode($response);
    }
}
        if (isset($_POST['delete'])&&$_POST['delete']==1) {
            if ($manipulations->delete($_POST['id'])) {
                $test = $connection->getData();
                echo json_encode($test);
            }
        }
//        if (isset($_GET['sortDate'])) {
//            $this->handler($this->sortDate());
//        }
//        if (isset($_POST['search'])) {
//            $this->handler($this->searchData());
//        }
        //
//        $connection = new connectionController();
//        if (!isset($_GET['del']) && !isset($_GET['sortDate']) && !isset($_POST['submit']) && !isset($_POST['search'])) {
//            $this->handler($connection->getData());
//        }
//        if (isset($_POST['submit'])) {
//            $i = 0;
//            if (!empty($_POST['performerName'])) {
//                if (strlen($_POST['performerName']) < 2) {
//                    echo 'Incorrect performer name! ' . '</br>';
//                    $i++;
//                }
//                if (is_numeric($_POST['performerName'])) {
//                    echo 'Performer FIO can not be nummeric! ' . '</br>';
//                    $i++;
//                }
//            } else {
//                echo 'Performer FIO require! ' . '</br>';
//                $i++;
//            }
//            if (!empty($_POST['email'])) {
//                if (strripos($_POST['email'], '@') === false) {
//                    echo "The link must contain a character \'@\'" . '</br>';
//                    $i++;
//                } elseif (!preg_match('/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i', $_POST['email'])) {
//                    echo 'Incorrect email! ' . '</br>';
//                    $i++;
//                }
//            } else {
//                echo 'Email require!' . '</br>';
//                $i++;
//            }
//            if (!empty($_POST['datepicker'])) {
//                if ((strtotime($_POST['datepicker']) + 86399) < time()) {
//                    echo 'Date less than current or incorrect! ' . '</br>';
//                    $i++;
//                }
//            } else {
//                echo 'Date require!' . '</br>';
//                $i++;
//            }
//            if (!empty($_POST['taskName'])) {
//                if ((strlen($_POST['taskName']) < 5) && (strlen($_POST['taskName']) > 255)) {
//                    echo 'Task name should be more 4 symbols and less 256' . '</br>';
//                    $i++;
//                }
//                if (is_numeric($_POST['taskName'])) {
//                    echo 'Task name can not be nummeric! ' . '</br>';
//                    $i++;
//                }
//            } else {
//                echo 'Task name require! ' . '</br>';
//                $i++;
//            }
//            if (!empty($_POST['taskDescription'])) {
//                if ((strlen($_POST['taskDescription']) < 5) && strlen($_POST['taskDescription']) > 1000) {
//                    echo 'Task description should be more 4 symbols and less 1001' . '</br>';
//                    $i++;
//                }
//                if (is_numeric($_POST['taskDescription'])) {
//                    echo 'Description can not be nummeric' . '</br>';
//                    $i++;
//                }
//            } else {
//                echo 'Task description require!' . '</br>';
//                $i++;
//            }
//            if ($i < 1) {
//                if ($this->add()) {
//                    $this->mailSend();//Send mail if task created
//                    $this->handler($connection->getData());
//                }
//            }
//        }
//        if (isset($_GET['del'])) {
//            $this->delete();
//            $this->handler($connection->getData());
//        }
//        if (isset($_GET['sortDate'])) {
//            $this->handler($this->sortDate());
//        }
//        if (isset($_POST['search'])) {
//            $this->handler($this->searchData());
//        }