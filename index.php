<?php
include 'core/app.php';
$app = new Application();

include BASE_PATH."/template/header.php"; ?>

<h1><?php echo $app->lang->item("site_title", 1, 2) ?></h1>

<?php include BASE_PATH."/template/footer.php"; ?>
