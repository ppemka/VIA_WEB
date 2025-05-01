<?php
/**
 * Service Class
 * Handles CRUD operations for service entities
 */
class Service {
    // Database connection and table name
    private $conn;
    private $table_name = "services";
    
    // Object properties
    public $id;
    public $title;
    public $description;
    public $image;
    
    /**
     * Constructor with $db as database connection
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create a new service
     * @param string $title
     * @param string $desc
     * @param string $img
     * @return bool
     */
    public function create($title, $desc, $img) {
        // Sanitize inputs
        $this->title = htmlspecialchars(strip_tags($title));
        $this->description = htmlspecialchars(strip_tags($desc));
        $this->image = htmlspecialchars(strip_tags($img));
        
        // Insert query
        $query = "INSERT INTO " . $this->table_name . " 
                  SET title=:title, description=:description, image=:image";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
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
    
    /**
     * Read all services
     * @return PDOStatement
     */
    public function readAll() {
        // Select all query
        $query = "SELECT id, title, description, image 
                FROM " . $this->table_name . " 
                ORDER BY id DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Read single service
     * @param int $id
     * @return bool
     */
    public function readOne($id) {
        // Query to read single record
        $query = "SELECT id, title, description, image 
                FROM " . $this->table_name . " 
                WHERE id = ?
                LIMIT 0,1";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind id parameter
        $stmt->bindParam(1, $id);
        
        // Execute query
        $stmt->execute();
        
        // Get record
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set values to object properties
        if($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->image = $row['image'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Update service
     * @param int $id
     * @param string $title
     * @param string $desc
     * @param string|null $img
     * @return bool
     */
    public function update($id, $title, $desc, $img = null) {
        // Sanitize inputs
        $this->id = htmlspecialchars(strip_tags($id));
        $this->title = htmlspecialchars(strip_tags($title));
        $this->description = htmlspecialchars(strip_tags($desc));
        
        // If image is provided
        if($img) {
            $this->image = htmlspecialchars(strip_tags($img));
            
            // Update query with image
            $query = "UPDATE " . $this->table_name . " 
                    SET title=:title, description=:description, image=:image 
                    WHERE id=:id";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind values
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":image", $this->image);
            $stmt->bindParam(":id", $this->id);
        } else {
            // Update query without image
            $query = "UPDATE " . $this->table_name . " 
                    SET title=:title, description=:description 
                    WHERE id=:id";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind values
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":id", $this->id);
        }
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Delete service
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        // Sanitize id
        $this->id = htmlspecialchars(strip_tags($id));
        
        // Delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind id
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Search services by keyword
     * @param string $keyword
     * @return PDOStatement
     */
    public function search($keyword) {
        // Sanitize
        $keyword = htmlspecialchars(strip_tags($keyword));
        $keyword = "%{$keyword}%";
        
        // Search query
        $query = "SELECT id, title, description, image 
                FROM " . $this->table_name . " 
                WHERE title LIKE ? OR description LIKE ?
                ORDER BY id DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(1, $keyword);
        $stmt->bindParam(2, $keyword);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
}
?>