<?php
include_once('../core/header.php');
include_once '../core/db.php';

// Get the user ID and university ID from the session
$user_id = $_SESSION["user_id"];

// Fetch RSOs for the user's university
$userComments = user_comments($user_id, $_GET["id"]);
?>



    <div class="container">
        <div class="column">
            <?php foreach ($userComments as $comment) { ?>
                <div class="row">

                    <div class="card" style="width: 75rem;">

                        <div class="card-body">
                            <h5 class="card-title"><?=$comment["Content"];?></h5>
                        </div>
                        <div class="card-body">
                            
                            <form class="text-start" action="/events/edit.php" method="post">
                                <input type="hidden" class="form-control" id="commentID" name="commentID" aria-describedby="commentID" value="<?=$comment["CommentID"];?>" required>
                                <div class="mb-3">
                                
                                    <input type="text" class="form-control" id="comment" name="comment" aria-describedby="event_id" required>
                                </div>
                                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Edit</button>
                            </form>
                            

                            <form class="text-start" action="/events/delete.php" method="post">
                                <input type="hidden" class="form-control" id="commentID" name="commentID" aria-describedby="commentID" value="<?=$comment["CommentID"];?>" required>
                                <button type="submit" id="submit" name="submit" class="btn btn-primary text-center">Delete</button>
                            </form>
                        
                        </div>
                    </div>

                </div>
            <?php } ?>
        </div>
    </div>

<?php
include_once('../core/footer.php');
?>