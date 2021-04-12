<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "test";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>
<?php
$table_id = 'id';
$table = 'test';
$fields = array(
    'id' => array(
        'title' => '#',
        'type' => 'static'
    ),
    'first' => array(
        'title' => 'First',
        'type' => 'text'
    ),
    'last' => array(
        'title' => 'Last',
        'type' => 'text'
    ),
    'handle' => array(
        'title' => 'Handle',
        'type' => 'text'
    )
);

$act = isset($_POST['act']) ? $_POST['act'] : null;

if($_POST) {
    if($act) {
        foreach($fields as $key => $field) {
            if(isset($_POST[$key]) && $_POST[$key] != '') {
                ${$key} = $_POST[$key];
            }
        }
        if($act == 'create') {
            $sql = "INSERT INTO " . $table . "(";
            $len = 0;
            foreach($fields as $key => $field) {
                if(isset(${$key})) {
                    $len++;
                }
            }
            $i = 0;
            foreach($fields as $key => $field) {
                if(isset(${$key})) {
                    $sql .= $key;
                    if($i != $len - 1) {
                        $sql .= ", ";
                    } else {
                        $sql .= ") VALUES (";
                    }
                    $i++;
                }
            }
            $i = 0;
            foreach($fields as $key => $field) {
                if(isset(${$key})) {
                    $sql .= "'" . ${$key} . "'";
                    if($i != $len - 1) {
                        $sql .= " ,";
                    } else {
                        $sql .= ")";
                    }
                    $i++;
                }
            }
            mysqli_query($conn, $sql);
        } elseif ($act == 'edit') {
            $sql = "UPDATE " . $table . " SET ";
            $len = 0;
            foreach($fields as $key => $field) {
                if(isset(${$key})) {
                    $len++;
                }
            }
            $i = 0;
            foreach($fields as $key => $field) {
                if(isset(${$key})) {
                    $sql .= $key . " = '" . ${$key} . "'";
                    if($i != $len - 1) {
                        $sql .= ", ";
                    } else {
                        $sql .= " WHERE " . $table_id . " = " . $id;
                    }
                    $i++;
                }
            }
            mysqli_query($conn, $sql);
        }
    }
}

$act = isset($_GET['act']) ? $_GET['act'] : null;
if($_GET) {
    if($act == 'delete') {
        $id = $_GET['id'];
        $sql = "DELETE FROM " . $table . " WHERE " . $table_id . " = " . $id;
        mysqli_query($conn, $sql);

    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row mb-3">
            <div class="card">
                <div class="row card-header">
                    <form method="GET" class="text-end">
                        <button class="btn btn-primary" name="create" value="1">Criar</button>
                    </form>
                </div>
                <?php
                if(isset($_GET['create'])) {
                    $id = isset($_GET['id']) ? $_GET['id'] : null;
                    if($id) {
                        $sql = "SELECT * FROM " . $table . " WHERE " . $table_id . " = " . $id . " LIMIT 1";
                        $item = mysqli_fetch_array(mysqli_query($conn, $sql));
                    }
                ?>
                <div class="card-body">
                    <form method="POST" class="row">
                    <?php
                    foreach($fields as $key => $field) {
                        if ($field['type'] == 'static') {
                            continue;
                        }
                    ?>
                        <div class="col-md-6 mb-3">
                            <label for="<?php echo $key; ?>" class="form-label"><?php echo $field['title']; ?></label>
                            <input type="<?php echo $field['type']; ?>" class="form-control" name="<?php echo $key; ?>" value="<?php echo isset($item[$key]) ? $item[$key] : ''; ?>">
                        </div>
                    <?php } ?>
                        <?php
                        if($id) {
                        ?>
                        <input type="hidden" name="id" value="<?php echo $id;?>">
                        <?php } ?>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary" name="act" value="<?php echo isset($id) ? 'edit' : 'create' ?>"><?php echo isset($id) ? 'Editar' : 'Criar' ?></button>
                        </div>
                    </form>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <?php
                                    foreach($fields as $field) {
                                    ?>
                                    <th scope="col"><?php echo $field['title']; ?></th>
                                    <?php
                                    }
                                    ?>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <tr>
                                    <?php //echo $sql; ?>
                                </tr> -->
                                <?php
                                $sql = "SELECT * FROM " . $table;
                                $res = mysqli_query($conn, $sql);
                                while ($row = mysqli_fetch_array($res)) {
                                ?>
                                <tr>
                                    <?php
                                    foreach($fields as $key => $field) {
                                    ?>
                                    <td><?php echo $row[$key]; ?></td>
                                    <?php } ?>
                                    <td>
                                        <form method="GET">
                                            <input type="hidden" name="<?php echo $table_id;?>" value="<?php echo $row[$table_id];?>">
                                            <button class="btn btn-info" name="create" value="1">Editar</button>
                                            <button class="btn btn-danger" name="act" value="delete">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
</body>
</html>
