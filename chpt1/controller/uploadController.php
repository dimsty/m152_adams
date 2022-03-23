<?php

require "model/postFunction.php";


$reponse = "";
$commentaire = filter_input(INPUT_POST, 'commentaire');
$action = filter_input(INPUT_POST, 'action');
$loop = 0;


switch ($action) {
    case 'publish':

        $nbFileUpload = count($_FILES['filesToUpload']['name']);
       // $lastIdPost = createPostAndReturnLastId($commentaire, date("Y-m-d H:i:s"));
        define('sizeImageLimite', 3 * 1024 * 1024);

        for ($i = 0; $i < $nbFileUpload; $i++) {

            $target_dir = "assets/img/"; // specifies the directory where the file is going to be placed
            $target_file = $target_dir . basename($_FILES["filesToUpload"]["name"][$i]);
            $uploadOk = 1;
            $filesToUploadType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $uniqueNameID = uniqid() .".". $filesToUploadType;

            // Check if image file is a actual image or fake image
            if (isset($_POST["publish"])) {
                $check = getimagesize($_FILES["filesToUpload"]["tmp_name"][$i]);
                if ($check !== false) {
                    $reponse .= "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    $reponse .= "File is not an image.";
                    $uploadOk = 0;
                }
            }

            // Check file size
            if ($_FILES["filesToUpload"]["size"][$i] > sizeImageLimite) {
                $reponse .= "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            /*
            // Allow certain file formats
            if ($filesToUploadType != "jpg" && $filesToUploadType != "png" && $filesToUploadType != "jpeg" && $filesToUploadType != "gif") {
                $reponse .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }*/

            // Check if $uploadOk is set to 0 by an error
                        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $reponse .= "Sorry, your file was not uploaded.";
        }else {                     
             //   if($lastIdPost){
                    if (move_uploaded_file($_FILES["filesToUpload"]["tmp_name"][$i], $target_dir . $uniqueNameID)) {
                        $reponse .= "The file " . htmlspecialchars(basename($_FILES["filesToUpload"]["name"][$i])) . " has been uploaded.";
                        createMediaAndPost($filesToUploadType, $uniqueNameID,date("Y-m-d H:i:s"), $commentaire, $loop);
                        $loop = 1;                            
                    } else {
                        $reponse .= "Sorry, there was an error uploading your file.";
                    }
                    header('Location: index.php?uc=home');
                   // createMedia($filesToUploadType, $uniqueNameID, date("Y-m-d H:i:s"),$lastIdPost);
              //  }                                     
            }        
        }
        break;
        case 'delete':
           
// recup du post
$idPost = filter_input(INPUT_GET, 'idPost', FILTER_SANITIZE_NUMBER_INT);
  
// sup des images
$medias = Media::getAllMediasByPostId($idPost);

// debut de la transaction
MonPdo::getInstance()->beginTransaction();

// suppression de tous les fichiers
foreach ($medias as $media) {
    if (unlink("./assets/uploads/" . $media->getNomMedia())) {
        Media::DeleteMedia($media->getIdMedia());
    } else {
        // on arrete la transaction
        MonPdo::getInstance()->rollBack();
        // retourne un message d'erreur
        $_SESSION['message'] = [
            'type' => "danger",
            'content' => "Un fichier n'a pas pu être supprimé. Merci de ressayer."
        ];
        header('Location: index.php');
    }
}
Post::DeletePost($idPost);
MonPdo::getInstance()->commit();
$_SESSION['message'] = [
    'type' => "success",
    'content' => "Le post a bien été supprimé."
];
header('Location: index.php');
          break;

}

require "view/post.php";