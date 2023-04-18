<?php
include_once('../auth/login_required.php');
include_once('../core/header.php');

// Get the user ID and university ID from the session
$user_id = $_SESSION["user_id"];
$university_id = $_SESSION["user_universityid"];

$university = university_get_university($university_id);

// Fetch RSOs for the user's university
$rsos = orgs_get_all_rsos($university_id);
?>


    <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
        <h1 class="display-4 fw-normal"><?= $university["Name"]; ?> Organizations</h1>
        <p class="fs-5 text-muted"><?= $university["Description"]; ?></p>
    </div>
    <div class="container">
        <div class="row">
            <?php foreach ($rsos as $rso) : ?>
                <?php

                // Check if the current user is an admin of the RSO
                $is_admin = $user_id == $rso['AdminID'];

                //Get the image url
                $imageUrl = $rso["ImageURL"];
                if ($imageUrl == null)
                    $imageUrl = $university["ImageURL"];


                //Get the member count of the rso
                $rsoMemberCount = count(orgs_get_members($rso["RSOID"]));

                //Get the RSO Admin email
                $rsoAdminEmail = users_get_user($university_id, $rso["AdminID"])["Email"];


                ?>

                <div class="col-md-4 event-card  my-4">
                    <div class="card h-100">
                        <img src="<?= $imageUrl ?>" class="card-img-top align-content-center" alt="">
                        <div class="card-body">
                            <h5 class="card-title"><?= $rso['Name'] ?></h5>
                            <p class="card-text"><?= $rso['Description'] ?></p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Admin:</strong>
                                <a href="mailto:<?= $rsoAdminEmail; ?>"><?= $rsoAdminEmail; ?></a>
                            </li>
                            <li class="list-group-item">
                                <strong>Members: </strong> <?= $rsoMemberCount ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Active: </strong> <?= $rso['Active'] ? "Active" : "Inactive" ?>
                            </li>
                            <?php if ($is_admin) : ?>
                                <li class="list-group-item">
                                    <a href="/organizations/edit_rso.php?id=<?= $rso["RSOID"] ?>"
                                       class="btn btn-primary w-100 mb-2">Manage
                                        RSO</a>

                                </li>
                            <?php endif; ?>
                        </ul>

                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

<?php
include_once('../core/footer.php');
?>