<?php
include_once('../auth/login_required.php');
include_once('../core/header.php');
require_once('../core/db.php');

$error = null;

$universityId = $_SESSION["user_universityid"];

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $rsoId = trim($_POST['rsoId']);
    $rsoName = trim($_POST['rsoName']);
    $rsoAdminEmail = trim($_POST['rsoAdmin']);
    $memberEmails = array_filter($_POST['memberEmails'], 'strlen');

    //Get the RSO
    $rso = orgs_get_rso($universityId, $rsoId);

    //Get the new rso admins id
    $rsoAdmin = users_get_user_from_email($universityId, $rsoAdminEmail)["UserID"];

    //Update the org
    orgs_update_rso($universityId, $rsoId, $rsoName, $rsoAdmin,$memberEmails);

    //Redirect!
    header('Location: /organizations/edit_rso.php?id='.$rsoId);
}


//Get the RSO
$rso = orgs_get_rso($universityId, $_GET["id"]);

$members = orgs_get_members($_GET["id"]);


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
                <h1>Edit RSO</h1>
                <form class="text-start" action="/organizations/edit_rso.php" method="post">

                    <input type="hidden" class="form-control" id="rsoId" name="rsoId" aria-describedby="rsoId" value="<?=$_GET["id"]?>" required>

                    <div class="mb-3">
                        <label for="rsoName" class="form-label">RSO Name</label>
                        <input type="text" class="form-control" id="rsoName" name="rsoName" value="<?=$rso["Name"]?>"required>
                    </div>

                    <div class="mb-3">
                        <label for="rsoName" class="form-label">Admin</label>
                        <input type="email" class="form-control" id="rsoAdmin" name="rsoAdmin" value="<?=users_get_user($universityId,$rso["AdminID"])["Email"]?>"required>
                    </div>

                    <div class="mb-3">
                        <label for="universityID" class="form-label">University ID</label>
                        <input type="number" class="form-control" id="universityID" name="universityID" value="<?=$rso["UniversityID"]?>" disabled>
                    </div>

                    <!-- Member list! -->
                    <div class="mb-3" id="memberEmailsContainer">
                        <label for="memberEmails[]" class="form-label">Members</label>
                        <?php foreach ($members as $rsoMember) : ?>
                            <div class="input-group mb-2 member-email-wrapper">
                                <input type="email" class="form-control memberEmails" id="memberEmails[]" name="memberEmails[]" placeholder="Email address" value="<?=(users_get_user($universityId, $rsoMember["UserID"])["Email"])?>" <?=orgs_check_admin($universityId, $rso["RSOID"], $rsoMember["UserID"]) ? "disabled" : ""?>>
                                <button type="button" class="btn btn-danger remove-member" <?=orgs_check_admin($universityId, $rso["RSOID"], $rsoMember["UserID"]) ? "disabled" : ""?>>Remove</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="addMoreMembers" class="btn btn-secondary mb-7">Add more members</button>
                    <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Save Changes</button>
                </form>
            </div>

            <script>
                $(document).ready(function() {
                    $("#addMoreMembers").click(function() {
                        $('<div class="input-group mb-2 member-email-wrapper"><input type="email" class="form-control mb-2 memberEmails" id="memberEmails[]" name="memberEmails[]" placeholder="Email address"><button type="button" class="btn btn-danger remove-member">Remove</button></div>').appendTo("#memberEmailsContainer");
                    });

                    $(document).on('click', '.remove-member', function() {
                        $(this).closest('.member-email-wrapper').remove();
                    });
                });
            </script>
        </div>
    </div>

<?php
include_once('../core/footer.php');
?>