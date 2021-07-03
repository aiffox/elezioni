<?php
    mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
session_start();
    session_unset();
    session_destroy();
    header("location: ./index.php");
?>