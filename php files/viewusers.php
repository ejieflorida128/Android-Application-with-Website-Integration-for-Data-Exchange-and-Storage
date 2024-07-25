<?php
    include('db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add and View Users</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .table-container {
            margin-top: 20px;
            max-height: 400px; /* Set maximum height for the table */
            overflow-y: auto; /* Enable vertical scrollbar if content exceeds the max height */
        }
        .sticky-header th {
            position: sticky;
            top: 0;
            background-color: #343a40; /* Set the background color for the sticky header */
            color: white; /* Set the text color for the sticky header */
            z-index: 1; /* Ensure the header remains above other content */
            text-align: center; /* Center align the text within the header cells */
        }
        .table tbody tr td {
            text-align: center; /* Center align the text within the table cells */
        }
    </style>
</head>
<body>

    <div class="container" style="margin-top: 50px;">
        <a href = "loading.php" class="btn btn-primary" style="box-shadow: 0 4px 8px rgba(4, 4, 4, 1.1);">ADD NEW DATA</a>
        <button class="btn btn-success" style="box-shadow: 0 4px 8px rgba(4, 4, 4, 1.1);" onclick="refreshPage()">REFRESH</button>
        <div class="table-container">
            <table class="table table-hovered">
                <thead class="table-dark sticky-header">
                    <tr>
                        <th>ID</th>
                        <th>NAME</th> 
                        <th>DATE OF DATA ALL MODIFIED IN APPLICATION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM users";
                        $query = mysqli_query($connForDatabase, $sql);
                        while($check = mysqli_fetch_assoc($query)){
                            echo '
                                <tr>
                                    <td>'.$check['Id'].'</td>
                                    <td>'.$check['Name'].'</td>
                                    <td>'.$check['date'].'</td>
                                </tr>
                            ';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function refreshPage() {
            location.reload();
        }
    </script>
</body>
</html>
