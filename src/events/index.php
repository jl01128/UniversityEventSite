<?php
include_once('../core/header.php');
?>


<?php

?>

<div class="h-100 d-flex align-items-center justify-content-center container">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

        <?php for($i = 0; $i < 8; $i++): ?>
        <div class="col">
            <div class="card" style="width: 18rem;">
                <img src="..." class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Event Name</h5>
                    <p class="card-text">Event description.</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Event Category</li>
                    <li class="list-group-item">Event Date</li>
                    <li class="list-group-item">Event Time</li>
                </ul>
                <div class="card-body">
                    <a href="#" class="card-link">View Event</a>
                    <a href="#" class="card-link">Edit event</a>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>


<?php
include_once('../core/footer.php');
?>


