<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['emplogin']) == 0) {
    header('location:index.php');
    exit;
}

if (isset($_POST['submit'])) {
    $requester_id = $_SESSION['eid'];
    
    // Fetch requester details (name and email) based on session ID
    $sql = "SELECT FirstName, LastName, EmailId FROM tblemployees WHERE id = :requester_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':requester_id', $requester_id, PDO::PARAM_INT);
    $query->execute();
    $requester = $query->fetch(PDO::FETCH_OBJ);
    
    if ($requester) {
        $requester_name = $requester->FirstName . ' ' . $requester->LastName;
        $requester_email = $requester->EmailId;
    } else {
        $_SESSION['error_message'] = 'Error fetching your details.';
        header('location: class-request.php');
        exit;
    }

    $requested_emp_id = $_POST['requested_emp_id'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Fetch the name of the requested employee
    $sql = "SELECT FirstName, LastName FROM tblemployees WHERE id = :requested_emp_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':requested_emp_id', $requested_emp_id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    
    if ($result) {
        $requested_emp_name = $result->FirstName . ' ' . $result->LastName;
    } else {
        $_SESSION['error_message'] = 'Error fetching requested employee details.';
        header('location: class-request.php');
        exit;
    }

    $submission_date = date('Y-m-d');
    $status = 'Pending';

    $sql = "INSERT INTO tblclassrequests (requester_id, requester_name, requester_email, requested_emp_id, requested_emp_name, from_date, to_date, submission_date, status) 
            VALUES (:requester_id, :requester_name, :requester_email, :requested_emp_id, :requested_emp_name, :from_date, :to_date, :submission_date, :status)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':requester_id', $requester_id, PDO::PARAM_INT);
    $query->bindParam(':requester_name', $requester_name, PDO::PARAM_STR);
    $query->bindParam(':requester_email', $requester_email, PDO::PARAM_STR);
    $query->bindParam(':requested_emp_id', $requested_emp_id, PDO::PARAM_INT);
    $query->bindParam(':requested_emp_name', $requested_emp_name, PDO::PARAM_STR);
    $query->bindParam(':from_date', $from_date, PDO::PARAM_STR);
    $query->bindParam(':to_date', $to_date, PDO::PARAM_STR);
    $query->bindParam(':submission_date', $submission_date, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->execute();

    // Set success message in session
    $_SESSION['success_message'] = 'Request sent successfully';

    // Redirect to the same page to show the message
    header('location: class-request.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Request to Take Class</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta charset="UTF-8">
    <meta name="description" content="Responsive Principal Dashboard Template" />
    <meta name="keywords" content="principal,dashboard" />
    <meta name="author" content="" />
    <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
    <link href="assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
    <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    
    <style>
h3 {
    text-align: center;
    font-size: 28px;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
}

/* Background image */
body {
    font-family: 'Roboto', sans-serif;
    background: url('assets/images/mountains3.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 20px;
}

/* Dashboard button container */
.dashboard-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    
}

/* Dashboard button styling */
.dashboard-btn {
    padding: 15px 30px;
    background-color: rgb(229, 30, 156);
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    font-size: 18px;
}


.dashboard-btn:hover {
    background-color: rgb(192, 84, 21);
}

/* Form styling */
form {
    width: 50%;
    margin: 0 auto;
    padding: 20px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Labels */
label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

/* Input and button fields */
input[type="text"], input[type="date"], button {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

/* Success message */
.success-message {
    color: green;
    background-color: #e8f5e9;
    padding: 10px;
    border: 1px solid #388e3c;
    border-radius: 5px;
    text-align: center;
    margin-bottom: 15px;
}

/* Table styling */
table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 12px;
    text-align: center;
    border: 1px solid #ddd;
}

th {
    background-color: #1e88e5;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}


    </style>
</head>
<body>

    <!-- Dashboard Button -->
    <a href="dashboard.php" class="dashboard-btn">Dashboard</a>

    <h3>Substitute Employee</h3>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <?php echo $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <form method="POST">
        <label for="requested_emp_id">Search and Select Employee:</label>
        <div class="dropdown">
            <input type="text" id="search_employee" placeholder="Type employee name..." autocomplete="off">
            <input type="hidden" name="requested_emp_id" id="requested_emp_id" required>
            <div class="dropdown-list" id="employee_list"></div>
        </div>

        <div style="display: flex; gap: 50px; margin-bottom: 15px;">
    <div style="flex: 1;">
        <label for="from_date">From Date:</label>
        <input type="date" name="from_date" style="width: 100%;" required>
    </div>
    <div style="flex: 1;">
        <label for="to_date">To Date:</label>
        <input type="date" name="to_date" style="width: 100%;" required>
    </div>
</div>


        <button type="submit" name="submit">Send Request</button>
    </form>

    <h3>Your Requests</h3>
    <table>
    <tr>
        <th>Request ID</th>
        <th>Requester Name</th>
        <th>Requester Email</th>
        <th>Req Emp ID</th>
        <th>Req Emp</th>
        <th>From Date</th>
        <th>To Date</th>
        <th>Req Date</th>
        <th>Status</th>
        <th>Response Description</th>
    </tr>
    <?php
    // Fetch and display all requests made by the logged-in employee
    $eid = $_SESSION['eid'];
    $sql = "SELECT id, requester_name, requester_email, requested_emp_id, requested_emp_name, from_date, to_date, submission_date, status, response_description 
            FROM tblclassrequests WHERE requester_id = :eid ORDER BY submission_date DESC";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            echo "<tr>
                    <td>{$result->id}</td>
                    <td>{$result->requester_name}</td>
                    <td>{$result->requester_email}</td>
                    <td>{$result->requested_emp_id}</td>
                    <td>{$result->requested_emp_name}</td>
                    <td>{$result->from_date}</td>
                    <td>{$result->to_date}</td>
                    <td>{$result->submission_date}</td>
                    <td>{$result->status}</td>
                    <td>{$result->response_description}</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='10'>No requests found</td></tr>";
    }
    ?>
</table>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#search_employee').on('input', function () {
                let query = $(this).val();
                $.ajax({
                    url: 'search-employees.php',
                    method: 'POST',
                    data: { query: query },
                    success: function (data) {
                        if (data.trim().length > 0) {
                            $('#employee_list').html(data).fadeIn();
                        } else {
                            $('#employee_list').html('<div>No employees found</div>').fadeIn();
                        }
                    }
                });
            });

            $('#search_employee').on('focus', function () {
                $.ajax({
                    url: 'search-employees.php',
                    method: 'POST',
                    success: function (data) {
                        if (data.trim().length > 0) {
                            $('#employee_list').html(data).fadeIn();
                        } else {
                            $('#employee_list').html('<div>No employees found</div>').fadeIn();
                        }
                    }
                });
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#employee_list, #search_employee').length) {
                    $('#employee_list').fadeOut();
                }
            });

            $(document).on('click', '.employee-option', function () {
                let emp_id = $(this).data('id');
                let emp_name = $(this).data('name');
                $('#search_employee').val(emp_name);
                $('#requested_emp_id').val(emp_id);
                $('#employee_list').fadeOut();
            });
        });
    </script>
</body>
</html>
