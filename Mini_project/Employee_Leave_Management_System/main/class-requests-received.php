<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['emplogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['respond'])) {
        $request_id = $_POST['request_id'];
        $status = $_POST['status'];
        $response_description = !empty($_POST['response_description']) ? $_POST['response_description'] : 'None';

        $sql = "UPDATE tblclassrequests SET status=:status, response_description=:response_description WHERE id=:request_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':response_description', $response_description, PDO::PARAM_STR);
        $query->bindParam(':request_id', $request_id, PDO::PARAM_INT);

        if ($query->execute()) {
            $_SESSION['success_message'] = "Response submitted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to submit the response. Please try again.";
        }

        header('location: class-requests-received.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Requests Received</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
    <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <style>
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
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
        .response-form {
            display: none;
            margin-top: 10px;
        }
        .edit-button {
            margin: 5px;
        }
        .success-message, .error-message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>

    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Requests Received</div>
            </div>
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Requests List</span>

                        <!-- Success message -->
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="success-message">
                                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Error message -->
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="error-message">
                                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                            </div>
                        <?php endif; ?>

                        <table class="responsive-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Requester Name</th>
                                    <th>Requester Email</th>
                                    <th>Posted On</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $eid = $_SESSION['eid'];
                                $sql = "SELECT * FROM tblclassrequests WHERE requested_emp_id=:eid ";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':eid', $eid, PDO::PARAM_INT);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                $cnt = 1;

                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) {
                                ?>
                                <tr>
                                    <td><?php echo htmlentities($cnt); ?></td>
                                    <td><?php echo htmlentities($result->requester_name); ?></td>
                                    <td><?php echo htmlentities($result->requester_email); ?></td>
                                    <td><?php echo htmlentities($result->submission_date); ?></td>
                                    <td><?php echo htmlentities($result->from_date); ?></td>
                                    <td><?php echo htmlentities($result->to_date); ?></td>
                                    <td><?php echo htmlentities($result->status); ?></td>
                                    <td><?php echo htmlentities($result->response_description); ?></td>
                                    <td>
                                        <button class="btn waves-effect waves-light edit-button" onclick="showResponseForm(<?php echo $result->id; ?>)">Edit</button>
                                        <form method="POST" class="response-form" id="response-form-<?php echo $result->id; ?>">
                                            <input type="hidden" name="request_id" value="<?php echo htmlentities($result->id); ?>">
                                            <select name="status" required>
                                                <option value="" disabled>Select Status</option>
                                                <option value="Pending" <?php if ($result->status == 'Pending') echo 'selected'; ?>>Pending</option>
                                                <option value="Accepted" <?php if ($result->status == 'Accepted') echo 'selected'; ?>>Accepted</option>
                                                <option value="Rejected" <?php if ($result->status == 'Rejected') echo 'selected'; ?>>Rejected</option>
                                            </select>

                                            <textarea name="response_description" rows="2" placeholder="Add a description (optional)"><?php echo htmlentities($result->response_description); ?></textarea>
                                            <button type="submit" name="respond" class="btn waves-effect waves-light">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php $cnt++; } } else { ?>
                                <tr>
                                    <td colspan="8">No requests found</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
    <script src="assets/plugins/materialize/js/materialize.min.js"></script>
    <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
    <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="assets/js/alpha.min.js"></script>
    <script>
        function showResponseForm(requestId) {
            var form = document.getElementById('response-form-' + requestId);
            form.style.display = (form.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</body>
</html>
<?php } ?>
