<?php
include "../model/db.php"; 
$target_dir = "../assets/img/";
$commentaire = filter_input(INPUT_POST, 'commentaire');
$uploadOk = 1;
$alreadyLoop = 0;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$db = new Database;
$MAX_SIZE = 1024*1024;
//$id = GetLastId();
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
} 
  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
  // if everything is ok, try to upload file
  } else {
    $name = uniqid();
    echo "<br><pre>";
    var_dump($id);
    echo "</pre>";
    $nbFiles = count($_FILES['fileToUpload']['tmp_name']);
    $db->insertComment($commentaire);
    for($i = 0; $i < $nbFiles; $i++){
      
      $target_file = "../assets/img/".uniqid();
      $imageFileType = pathinfo($_FILES['fileToUpload']['name'][$i], PATHINFO_EXTENSION);
      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        header('Location: ../index.php');
      }
      else{
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file.".".$imageFileType)) {
    $db->insertImage($target_file .".". $imageFileType);
    echo "The file ".htmlspecialchars(basename($_FILES["fileToUpload"]["name"][$i]))." has been uploaded.";
    echo "The comment ".$commentaire." has been uploaded.";
    //var_dump(createMediaAndPost($imageFileType, $target_file , date("Y-m-d H:i:s"), $commentaire, $alreadyLoop));

    
    $alreadyLoop = 1;
    //header('Location: ../index.php');
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}}
      
    
  }
  var_dump($target_file);
  
?>