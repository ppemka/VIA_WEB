<?php
class Service {
    // Database connection and table name
    private $conn;
    private $table_name = "services";
    
    // Object properties
    public $id;
    public $title;
    public $description;
    public $image;
    
    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Read all services
    public function readAll() {
        // Select all query
        $query = "SELECT id, title, description, image FROM " . $this->table_name . " ORDER BY id DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read one service
    public function readOne() {
        // Query to read single record
        $query = "SELECT id, title, description, image FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind ID of service to be updated
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        $stmt->execute();
        
        // Fetch row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set properties
        if($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->image = $row['image'];
            return true;
        }
        
        return false;
    }
    
    // Create service
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET title=:title, description=:description, image=:image";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->image = htmlspecialchars(strip_tags($this->image));
        
        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Update service
    public function update() {
        // Query to update record
        $query = "UPDATE " . $this->table_name . " 
                  SET title=:title, description=:description";
        
        // If image is provided, include it in the update
        if(!empty($this->image)) {
            $query .= ", image=:image";
        }
        
        $query .= " WHERE id=:id";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":id", $this->id);
        
        // Bind image if provided
        if(!empty($this->image)) {
            $this->image = htmlspecialchars(strip_tags($this->image));
            $stmt->bindParam(":image", $this->image);
        }
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete service
    public function delete() {
        // Query to delete record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind id of record to delete
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Search services
    public function search($keywords) {
        // Sanitize
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
        
        // Query to search records
        $query = "SELECT id, title, description, image 
                  FROM " . $this->table_name . " 
                  WHERE title LIKE ? OR description LIKE ? 
                  ORDER BY id DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
}
?>