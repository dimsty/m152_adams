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
	  public function insertImage($imageUser) {
	    $sql = 'INSERT INTO m152.MEDIA (nomMedia) VALUES (:nomMedia)';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['nomMedia' => $imageUser]);
	    return true;
	  }

	  //// Update an user in the database
	  //public function update($name, $lastName, $email, $dateNaissance, $id) {
	  //  $sql = 'UPDATE atap.personnes SET nom = :nom,prenom = :prenom,mail = :email, dateNaissance = :dateNaissance, idGroup = :idGroup WHERE idPersonne = :id';
	  //  $stmt = $this->conn->prepare($sql);
	  //  $stmt->execute(['id' => $id,'nom' => $lastName, 'prenom' => $name,  'email' => $email, 'dateNaissance' => $dateNaissance, 'idGroup' => 1]);
	  //  return true;
	  //}
//
	  //// Delete an user from database
	  //public function delete($id) {
	  //  $sql = 'DELETE FROM atap.personnes WHERE idPersonne = :id';
	  //  $stmt = $this->conn->prepare($sql);
	  //  $stmt->execute(['id' => $id]);
	  //  return true;
	  //}
	}

?>