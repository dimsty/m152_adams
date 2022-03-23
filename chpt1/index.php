<?php
include("view/header.php");

$uc = empty($_GET['uc']) ? "home" : $_GET['uc'];

switch ($uc) {
    case 'home':
        require_once("controller/minController.php");
        break;
    case 'post':
        require_once("controller/uploadController.php");
        break;
}

include("view/footer.php");