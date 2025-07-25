<?php
class AdminProductModel {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function getProducts($filters = []) {
        
        $query = "SELECT * FROM " . $this->table;

       
        $conditions = [];
        $params = [];

       
        if (isset($filters['type']) && !empty($filters['type'])) {
            $conditions[] = "type = :type";
            $params[':type'] = $filters['type'];
        }

        // Filter by status (in stock or out of stock)
        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'in_stock') {
                $conditions[] = "quantity > 0";
            } elseif ($filters['status'] === 'out_of_stock') {
                $conditions[] = "quantity = 0";
            }
        }

        // Filter by is_new
        if (isset($filters['is_new']) && $filters['is_new'] !== '') {
            $conditions[] = "is_new = :is_new";
            $params[':is_new'] = $filters['is_new'];
        }

        // Search by name or description
        if (isset($filters['search']) && !empty($filters['search'])) {
            $conditions[] = "(name LIKE :search OR description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Add order by
        $query .= " ORDER BY id ASC";

       
        $stmt = $this->conn->prepare($query);

        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function getProductById($id) {
        
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':id', $id);

        
        $stmt->execute();

        return $stmt->fetch();
    }

    
    public function getProductTypes() {
        
        $query = "SELECT DISTINCT type FROM " . $this->table . " ORDER BY type";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->execute();

        $types = [];
        while ($row = $stmt->fetch()) {
            $types[] = $row['type'];
        }

        return $types;
    }

    
    public function createProduct($data) {
        
        $query = "INSERT INTO " . $this->table . "
                  SET name = :name,
                      type = :type,
                      description = :description,
                      price = :price,
                      old_price = :old_price,
                      image_path = :image_path,
                      is_new = :is_new,
                      quantity = :quantity";

        
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind parameters
        $name = htmlspecialchars(strip_tags($data['name']));
        $type = htmlspecialchars(strip_tags($data['type']));
        $description = htmlspecialchars(strip_tags($data['description']));
        $price = (float)$data['price'];
        $oldPrice = !empty($data['old_price']) ? (float)$data['old_price'] : null;
        $imagePath = htmlspecialchars(strip_tags($data['image_path']));
        $isNew = isset($data['is_new']) ? (int)$data['is_new'] : 0;
        $quantity = (int)$data['quantity'];

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':old_price', $oldPrice);
        $stmt->bindParam(':image_path', $imagePath);
        $stmt->bindParam(':is_new', $isNew);
        $stmt->bindParam(':quantity', $quantity);

        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    
    public function updateProduct($id, $data) {
        
        $query = "UPDATE " . $this->table . "
                  SET name = :name,
                      type = :type,
                      description = :description,
                      price = :price,
                      old_price = :old_price,
                      image_path = :image_path,
                      is_new = :is_new,
                      quantity = :quantity
                  WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

        
        $name = htmlspecialchars(strip_tags($data['name']));
        $type = htmlspecialchars(strip_tags($data['type']));
        $description = htmlspecialchars(strip_tags($data['description']));
        $price = (float)$data['price'];
        $oldPrice = !empty($data['old_price']) ? (float)$data['old_price'] : null;
        $imagePath = htmlspecialchars(strip_tags($data['image_path']));
        $isNew = isset($data['is_new']) ? (int)$data['is_new'] : 0;
        $quantity = (int)$data['quantity'];

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':old_price', $oldPrice);
        $stmt->bindParam(':image_path', $imagePath);
        $stmt->bindParam(':is_new', $isNew);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id', $id);

       
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    
    public function deleteProduct($id) {
        
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':id', $id);

        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
