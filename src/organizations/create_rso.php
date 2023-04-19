<?php
include_once('../auth/login_required.php');
include_once('../core/header.php');
require_once('../core/db.php');

$error = null;

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $rsoName = trim($_POST['rsoName']);
    $rsoDescription = trim($_POST['rsoDescription']);
    $rsoImage = trim($_POST['rsoImage']);
    $universityID = intval($_POST['universityID']);
    $memberEmails = array_filter($_POST['memberEmails'], 'strlen');

    // Get the current user's ID as the admin
    $adminID = $_SESSION["user_id"];

    //Check if the rso alreaxy exists with this name
    $rsoId = orgs_get_rsoid($universityID, $rsoName);
    if ($rsoId != null) {
        $error = "An RSO already exists with this name.";
    }

    //Error checking
    if ($error == null) {

        //Create the RSO
        orgs_create_rso($universityID, $rsoName, $rsoDescription, $rsoImage, $adminID, $memberEmails);

        //Get the new RSOID
        $rsoId = orgs_get_rsoid($universityID, $rsoName);

        //Add admin to the rso
        orgs_add_member($rsoId["RSOID"], $adminID);

        header("Location: /organizations/edit_rso.php?id=" . $rsoId);
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
                    <label for="rsoDescription" class="form-label">RSO Description</label>
                    <input type="text" class="form-control" id="rsoDescription" name="rsoDescription" required>
                </div><div class="mb-3">
                    <label for="rsoImage" class="form-label">RSO Image URL</label>
                    <input type="text" class="form-control" id="rsoImage" name="rsoImage" required>
                </div>

                <input type="number" class="form-control" id="universityID" name="universityID" required hidden value="<?=$_SESSION["user_universityid"];?>">
                <div class="mb-3" id="memberEmailsContainer">
                    <label for="memberEmails[]" class="form-label">Member Email Addresses</label>
                    <?php for ($i = 0;
                    $i < 4;
                    $i++) : ?>
                        <input type="email" class="form-control mb-2 memberEmails" id="memberEmails[]"
                               name="memberEmails[]" placeholder="Email address">
                    <?php endfor; ?>
                </div>
                <div class="mb-2">
                    <button type="button" id="addMoreMembers" class="btn btn-secondary  w-100">Add more members</button>
                </div>
                    <div class="mb-2">
                        <button type="submit" id="submit" name="submit" class="btn btn-primary text-center w-100">Create RSO</button>
                    </div>
            </form>
        </div>

        <script>
            $(document).ready(function () {
                $("#addMoreMembers").click(function () {
                    $('<input type="email" class="form-control mb-2 memberEmails" id="memberEmails[]" name="memberEmails[]" placeholder="Email address">').appendTo("#memberEmailsContainer");
                });
            });
        </script>
    </div>
</div>

<?php
include_once ('../core/footer.php');
?>