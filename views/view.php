<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Подключаем Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Подключаем JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="#"/>
</head>
<body>
<div class="container">

    <h1>Task-maker</h1>
    <div class="row">
        <div class="col-md-4">
            <form action="api.php" id="form1" method="post">
                <div class="form-group">
                    <label for="performerName">Performer FIO</label>
                    <input type="text" class="form-control" id="performerName" name="performerName"
                           value="<?php if (isset($_POST['performerName'])) {
                               echo $_POST['performerName'];
                           } ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="text" class="form-control" id="email" name="email" aria-describedby="email"
                           placeholder="Enter email" value="<?php if (isset($_POST['email'])) {
                        echo $_POST['email'];
                    } ?>">
                </div>
                <div class="form-group">
                    <label for="datepicker">Date end</label>
                    <input id="datepicker" name="datepicker" value="<?php if (isset($_POST['datepicker'])) {
                        echo $_POST['datepicker'];
                    } ?>">
                </div>
                <div class="form-group">
                    <label for="taskName">Task name</label>
                    <input type="text" class="form-control" id="taskName" name="taskName"
                           value="<?php if (isset($_POST['taskName'])) {
                               echo $_POST['taskName'];
                           } ?>">
                </div>
                <div class="form-group">
                    <label for="taskDescription">Task description</label>
                    <textarea class="form-control" id="taskDescription" rows="3"
                              name="taskDescription"><?php if (isset($_POST['taskDescription'])) {
                            echo $_POST['taskDescription'];
                        } ?></textarea>
                </div>
                <input type="submit" class="btn btn-primary" id="submit" name="submit" value="Submit">
            </form>
        </div>
        <div class="col-md-8">
            <table style="margin:10px 0;">
                <tr>
                    <span><b>Sorting date: </b></span>
                    <th class="beauty"><a href="index.php?sortDate=created_at" id="created_at" style="color: #0b2e13">Date
                            create</a></th>
                    <th class="beauty"><a href="index.php?sortDate=date" id="dateEnd" style="color: #0b2e13">Date
                            end</a></th>
                </tr>
            </table>
            <form method="post" action="controllers/indexController.php" style="margin-bottom: 10px;">
                <span><b>Search by: </b></span>
                <label for="searchFio"> Performer </label>
                <input type="search" id="searchFio" name="searchFio" value="<?php if (isset($_POST['searchFio'])) {
                    echo $_POST['searchFio'];
                } ?>">
                <label for="searchTaskName"> Task name </label>
                <input type="search" id="searchTaskName" name="searchTaskName"
                       value="<?php if (isset($_POST['searchTaskName'])) {
                           echo $_POST['searchTaskName'];
                       } ?>">
                <input type="submit" name="search" id="search" value="Search" class="btn btn-success"></br>
            </form>
            <div>
                <table id="result">
                    <tr>
                        <th>Performer</th>
                        <th>Email</th>
                        <th>Created_at</th>
                        <th>Date end</th>
                        <th>Task name</th>
                        <th>Task description</th>
                        <th>Delete</th>
                    </tr>
                    <?php

                    use controllers\Connection;
                    use controllers\indexController;

                    if (empty($_GET) && empty($_POST)) {
                        $startView = new indexController(new Connection());
                        $startView->index();
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    //function to delete data
    $(document).ready(function () {
        $('button#delete,#delete').on("click",function (event) {
            var id = this.name;
            console.log(id);
            event.preventDefault();
            del({'delete': 1,'id':id});
        });
    });

    function del(data) {
        $.ajax({
            url: 'api.php',
            type: "POST",
            data: data,
            dataType: "text",
            error: error,
            success: success
        });
    }
    //function to add data
    $(document).ready(function () {
        $('#form1').submit(function (event) {
            var serialize = $('#form1').serializeArray();
            console.log({serialize, 'add': 1});
            event.preventDefault();
            add({serialize, 'add': 1});
        });
    });

    function add(data) {
        $.ajax({
            url: 'api.php',
            type: "POST",
            data: data,
            dataType: "text",
            error: error,
            success: success
        });
    }

    function error() {
        alert('error');
    }

    function success(result) {
        var newresult = $.parseJSON(result);
        console.log(newresult);

        if (typeof newresult.status !== "undefined" && newresult.status !== 200) // Status code 200 represents Success/OK
        {
            alert("Unprocessable Entity " + newresult.status + '!' + newresult.statusText);
        }
        else {
            console.log(newresult);
            var arrId = [];
            var str = '';
            var str1 = '<tr>';
            var th = '<tr><th>Performer</th>' + '<th>Email</th>' + '<th>Created_at</th>' + '<th>Date end</th>' +
                '<th>Task name</th>' + '<th>Task description</th>' + '<th>Delete</th></tr>';
            for (var i = 0; i < newresult.length; i++) {
                str = newresult[i];
                for (var n in str) {
                    if (n === 'created_at' || n === 'date') {
                        var date = new Date(str[n]);
                        str[n] = ('0' + date.getDate()).slice(-2) + '.' + ('0' + (date.getMonth() + 1)).slice(-2) + '.' + date.getFullYear();
                    }
                    if (n === 'id') {
                        str1 += '';
                        arrId.push(str[n]);
                    } else {
                        str1 += '<td>' + str[n] + '</td>';
                    }
                }
                str1 += "<td><button id='delete'"+" name="+"\""+arrId[i]+"\""+">delete</button></td></tr>";
            }
            $('#result').empty();
            $('#result').append(th).append(str1);
        }
    }

    // $('#submit').click(function () {
    //     var serialize = $('input,textarea').serializeArray();
    //     let arr = [];
    //     for (var i = 0; i < serialize.length; i++) {
    //         arr[i] = serialize[i]['value'];
    //     }
    //     console.log(arr);
    //     $.ajax({
    //         url: 'controllers/indexController.php',
    //         type: 'POST',
    //         data: {
    //             'data': arr,
    //         },
    //         success: function (data) {
    //             $('#success').html(data);
    //             alert('Task sent to mail');
    //             console.log(data);
    //         }
    //     });
    // });
    //
    // $('#search').click(function () {
    //     var serialize = $('input').serializeArray();
    //     let arr = [];
    //     for (var i = 0; i < serialize.length; i++) {
    //         arr[i] = serialize[i]['value'];
    //     }
    //     console.log(arr);
    //     $.ajax({
    //         url: 'controllers/indexController.php',
    //         type: 'POST',
    //         data: {
    //             'data': arr,
    //         },
    //         success: function (data) {
    //             $('#success').html(data);
    //             console.log(data);
    //         }
    //     });
    // });
    // $('#created_at,#dateEnd,#delete').click(function () {
    //     var serialize = $('a').serializeArray();
    //     let arr = [];
    //     for (var i = 0; i < serialize.length; i++) {
    //         arr[i] = serialize[i]['value'];
    //     }
    //     console.log(arr);
    //     $.ajax({
    //         url: 'controllers/indexController.php',
    //         type: 'GET',
    //         data: {
    //             'data': arr,
    //         },
    //         success: function (data) {
    //             $('#success').html(data);
    //             console.log(data);
    //         }
    //     });
    // });

    $('#datepicker').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'dd.mm.yyyy'
    });
</script>
</body>
</html>
