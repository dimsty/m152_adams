<?php
require "dbConnection.php";

function createMediaAndPost($typeMedia, $nomMedia, $creationDate, $commentaire, $alreadyLoop)
{
    echo "aaa";
    static $ps = null;
    static $LastidPost = null;
    $answer = false;
    $idPost = $LastidPost;
    try {
        // debut de la transaction
        dbConnect()->beginTransaction();
        if ($alreadyLoop == 0) {
            //creation POST
            $sql = "INSERT INTO `m152`.`POST` (`commentaire`, `creationDate`) ";
            $sql .= "VALUES (:COMMENTAIRE, :CREATIONDATE)";
            $pdo = dbConnect();
            $ps = $pdo->prepare($sql);
            $ps->bindParam(':COMMENTAIRE', $commentaire, PDO::PARAM_STR);
            $ps->bindParam(':CREATIONDATE', $creationDate, date("Y-m-d H:i:s"));
            $answer = $ps->execute();
            $idPost = $pdo->lastInsertId();
            $ps->close;
        }
        //Creation MEDIA
        $sql = "INSERTfghfgh INTO `m152`.`MEDIA` (`typeMedia`, `nomMedia`, `creationDate`, `idPost`) ";
        $sql .= "VALUES (:TYPEMEDIA, :NOMMEDIA, :CREATIONDATE, :IDPOST)";
        $ps = dbConnect()->prepare($sql);
        $ps->bindParam(':TYPEMEDIA', $typeMedia, PDO::PARAM_STR);
        $ps->bindParam(':NOMMEDIA', $nomMedia, PDO::PARAM_STR);
        $ps->bindParam(':CREATIONDATE', $creationDate, PDO::PARAM_STR);
        $ps->bindParam(':IDPOST', $idPost, PDO::PARAM_INT);
        $answer = $ps->execute();
        $LastidPost = $idPost;
        $ps->close;

        //commit
        dbConnect()->commit();
    } catch (PDOException $e) {
        echo $e->getMessage();
        //rollBack
        dbConnect()->rollBack();
    }
    return $answer;
}


