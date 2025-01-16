<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['emplogin']) == 0) {
    header('location:index.php');
} else {
    $eid = $_SESSION['eid'];

    // Define total leave quotas
    $totalCasual = 10;
    $totalEarned = 15;
    $totalSick = 1;
    $totalRestricted = 5;
    $totalOfficial = 5; 

    // Query to count approved leaves for each type
    $sqlApproved = "SELECT LeaveType, COUNT(*) as UsedCount FROM tblleaves 
                    WHERE empid = :eid AND Status = 1 
                    GROUP BY LeaveType";
    $queryApproved = $dbh->prepare($sqlApproved);
    $queryApproved->bindParam(':eid', $eid, PDO::PARAM_STR);
    $queryApproved->execute();
    $approvedResults = $queryApproved->fetchAll(PDO::FETCH_OBJ);

    // Initialize approved leave counts
    $approvedCasual = $approvedEarned = $approvedSick = $approvedRestricted = $approvedOfficial = 0;

    // Map approved leaves to their types (case insensitive)
    foreach ($approvedResults as $result) {
        switch (strtolower($result->LeaveType)) {
            case 'casual leaves': $approvedCasual = $result->UsedCount; break;
            case 'earned leaves': $approvedEarned = $result->UsedCount; break;
            case 'sick leaves': $approvedSick = $result->UsedCount; break;
            case 'restricted leaves(rh)': $approvedRestricted = $result->UsedCount; break;
            case 'official duty': $approvedOfficial = $result->UsedCount; break;  // Added case for Official Duty
        }
    }

    // Calculate remaining leaves and prevent negative values
    $remainingCasual = max(0, $totalCasual - $approvedCasual);
    $remainingEarned = max(0, $totalEarned - $approvedEarned);
    $remainingSick = max(0, $totalSick - $approvedSick);
    $remainingRestricted = max(0, $totalRestricted - $approvedRestricted);
    $remainingOfficial = max(0, $totalOfficial - $approvedOfficial);  // Calculated remaining Official Duty
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Title -->
    <title>Employee | Remaining Leaves</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta charset="UTF-8">
    <meta name="description" content="Responsive Principal Dashboard Template" />
    <meta name="keywords" content="principal,dashboard" />
    <meta name="author" content="Steelcoders" />

    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
    <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>
    <style>
        /* Table Styling */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            font-family: 'Roboto', sans-serif;
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #1e88e5;
            color: #fff;
            font-size: 16px;
            text-transform: uppercase;
        }
        td {
            background-color: #f9f9f9;
            color: #333;
            font-size: 14px;
        }
        tr:nth-child(even) td {
            background-color: #e3f2fd;
        }
        tr:hover td {
            background-color: #bbdefb;
            cursor: pointer;
        }

        /* Header and Card Styling */
        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: #424242;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }
        .card-content {
            padding: 30px;
        }
        .card-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #1e88e5;
        }

        /* Error/Success message styling */
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap{
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Remaining Leaves</div>
            </div>
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Remaining Leaves</span>
                        <table class="responsive-table">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Quota</th>
                                    <th>Approved</th>
                                    <th>Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Casual</td>
                                    <td><?php echo htmlentities($totalCasual); ?></td>
                                    <td><?php echo htmlentities($approvedCasual); ?></td>
                                    <td><?php echo htmlentities($remainingCasual); ?></td>
                                </tr>
                                <tr>
                                    <td>Earned</td>
                                    <td><?php echo htmlentities($totalEarned); ?></td>
                                    <td><?php echo htmlentities($approvedEarned); ?></td>
                                    <td><?php echo htmlentities($remainingEarned); ?></td>
                                </tr>
                                <tr>
                                    <td>Sick</td>
                                    <td><?php echo htmlentities($totalSick); ?></td>
                                    <td><?php echo htmlentities($approvedSick); ?></td>
                                    <td><?php echo htmlentities($remainingSick); ?></td>
                                </tr>
                                <tr>
                                    <td>Restricted</td>
                                    <td><?php echo htmlentities($totalRestricted); ?></td>
                                    <td><?php echo htmlentities($approvedRestricted); ?></td>
                                    <td><?php echo htmlentities($remainingRestricted); ?></td>
                                </tr>
                                <tr>
                                    <td>Official Duty</td>  <!-- Added row for Official Duty -->
                                    <td><?php echo htmlentities($totalOfficial); ?></td>
                                    <td><?php echo htmlentities($approvedOfficial); ?></td>
                                    <td><?php echo htmlentities($remainingOfficial); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Javascripts -->
    <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
    <script src="assets/plugins/materialize/js/materialize.min.js"></script>
    <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
    <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="assets/js/alpha.min.js"></script>
</body>
</html>
<?php } ?>
