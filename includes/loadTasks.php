<?php

include_once 'functions.php';

if ($_FILES && $_FILES["testFile"]["error"] == UPLOAD_ERR_OK) {
    $file_name =  $uploaddir . basename($_FILES['testFile']['name']);
    move_uploaded_file($_FILES["testFile"]["tmp_name"], $file_name);

    reade_file($file_name);
}

include_once 'header.php';
?>

<form enctype="multipart/form-data" action="" method="post">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <input type="file" name="testFile">
    <input type="submit" value="Загрузить">
</form>

<?php include_once 'footer.php'; ?>