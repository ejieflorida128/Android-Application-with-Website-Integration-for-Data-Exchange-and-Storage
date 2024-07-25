<?php
include('db_connect.php');

   if($_SERVER['REQUEST_METHOD'] == "POST"){
    $checkallDataCurrentId = "SELECT * FROM users";
    $checkId = mysqli_query($connForDatabase,$checkallDataCurrentId);

    $id = "";

    while($test = mysqli_fetch_assoc($checkId)){
            $id = $test['Id'];
    }

    $getId = $id + 1;

    $getName = $_POST['InputName'];

    $newdATA = "INSERT INTO users (Id,Name) VALUES ('$getId','$getName')";
    mysqli_query($connForDatabase,$newdATA);

        header("Location: ../SQLITEMYSQLSYNC/loadingBackk.php");
   }

    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD NEW DATA</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <style>
        *{
            margin: 5px;
        }
    </style>
</head>
<body>


<div class="container">
    <div class="box" style = "width: 500px; box-shadow: 0 4px 8px rgba(4, 4, 4, 1.1); border-radius: 20px; margin: 40px; padding: 50px; margin-left: 280px; margin-top: 130px;">
    <h2>Add new name!</h2>
        <form action="newData.php" method = "post"> 
        <label for="InputName">Please enter name: </label>
        <input type="text" id = "InputName" name = "InputName" class = "form-control">
        <input type="submit" class = "btn btn-success" value = "ADD DATA" style = "margin-top: 50px; box-shadow: 0 4px 8px rgba(4, 4, 4, 1.1);">
        </form>
    </div>
</div>
    
    
</body>
</html>