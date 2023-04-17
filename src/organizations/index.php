<?php
include_once('../core/header.php');

// Get the user ID and university ID from the session
$user_id = $_SESSION["user_id"];
$university_id = $_SESSION["user_universityid"];

// Fetch RSOs for the user's university
$stmt = $dbConn->prepare('SELECT * FROM RSOs WHERE UniversityID = :university_id');
$stmt->execute(['university_id' => $university_id]);
$rsos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="container">
        <div class="row">
            <?php foreach ($rsos as $rso) : ?>
                <?php
                // Check if the current user is an admin of the RSO
                $is_admin = $user_id == $rso['AdminID'];
                ?>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($rso['Name']) ?></h5>
                            <p class="card-text">RSO</p>
                            <a href="#" class="btn btn-primary">View RSO</a>
                            <?php if ($is_admin) : ?>
                                <a href="#" class="btn btn-warning">Edit RSO</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php
include_once('../core/footer.php');
?>