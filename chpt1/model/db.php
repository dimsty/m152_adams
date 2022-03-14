<?php
	// Include config.php file
	include_once 'config.php';

	// Create a class Users
	class Database extends Config {
	  // Fetch all or a single user from database
	  public function fetch($id = 0) {
	    $sql = 'SELECT * FROM jobs.jobs';
	    if ($id != 0) {
	      $sql .= ' WHERE id = :id';
	    }
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['id' => $id]);
	    $rows = $stmt->fetchAll();
		
	  }
	  // Insert an user in the database
	  public function insertImage($nomMedia) {
	    $sql = 'INSERT INTO m152.MEDIA (nomMedia) VALUES (:nomMedia)';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['nomMedia' => $nomMedia]);
	    return true;
	  }

	  function getCountFromDifferentIdPost()
{
    static $ps = null;
    $sql = 'SELECT m.idPost, count(*) ';
    $sql .= ' FROM m152.media as m ';
    $sql .= ' GROUP BY idPost;';

    if ($ps == null) {
        $ps = m152DB()->prepare($sql);
    }
    $answer = false;
    try {
        if ($ps->execute())
            $answer = $ps->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $answer;
}

	  function createMediaAndPost($typeMedia, $nomMedia, $creationDate, $commentaire, $alreadyLoop)
{
    static $ps = null;
    $answer = false;
    try {
        m152DB()->beginTransaction();

        if ($alreadyLoop == 0) {
            $sql = "INSERT INTO `m152`.`POST` (`commentaire`, `creationDate`) ";
            $sql .= "VALUES (:COMMENTAIRE, :CREATIONDATE)";
            $ps = m152DB()->prepare($sql);
            $ps->bindParam(':COMMENTAIRE', $commentaire, PDO::PARAM_STR);
            $ps->bindParam(':CREATIONDATE', $creationDate, PDO::PARAM_STR);
            $answer = $ps->execute();
            $ps->close;
        }

        $sql = "INSERT INTO `m152`.`MEDIA` (`typeMedia`, `nomMedia`, `creationDate`, `idPost`) ";
        $sql .= "VALUES (:TYPEMEDIA, :NOMMEDIA, :CREATIONDATE, :IDPOST)";
        $ps = m152DB()->prepare($sql);
        $ps->bindParam(':TYPEMEDIA', $typeMedia, PDO::PARAM_STR);
        $ps->bindParam(':NOMMEDIA', $nomMedia, PDO::PARAM_STR);
        $ps->bindParam(':CREATIONDATE', $creationDate, PDO::PARAM_STR);
        $ps->bindParam(':IDPOST', getLastId(), PDO::PARAM_INT);
        $answer = $ps->execute();
        $ps->close;

        m152DB()->commit();
    } catch (PDOException $e) {
        echo $e->getMessage();
        m152DB()->rollBack();
    }
    return $answer;
}

function readPostAndMediaWithId($id)
{
    static $ps = null;
    $sql = 'SELECT m.idPost, m.nomMedia, m.typeMedia, p.commentaire ';
    $sql .= 'FROM m152.media as m, m152.post as p ';
    $sql .= 'WHERE m.idPost = p.idPost AND p.idPost = :ID ';

    if ($ps == null) {
        $ps = m152DB()->prepare($sql);
    }
    $answer = false;
    try {
        $ps->bindParam(':ID', $id, PDO::PARAM_STR);
        if ($ps->execute())
            $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $answer;
}

function PostAndMediaToCarousel()
{
    $html = "";
    $array = getCountFromDifferentIdPost();
    if (!empty($array)) {
        // Chaque ligne
        for ($i = getLastId(); $i > 0; $i--) {
            $arrayMedia = readPostAndMediaWithId($i);
            $html .= "\n <div class=\"panel panel-default\">";
            $html .= "\n <div id=\"my-pics$i\" class=\"carousel slide\" data-ride=\"carousel\" data-interval=\"false\" style=\"margin:auto;\" >";

            $html .= "\n <ol class=\"carousel-indicators\">";

            for ($j = 1; $j < $array[$i]["count(*)"] + 1; $j++) {
                $html .= "\n <li data-target=\"#my-pics$i\" data-slide-to=\"$i\" class=\"active\"></li>";
            }

            $html .= "\n </ol>";
            $html .= "\n <div class=\"carousel-inner\" role=\"listbox\">";

            for ($k = 0; $k < $array[$i]["count(*)"]; $k++) {
                if ($k == 0) {
                    $html .= "\n <div align=\"center\" class=\"item active\">";
                } else {
                    $html .= "\n <div align=\"center\" class=\"item\">";
                }
                if ($arrayMedia[$k]["typeMedia"] == "mp4" || $arrayMedia[$k]["typeMedia"] == "m4v") {
                    $html .= "\n <video width=\"100%\" height=\"100%\" autoplay loop controls>";
                    $html .= "\n <source src=\"img/" . $arrayMedia[$k]["nomMedia"] . "\" type=\"video/mp4\">";
                    $html .= "\n </video>";
                    $html .= "\n </div>";
                }
                if ($arrayMedia[$k]["typeMedia"] == "png" || $arrayMedia[$k]["typeMedia"] == "jpg" || $arrayMedia[$k]["typeMedia"] == "jpeg" || $arrayMedia[$k]["typeMedia"] == "gif" || $arrayMedia[$k]["typeMedia"] == "jpg") {
                    $html .= "\n <img src=\"img/" . $arrayMedia[$k]["nomMedia"] . "\" alt=\"" . $arrayMedia[$k]["nomMedia"] . "\">";
                    $html .= "\n </div>";
                }

                if ($arrayMedia[$k]["typeMedia"] == "mp3" || $arrayMedia[$k]["typeMedia"] == "wav" || $arrayMedia[$k]["typeMedia"] == "ogg") {
                    $html .= "\n <audio controls autoplay";
                    $html .= "\n <source src=\"img/" . $arrayMedia[$k]["nomMedia"] . "\" type=\"video/mp4\">";
                    $html .= "\n </audio>";
                    $html .= "\n </div>";
                }
            }
            $html .= "\n </div>";

            if ($array[$i]["count(*)"] > 1) {
                $html .= "\n <a class=\"left carousel-control\" href=\"#my-pics$i\" role=\"button\" data-slide=\"prev\">";
                $html .= "\n <span class=\"icon-prev\" aria-hidden=\"true\"></span>";
                $html .= "\n <span class=\"sr-only\">Previous</span>";
                $html .= "\n </a>";
    
                $html .= "\n <a class=\"right carousel-control\" href=\"#my-pics$i\" role=\"button\" data-slide=\"next\">";
                $html .= "\n <span class=\"icon-next\" aria-hidden=\"true\"></span>";
                $html .= "\n <span class=\"sr-only\">Next</span>";
                $html .= "\n </a>";
            }

            $html .= "\n </div>";

            $html .= "\n <div class=\"panel-body\">";
            $html .= "\n <hr>";
            $html .= "\n " . $arrayMedia[0]["commentaire"];
            $html .= "\n <a ><span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span></a>";
            $html .= "\n <a><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></a>";
            $html .= "\n </div>";

            $html .= "\n </div>";
        }
    }
    return $html;
}
}

?>