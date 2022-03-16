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

        public function CheckCommentAndImageAreTheSamePost(){
            $req = 'SELECT idMedia, nomMedia, p.idPost, commentaire From MEDIA as m
            JOIN POST as p on m.idPost = p.idPost ORDER BY p.idPost DESC';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return true;
        }

        public function insertComment($commentaire) {
            $sql = 'INSERT INTO m152.POST (commentaire) VALUES (:commentaire)';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['commentaire' => $commentaire]);
            //var_dump($this->lastInsertId());

            //$lastID = $this->lastInsertId()
            echo "aaa";
            die;
            return true;
	  }

       public function GetLastId(){
        $sql = 'SELECT * FROM m152.POST';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $this->conn->lastInsertID();
       }

    }

?>