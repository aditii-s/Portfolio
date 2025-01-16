<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['emplogin']) == 0) {   
    header('location:index.php');
} else {
    if(isset($_POST['apply'])) {
        $empid = $_SESSION['eid'];
        $leavetype = $_POST['leavetype'];
        $fromdate = $_POST['fromdate'];  
        $todate = $_POST['todate'];
        $description = $_POST['description'];  
        $status = 0;
        $isread = 0;

        // File upload handling
        $proof = "";
        if($leavetype == 'Official Duty(OD)' && isset($_FILES['proof']) && $_FILES['proof']['error'] == 0) {
            $proof = "assets/uploads/" . basename($_FILES['proof']['name']);
            move_uploaded_file($_FILES['proof']['tmp_name'], $proof);
        }
        if($leavetype == 'Official Duty(OD)' && !$proof) {
            $error = "Proof document is required for Official Duty leave.";
        }
        // Date validation
        if($fromdate > $todate) {
            $error = "ToDate should be greater than FromDate.";
        }

        // Total leave quotas (set your own values)
        $totalCasual = 10;
        $totalEarned = 15;
        $totalSick = 1;
        $totalRestricted = 5;
        $totalOfficialDuty = 5; 

        // Query to count approved leaves for the employee
        $sqlApproved = "SELECT LeaveType, COUNT(*) as UsedCount FROM tblleaves 
                        WHERE empid = :eid AND Status = 1 
                        GROUP BY LeaveType";
        $queryApproved = $dbh->prepare($sqlApproved);
        $queryApproved->bindParam(':eid', $empid, PDO::PARAM_STR);
        $queryApproved->execute();
        $approvedResults = $queryApproved->fetchAll(PDO::FETCH_OBJ);

        // Initialize the approved leave counts
        $approvedCasual = $approvedEarned = $approvedSick = $approvedRestricted = $approvedOfficialDuty = 0;

        foreach ($approvedResults as $result) {
            switch (strtolower($result->LeaveType)) {
                case 'casual leaves': $approvedCasual = $result->UsedCount; break;
                case 'earned leaves': $approvedEarned = $result->UsedCount; break;
                case 'sick leaves': $approvedSick = $result->UsedCount; break;
                case 'restricted leaves(rh)': $approvedRestricted = $result->UsedCount; break;
                case 'official duty(od)': $approvedOfficialDuty = $result->UsedCount; break;
            }
        }

        // Calculate remaining leaves
        $remainingCasual = max(0, $totalCasual - $approvedCasual);
        $remainingEarned = max(0, $totalEarned - $approvedEarned);
        $remainingSick = max(0, $totalSick - $approvedSick);
        $remainingRestricted = max(0, $totalRestricted - $approvedRestricted);
        $remainingOfficialDuty = max(0, $totalOfficialDuty - $approvedOfficialDuty);

        // Check if the leave type is available
        if ($leavetype == 'Casual Leaves' && $remainingCasual <= 0) {
            $error = "You do not have any remaining Casual leaves.";
        } elseif ($leavetype == 'Earned Leaves' && $remainingEarned <= 0) {
            $error = "You do not have any remaining Earned leaves.";
        } elseif ($leavetype == 'Sick Leaves' && $remainingSick <= 0) {
            $error = "You do not have any remaining Sick leaves.";
        } elseif ($leavetype == 'Restricted Leaves(RH)' && $remainingRestricted <= 0) {
            $error = "You do not have any remaining Restricted leaves.";
        } elseif ($leavetype == 'Official Duty' && $remainingOfficialDuty <= 0) {
            $error = "You do not have any remaining Official Duty leaves.";
        } elseif ($leavetype == 'Official Duty' && empty($proof)) {
            $error = "Proof document is required for Official Duty leave.";
        } else {
            // Insert the leave application into the database
            $sql = "INSERT INTO tblleaves(LeaveType, ToDate, FromDate, Description, Status, IsRead, empid, Proof) 
                    VALUES(:leavetype, :todate, :fromdate, :description, :status, :isread, :empid, :proof)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':leavetype', $leavetype, PDO::PARAM_STR);
            $query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
            $query->bindParam(':todate', $todate, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':isread', $isread, PDO::PARAM_STR);
            $query->bindParam(':empid', $empid, PDO::PARAM_STR);
            $query->bindParam(':proof', $proof, PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();
            if($lastInsertId) {
                $msg = "Leave applied successfully.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee | Apply Leave</title>
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
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
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
                <div class="page-title"></div>
            </div>
            <div class="col s12 m12 l8">
                <div class="card">
                    <div class="card-content">
                        <form id="example-form" method="post" name="addemp" enctype="multipart/form-data">
                            <div>
                                <h3>Apply for Leave</h3>
                                <section>
                                    <div class="wizard-content">
                                        <div class="row">
                                            <div class="col m12">
                                                <div class="row">
                                                    <?php if($error) { ?>
                                                        <div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div>
                                                    <?php } else if($msg) { ?>
                                                        <div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div>
                                                    <?php } ?>
                                                    
                                                    <div class="input-field col s12">
                                                        <select name="leavetype" id="leavetype" onchange="toggleProofField()" autocomplete="off" required>
                                                            <option value="">Select leave type...</option>
                                                            <?php 
                                                            $sql = "SELECT LeaveType FROM tblleavetype";
                                                            $query = $dbh->prepare($sql);
                                                            $query->execute();
                                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                            if($query->rowCount() > 0) {
                                                                foreach($results as $result) {
                                                            ?>
                                                                <option value="<?php echo htmlentities($result->LeaveType); ?>"><?php echo htmlentities($result->LeaveType); ?></option>
                                                            <?php } } ?>
                                                        </select>
                                                    </div>

                                                    <div class="row" style="margin-top: 20px;">
    <div class="input-field col m6 s12">
        <input placeholder="Select from date" id="fromdate" name="fromdate" class="masked" type="date" required>
        <label for="fromdate" class="active">From Date</label>
    </div>

    <div class="input-field col m6 s12">
        <input placeholder="Select to date" id="todate" name="todate" class="masked" type="date" required>
        <label for="todate" class="active">To Date</label>
    </div>
</div>

<!-- Proof Section -->
<div class="input-field col m6 s12" id="proofSection" style="display:none;">
    <label for="proof" style="margin-bottom: 40px; display:inline-block;"><b>Upload Proof</b></label>
    <div>
        <input type="file" name="proof" id="proof" accept="*" style="border: 1px solid #ddd; padding: 5px; border-radius: 4px; margin-top: 40px;">
    </div>
</div>

<div class="input-field col m12 s12">
    <label for="description">Description</label>
    <textarea id="textarea1" name="description" class="materialize-textarea" length="500" required></textarea>
</div>

<!-- Apply Button -->
<div class="input-field col s12">
    <button type="submit" name="apply" id="apply" class="waves-effect waves-light btn indigo m-b-xs">Apply</button>
</div>

                                        </div>
                                    </div>
                                </section>
                            </div>
                        </form>
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
    <script src="assets/js/pages/form_elements.js"></script>
    <script src="assets/js/pages/form-input-mask.js"></script>
    <script src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>

    <script>
        function toggleProofField() {
            const leavetype = document.getElementById('leavetype').value;
            const proofSection = document.getElementById('proofSection');
            const proofInput = document.getElementById('proof');

            if (leavetype === 'Official Duty(OD)') {
                proofSection.style.display = 'block';
                proofInput.required = true;
            } else {
                proofSection.style.display = 'none';
                proofInput.required = false;
            }
        }
    </script>
</body>
</html>
<?php } ?>
