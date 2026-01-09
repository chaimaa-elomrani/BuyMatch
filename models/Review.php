<?php
class Review {
    private $id;
    private $userId;
    private $productId;
    private $rating;
    private $comment;
    private $createdAt;
    private $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId(){
        return $this->id;
    }

    public function getUserId(){
        return $this->userId;
    }

    public function getProductId(){
        return $this->productId;
    }

    public function getRating(){
        return $this->rating;
    }

    public function getComment(){
        return $this->comment;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }

    public function setUserId($userId){
        $this->userId = $userId;
    }

    public function setProductId($productId){
        $this->productId = $productId;
    }

    public function setRating($rating){
        $this->rating = $rating;
    }

    public function setComment($comment){
        $this->comment = $comment;
    }

    public function save(){
        $stmt = $this->db->prepare("
            INSERT INTO reviews (user_id, product_id, rating, comment, created_at) 
            VALUES (:user_id, :product_id, :rating, :comment, NOW())
        ");
        $stmt->bindParam(':user_id', $this->userId); // bindparam ; it's like linking the variable to the placeholder in the sql query
        $stmt->bindParam(':product_id', $this->productId);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':comment', $this->comment);
        return $stmt->execute();
    }
}