function LastIdReturn()
{
    static $ps = null;
    $sql = 'SELECT idPost ';
    $sql .= ' FROM m152.POST';
    $sql .= ' ORDER BY idPost DESC LIMIT 1';
    if ($ps == null) {
        $ps = dbConnect()->prepare($sql);
    }
    $answer = false;
    try {
        if ($ps->execute())
            $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $answer;
}

/**
 * Supprime la POST avec l'id $idPost.
 * @param mixed $idPost
 * @return bool 
 */
function deletePostAndMedia($idPost)
{
    static $ps = null;
    $sql = 'DELETE m.idMedia, T2';
    $sql .= ' FROM T1 ';
    $sql .= ' INNER JOIN T2 ON T1.key = T2.key '; 
    $sql .= ' WHERE idPost = :IDPOST; ';    
    if ($ps == null) {
        $ps = dbConnect()->prepare($sql);
    }
    $answer = false;
    try {
        $ps->bindParam(':IDPOST', $idPost, PDO::PARAM_INT);
        $ps->execute();
        $answer = ($ps->rowCount() > 0);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $answer;
}

/**
 * Supprime la note avec l'id $idMedia.
 * @param mixed $idMedia
 * @return bool 
 */
function deleteMedia($idMedia)
{
    static $ps = null;
    $sql = "DELETE FROM `m152`.`MEDIA` WHERE (`idMedia` = :IDMEDIA);";
    if ($ps == null) {
        $ps = dbConnect()->prepare($sql);
    }
    $answer = false;
    try {
        $ps->bindParam(':IDMEDIA', $idMedia, PDO::PARAM_INT);
        $ps->execute();
        $answer = ($ps->rowCount() > 0);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $answer;
}


function countImagesMediaAssoc($idPost)
{
    static $ps = null;

    $sql = ' SELECT count(m.idMedia) as nb';
    $sql .= ' FROM m152.MEDIA as m ';
    $sql .= ' WHERE m.idPost = :IDPOST; ';

    if ($ps == null) {
        $ps = dbConnect()->prepare($sql);
    }
    $answer = false;
    try {
        $ps->bindParam(':IDPOST', $idPost, PDO::PARAM_INT);

        if ($ps->execute())
            $answer = $ps->fetch(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return $answer;
}

function readMediaAssoc($idPost)
{
    static $ps = null;

    $sql = 'SELECT m.idPost, m.nomMedia, m.typeMedia ,p.commentaire';
    $sql .= ' FROM m152.MEDIA as m, m152.POST as p ';
    $sql .= ' WHERE m.idPost = p.idPost AND m.idPost = :IDPOST; ';

    if ($ps == null) {
        $ps = dbConnect()->prepare($sql);
    }
    $answer = false;
    try {
        $ps->bindParam(':IDPOST', $idPost, PDO::PARAM_INT);

        if ($ps->execute())
            $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return $answer;
}

function readPost()
{
    static $ps = null;
    $sql = 'SELECT *';
    $sql .= ' FROM m152.POST';

    if ($ps == null) {
        $ps = dbConnect()->prepare($sql);
    }
    $answer = false;
    try {
        if ($ps->execute())
            $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return $answer;
}



function AffichagePost()
{
    $html = "";

    for ($i = LastIdReturn()[0]["idPost"]; $i >= 1; $i--) {
        $readMediasPost = readMediaAssoc($i);
        if (countImagesMediaAssoc($i) > 1) {

            $html .= "<div class=\"panel panel-default\">";
            $html .= "<div id=\"myCarousel$i\" class=\"carousel slide\" data-interval=\"false\" data-ride=\"carousel\">";

            // Indicators -->
            $html .= "<ol class=\"carousel-indicators\">";
            for ($g = 0; $g < countImagesMediaAssoc($i); $g++) {
                $active = ($g == 0) ? "active" : "";
                $html .= "<li data-target=\"#myCarousel$i\" data-slide-to=\"$i\" class=\"$active\"></li>";
            }
            $html .= "</ol>";

            $html .= "<div class=\"carousel-inner\">";
            //Wrapper for slides 
            for ($e = 0; $e < countImagesMediaAssoc($i); $e++) {
                $active = ($e == 0) ? "active" : "";
                $html .= "<div class=\"item $active\" align=\"center\">";

                if ($readMediasPost[$e]["typeMedia"] == "png" || $readMediasPost[$e]["typeMedia"] == "jpg" || $readMediasPost[$e]["typeMedia"] == "jpeg" || $readMediasPost[$e]["typeMedia"] == "gif" || $readMediasPost[$e]["typeMedia"] == "jpg") {
                    $html .= "<img src=\"assets/img/" . $readMediasPost[$e]["nomMedia"] . "\" alt=\"" . $readMediasPost[$e]["nomMedia"] . "\" width=\"100%\" height=\"50%\">";
                    $html .= "</div>";
                }

                if ($readMediasPost[$e]["typeMedia"] == "mp4" || $readMediasPost[$e]["typeMedia"] == "m4v") {
                    $html .= "\n <video width=\"100%\" height=\"100%\" controls autoplay loop muted >";
                    $html .= "\n <source src=\"assets/img/" . $readMediasPost[$e]["nomMedia"] . "\" type=\"video/mp4\" width=\"100%\" height=\"50%\">";
                    $html .= "\n </video>";
                    $html .= "\n </div>";
                }

                if ($readMediasPost[$e]["typeMedia"] == "mp3" || $readMediasPost[$e]["typeMedia"] == "wav" || $readMediasPost[$e]["typeMedia"] == "ogg") {
                    $html .= "\n <audio controls>";
                    $html .= "\n <source src=\"assets/img/" . $readMediasPost[$e]["nomMedia"] . "\" width=\"100%\" height=\"50%\">";
                    $html .= "\n </audio>";
                    $html .= "\n </div>";
                }
            }
            
            $html .= "</div>";


            // Left and right controls -->
            $html .= "<a class=\"left carousel-control\" href=\"#myCarousel$i\" data-slide=\"prev\">";
            $html .= "<span class=\"glyphicon glyphicon-chevron-left\"></span>";
            $html .= "<span class=\"sr-only\">Previous</span>";
            $html .= "</a>";
            $html .= "<a class=\"right carousel-control\" href=\"#myCarousel$i\" data-slide=\"next\">";
            $html .= "<span class=\"glyphicon glyphicon-chevron-right\"></span>";
            $html .= "<span class=\"sr-only\">Next</span>";
            $html .= "</a>";
            $html .= "</div>";

            $html .= "<div class=\"panel-body\">";
            $html .= "<p class=\"lead\">" . $readMediasPost[0]["commentaire"] . "</p>";
            $html .= "<button type=\"button\" class=\"btn btn-danger\">Danger</button>";
            $html .= "</div>";
            $html .= "</div>";
            
        } elseif (countImagesMediaAssoc($i) == 1) {

             $html .= "<div class=\"panel panel-default\">";
             $html .= "<div class=\"carousel\">";

            if ($readMediasPost[0]["typeMedia"] == "png" || $readMediasPost[0]["typeMedia"] == "jpg" || $readMediasPost[0]["typeMedia"] == "jpeg" || $readMediasPost[0]["typeMedia"] == "gif" || $readMediasPost[0]["typeMedia"] == "jpg") {
                $html .= "<div class=\"item\" align=\"center\" >";
                $html .= "<img src=\"assets/img/" . $readMediasPost[0]["nomMedia"] . "\" alt=\"" . $readMediasPost[0]["nomMedia"] . "\" width=\"100%\" height=\"50%\">";
                $html .= "</div>";
            }

            if ($readMediasPost[0]["typeMedia"] == "mp4" || $readMediasPost[0]["typeMedia"] == "m4v") {
                $html .= "<div class=\"item\" align=\"center\">";
                $html .= "\n <video width=\"100%\" height=\"100%\" controls autoplay loop muted >";
                $html .= "\n <source src=\"assets/img/" . $readMediasPost[0]["nomMedia"] . "\" type=\"video/mp4\" width=\"100%\" height=\"50%\">";
                $html .= "\n </video>";
                $html .= "\n </div>";
            }

            if ($readMediasPost[0]["typeMedia"] == "mp3" || $readMediasPost[0]["typeMedia"] == "wav" || $readMediasPost[0]["typeMedia"] == "ogg") {
                $html .= "<div class=\"item\" align=\"center\">";
                $html .= "\n <audio controls>";
                $html .= "\n <source src=\"assets/img/" . $readMediasPost[0]["nomMedia"] . "\" width=\"100%\" height=\"50%\">";
                $html .= "\n </audio>";
                $html .= "\n </div>";
            }
        
            $html .= "<div class=\"panel-body\">";
            $html .= "<p class=\"lead\">" . $readMediasPost[0]["commentaire"] . "</p>";
            $html .= "<button type=\"button\" onclick=\" deletePostAndMedia(); \" class=\"btn btn-danger\">Danger</button>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }
    }

    return $html;
}
