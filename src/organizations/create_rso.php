<?php
include_once('../auth/login_required.php');
include_once('../core/header.php');
require_once('../core/db.php');

$error = null;

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $rsoName = trim($_POST['rsoName']);
    $universityID = intval($_POST['universityID']);
    $memberEmails = array_filter($_POST['memberEmails'], 'strlen');

    // Get the current user's ID as the admin
    $adminID = $_SESSION["user_id"];

    echo 'admin: ';
    echo $adminID;

    if (empty($rsoName) || empty($universityID) || count($memberEmails) < 4) {
        $error = "All fields are required, and at least 4 members should be added.";
    } else {
        try {
            // Insert the RSO
            $stmt = $dbConn->prepare("INSERT INTO RSOs (Name, AdminID, UniversityID) VALUES (:name, :adminID, :universityID)");
            $stmt->bindParam(':name', $rsoName);
            $stmt->bindParam(':adminID', $adminID);
            $stmt->bindParam(':universityID', $universityID);
            $stmt->execute();
            $rsoID = $dbConn->lastInsertId();

            // Get UserIDs for member email addresses
            $userIDs = [];
            foreach ($memberEmails as $email) {
                $stmt = $dbConn->prepare("SELECT UserID FROM Users WHERE Email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $userID = $stmt->fetchColumn();
                if ($userID) {
                    $userIDs[] = $userID;
                }
            }

            if (count($userIDs) < 4) {
                $error = "At least 4 valid member email addresses are required.";
            } else {
                // Add members to the RSOMembers table
                $stmt = $dbConn->prepare("INSERT INTO RSOMembers (UserID, RSOID) VALUES (:userID, :rsoID)");
                foreach ($userIDs as $userID) {
                    $stmt->bindParam(':userID', $userID);
                    $stmt->bindParam(':rsoID', $rsoID);
                    $stmt->execute();
                }


                //Add the user that created the RSO.
                $stmt->bindParam(':userID', $_SESSION["user_id"]);
                $stmt->bindParam(':rsoID', $rsoID);
                $stmt->execute();


                //header("Location: success.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Error creating RSO: " . $e->getMessage();
        }
    }
}

?>

<?php if ($error != null) : ?>
    <div class="alert alert-danger text-center" role="alert">
        Error: <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="h-100 d-flex align-items-center justify-content-center">

    <!--Create RSO card.-->
    <div class="card text-center w-25">
        <div class="container">
            <h1>Create RSO</h1>
            <form class="text-start" action="/organizations/create_rso.php" method="post">
                <div class="mb-3">
                    <label for="rsoName" class="form-label">RSO Name</label>
                    <input type="text" class="form-control" id="rsoName" name="rsoName" required>
                </div>
                <div class="mb-3">
                    <label for="universityID" class="form-label">University ID</label>
                    <input type="number" class="form-control" id="universityID" name="universityID" required>
                </div>
                <div class="mb-3" id="memberEmailsContainer">
                    <label for="memberEmails[]" class="form-label">Member Email Addresses</label>
                    <?php for ($i = 0; $i < 4; $i++) : ?>
                        <input type="email" class="form-control mb-2 memberEmails" id="memberEmails[]" name="memberEmails[]" placeholder="Email address">
                    <?php endfor; ?>
                </div>
                <button type="button" id="addMoreMembers" class="btn btn-secondary mb-3">Add more members</button>
                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Create RSO</button>
            </form>
        </div>

        <script>
            $(document).ready(function() {
                $("#addMoreMembers").click(function() {
                    $('<input type="email" class="form-control mb-2 memberEmails" id="memberEmails[]" name="memberEmails[]" placeholder="Email address">').appendTo("#memberEmailsContainer");
                });
            });
        </script>
    </div>
</div>

<?php
include_once('../core/footer.php');
?>