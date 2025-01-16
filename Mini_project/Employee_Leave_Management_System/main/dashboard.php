<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['emplogin'])==0)
    {   
header('location:index.php');
}
else{    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Employee | Dashboard</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Principal Dashboard Template" />
        <meta name="keywords" content="principal,dashboard" />
        <meta name="author" content="" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">    
        <link href="assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
        <link href="assets/plugins/weather-icons-master/css/weather-icons.min.css" rel="stylesheet">

        <!-- Theme Styles -->
        <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        
        
    </head>
    <body>
           <?php include('includes/header.php');?>
       <?php include('includes/sidebar.php');?>

            <main class="mn-inner">
                <div class="">
                    <div class="row no-m-t no-m-b">
                        <a href="leavehistory.php" target="blank">
                            <div class="col s12 m12 l4">
                                <div class="card stats-card">
                                    <div class="card-content">
                                        <span class="card-title">Total Leaves</span>
                                        <?php $eid=$_SESSION['eid'];
                                        $sql = "SELECT id from  tblleaves where empid ='$eid'";
                                        $query = $dbh -> prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $totalleaves=$query->rowCount();
                                        ?>   
                                        <span class="stats-counter"><span class="counter"><?php echo htmlentities($totalleaves);?></span></span>
                                    </div>
                                    <div class="progress stats-card-progress">
                                        <div class="success" style="width: 70%"></div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="leavehistory.php" target="blank">
                            <div class="col s12 m12 l4">
                                <div class="card stats-card">
                                    <div class="card-content">
                                        <span class="card-title">Approved Leaves</span>
                                        <?php
                                        $sql = "SELECT id from  tblleaves where Status=1 and empid ='$eid'";
                                        $query = $dbh -> prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $approvedleaves=$query->rowCount();
                                        ?>   
                                        <span class="stats-counter"><span class="counter"><?php echo htmlentities($approvedleaves);?></span></span>
                                    </div>
                                    <div class="progress stats-card-progress">
                                        <div class="success" style="width: 70%"></div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="remaining-leaves.php" target="_blank">
                            <div class="col s12 m12 l4">
                                <div class="card stats-card">
                                    <div class="card-content">
                                        <span class="card-title">Remaining Leaves</span>
                                        <?php
                                        // Define total leave quotas
                                        $totalCasual = 10;
                                        $totalEarned = 15;
                                        $totalSick = 1;
                                        $totalRestricted = 5;
                                        $totalOfficialDuty = 5;

                                        // Query to count used leaves for each type
                                        $sql = "SELECT LeaveType, COUNT(*) as UsedCount FROM tblleaves 
                                                WHERE empid = :eid AND Status = 1 
                                                GROUP BY LeaveType";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                                        // Initialize used leave counts
                                        $usedCasual = $usedEarned = $usedSick = $usedRestricted = $usedOfficialDuty =0;

                                        // Map used leaves to their types
                                        foreach ($results as $result) {
                                            switch ($result->LeaveType) {
                                                case 'Casual Leaves': $usedCasual = $result->UsedCount; break;
                                                case 'Earned Leaves': $usedEarned = $result->UsedCount; break;
                                                case 'Sick Leaves': $usedSick = $result->UsedCount; break;
                                                case 'Restricted Leaves(RH)': $usedRestricted = $result->UsedCount; break;
                                                case 'Official Duty(OD)': $usedRestricted = $result->UsedCount; break;
                                            }
                                        }

                                        // Calculate remaining leaves
                                        $remainingCasual = $totalCasual - $usedCasual;
                                        $remainingEarned = $totalEarned - $usedEarned;
                                        $remainingSick = $totalSick - $usedSick;
                                        $remainingRestricted = $totalRestricted - $usedRestricted;
                                        $remainingOfficialDuty = $totalOfficialDuty - $usedOfficialDuty;

                                        // Total remaining leaves
                                        $totalRemaining = $remainingCasual + $remainingEarned + $remainingSick + $remainingRestricted + $remainingOfficialDuty;
                                        ?>
                                        <span class="stats-counter"><span class="counter"><?php echo htmlentities($totalRemaining); ?></span></span>
                                    </div>
                                    <div class="progress stats-card-progress">
                                        <div class="success" style="width: 70%"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="row no-m-t no-m-b">

    
                    <div class="row no-m-t no-m-b">
                        
    <a href="class-request.php" target="_blank">
    <div class="row no-m-t no-m-b">

<!-- Substitute Employee Button with Count -->
<a href="class-request.php" target="_blank">
    <div class="col s12 m12 l4">
        <div class="card stats-card">
            <div class="card-content">
                <span class="card-title">Substitute Employee</span>
                <?php
                $sql = "SELECT COUNT(*) as SubstituteCount FROM tblclassrequests WHERE requester_id = :eid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_OBJ);
                $substituteCount = $result->SubstituteCount;
                ?>
                <span class="stats-counter"><span class="counter"><?php echo htmlentities($substituteCount); ?></span></span>
            </div>
            <div class="progress stats-card-progress">
                <div class="success" style="width: 70%"></div>
            </div>
        </div>
    </div>
</a>

<!-- Requests Received Button with Count -->
<a href="class-requests-received.php" target="_blank">
    <div class="col s12 m12 l4">
        <div class="card stats-card">
            <div class="card-content">
                <span class="card-title">Requests Received</span>
               <?php
if (isset($_SESSION['pending_requests_count'])) {
    $pendingRequests = $_SESSION['pending_requests_count'];
} else {
    // Fallback to the default query if session data isn't available
    $sql = "SELECT COUNT(*) as PendingRequests FROM tblclassrequests WHERE requested_emp_id=:eid AND status='Pending'";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    $pendingRequests = $result->PendingRequests;
}
?>

<span class="stats-counter"><span class="counter"><?php echo htmlentities($pendingRequests); ?></span></span>

            </div>
            <div class="progress stats-card-progress">
                <div class="success" style="width: 70%"></div>
            </div>
        </div>
    </div>
</a>

</div>

                    <div class="row no-m-t no-m-b">
                        <div class="col s15 m12 l12">
                            <div class="card invoices-card">
                                <div class="card-content">
                                    <span class="card-title">Latest Leave Applications</span>
                                    <table id="example" class="display responsive-table ">
    <thead>
        <tr>
            <th>#</th>
            <th width="200">Employee Name</th>
            <th width="120">Leave Type</th>
            <th width="180">Posting Date</th>
            <th>Status</th>
            <th>Proof</th>
            <th>Substitute Emp</th>
            <th align="center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $sql ="SELECT tblleaves.id as lid, tblemployees.FirstName, tblemployees.LastName, tblemployees.EmpId, tblemployees.id, tblleaves.LeaveType, tblleaves.PostingDate, tblleaves.Status, tblleaves.Proof, tblleaves.FromDate, tblleaves.ToDate 
               FROM tblleaves 
               JOIN tblemployees ON tblleaves.empid = tblemployees.id 
               WHERE tblleaves.empid = :eid 
               ORDER BY lid DESC 
               LIMIT 6";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eid', $eid, PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $cnt = 1;
        if ($query->rowCount() > 0) {
            foreach ($results as $result) {
                ?>  
                <tr>
                    <td><b><?php echo htmlentities($cnt); ?></b></td>
                    <td><a href="editemployee.php?empid=<?php echo htmlentities($result->id); ?>" target="_blank">
                        <?php echo htmlentities($result->FirstName . " " . $result->LastName); ?>
                        (<?php echo htmlentities($result->EmpId); ?>)</a>
                    </td>
                    <td><?php echo htmlentities($result->LeaveType); ?></td>
                    <td><?php echo htmlentities($result->PostingDate); ?></td>
                    <td>
                        <?php $stats = $result->Status;
                        if ($stats == 1) {
                            echo "<span style='color: green'>Approved</span>";
                        } elseif ($stats == 2) {
                            echo "<span style='color: red'>Not Approved</span>";
                        } else {
                            echo "<span style='color: blue'>Waiting for approval</span>";
                        } ?>
                    </td>
                    <td>
                        <?php if (!empty($result->Proof)) { ?>
                            <a href="<?php echo htmlentities($result->Proof); ?>" target="_blank">View Proof</a>
                        <?php } else { ?>
                            <span>No Proof</span>
                        <?php } ?>
                    </td>
                    <td>
    <?php
    $leaveFromDate = $result->FromDate;
    $leaveToDate = $result->ToDate;

    // Query to find a matching accepted substitute employee
    $subSql = "SELECT requested_emp_name FROM tblclassrequests 
               WHERE from_date = :FromDate 
               AND to_date = :ToDate 
               AND Status = 'Accepted'";
    $subQuery = $dbh->prepare($subSql);
    $subQuery->bindParam(':FromDate', $leaveFromDate, PDO::PARAM_STR);
    $subQuery->bindParam(':ToDate', $leaveToDate, PDO::PARAM_STR);
    $subQuery->execute();
    $subResult = $subQuery->fetch(PDO::FETCH_OBJ);

    if ($subResult) {
        echo htmlentities($subResult->requested_emp_name);
    } else {
        echo "No Substitute";
    }
    ?>
</td>

                    <td>
                        <a href="leave-details.php?leaveid=<?php echo htmlentities($result->lid); ?>" class="waves-effect waves-light btn blue m-b-xs">View Details</a>
                    </td>
                </tr>
                <?php $cnt++; 
            }
        } ?>
    </tbody>
</table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Javascripts -->
        <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="assets/plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="assets/plugins/counter-up-master/jquery.counterup.min.js"></script>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/chart.js/chart.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="assets/plugins/curvedlines/curvedLines.js"></script>
        <script src="assets/plugins/peity/jquery.peity.min.js"></script>
        <script src="assets/js/alpha.min.js"></script>
        <script src="assets/js/pages/dashboard.js"></script>
        
        
    </body>
</html>
<?php } ?>