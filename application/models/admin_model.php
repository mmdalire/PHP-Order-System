<?php
class Admin_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function viewPendingAdmins() {
        $query = 
            "SELECT u.user_id, u.first_name, u.last_name, u.username, ut.user_type, s.status_name, u.created_date
            FROM user u
            JOIN status s
                ON u.user_status_id = s.status_id
            JOIN user_type ut
                USING (user_type_id)
            WHERE ut.user_type_id = 1 AND s.status_name = 'Pending'
            ORDER BY u.user_id DESC;";
        
        $data = $this->db->query($query);
        return $data->result();
    }

    public function viewPendingCustomers() {
        $query = 
            "SELECT u.user_id, u.first_name, u.last_name, u.username, ut.user_type, s.status_name, u.created_date
            FROM user u
            JOIN status s
                ON u.user_status_id = s.status_id
            JOIN user_type ut
                USING (user_type_id)
            WHERE ut.user_type_id = 2 AND s.status_name = 'Pending'
            ORDER BY u.user_id DESC;"
        ;
        
        $data = $this->db->query($query);
        return $data->result();
    }

    public function makeUserActive($id, $username) {
        $query = 
            "UPDATE user
            SET user_status_id = 1, updated_by = '" . $username . "', updated_date = NOW()
            WHERE user_id = " . $id . ";"
        ;
        
        if($this->db->query($query)) {
            return TRUE;
        }
        return FALSE;
    }

    public function makeUserInactive($id, $username) {
        $query = 
            "UPDATE user
            SET user_status_id = 2, updated_by = '" . $username . "', updated_date = NOW()
            WHERE user_id = " . $id . ";"
        ;
        
        if($this->db->query($query)) {
            return TRUE;
        }
        return FALSE;
    }

    public function countPendingUsers() {
        $query = 
            "SELECT COUNT(*)
            FROM user u
            JOIN status s
                ON u.user_status_id = s.status_id
            JOIN user_type ut
                USING (user_type_id)
            WHERE s.status_name = 'Pending';"
        ;

        $count = $this->db->query($query);
        return $count->row();
    }

    public function countPendingAdmins() {
        $query = 
            "SELECT COUNT(*)
            FROM user u
            JOIN status s
                ON u.user_status_id = s.status_id
            JOIN user_type ut
                USING (user_type_id)
            WHERE s.status_name = 'Pending' AND user_type_id = 1;"
        ;

        $count = $this->db->query($query);
        return $count->row();
    }

    public function countPendingCustomers() {
        $query = 
            "SELECT COUNT(*)
            FROM user u
            JOIN status s
                ON u.user_status_id = s.status_id
            JOIN user_type ut
                USING (user_type_id)
            WHERE s.status_name = 'Pending' AND user_type_id = 2;"
        ;

        $count = $this->db->query($query);
        return $count->row();
    }

    public function viewUserType($id) {
        $query = 
            "SELECT user_type_id 
            FROM user
            WHERE user_id = " . $id . ";"
        ;

        $userType = $this->db->query($query);
        return $userType->row();
    }

    public function getProductById($id) {
        $query = 
            "SELECT * 
            FROM product
            WHERE product_id = " . $id . ";"
        ;

        $data = $this->db->query($query);
        return $data->row();
    }

    public function viewAllProducts() {
        $query = 
            "SELECT *
            FROM product"
        ;

        $data = $this->db->query($query);
        return $data->result();
    }   

    public function addProduct($productName, $quantity, $price, $username) {
        $query = 
            "INSERT INTO product (product_name, quantity, price, created_by, created_date, status_id)
            VALUES ('" . $productName . "', " . $quantity . ", " . $price . ", '" . $username . "', NOW(), 5);"
        ;

        if($this->db->query($query)) {
            return TRUE;
        }
        return FALSE;
    }

    public function checkDuplicateProduct($productName) {
        $query = $this->db->query(
            "SELECT *
            FROM product
            WHERE product_name = '" . $productName . "'
            LIMIT 1;"
        );

        if(count($query->result()) > 0) {
            return TRUE;
        } 
        return FALSE;
    }

    public function updateProduct($productName, $quantity, $price, $username, $productId) {
        $query = 
            "UPDATE product
            SET product_name = '" . $productName . "', quantity = " . $quantity . ", price = " . $price . ", updated_by = '" . $username . "', updated_date = NOW(), status_id = 5
            WHERE product_id = " . $productId . ";"
        ;

        if($this->db->query($query)) {
            return TRUE;
        }
        return FALSE;
    }

    public function deleteProductId($productId) {
        //Check if the product if it is being bought by customers
        $selectQuery = 
            "SELECT COUNT(*) AS productCount
            FROM order_items
            WHERE product_id = " . $productId . ";"
        ;

        $count = (array)$this->db->query($selectQuery)->row();
        if($count['productCount'] > 0) {
            return FALSE;
        }
        
        //Delete the product when it is not being bought by customers
        $deleteProductQuery = 
            "DELETE FROM product
            WHERE product_id = " . $productId . ";"
        ;

        if($this->db->query($deleteProductQuery)) {
            return TRUE;
        }
        return FALSE;
    }

    public function viewAllOrders() {
        $query = 
            "SELECT u.username, o.order_id, o.order_by, o.order_date
            FROM orders o
            JOIN user u
                ON o.order_by = u.user_id;"
        ;

        $data = $this->db->query($query);
        return $data->result();
    }

    public function viewOrdersTotal() {
        $query = 
            "SELECT SUM(oi.subtotal) AS total
            FROM orders o
            JOIN order_items oi
                USING(order_id);"
        ;

        $data = $this->db->query($query);
        return $data->row();
    }

    public function viewOrdersCount() {
        $query = 
            "SELECT COUNT(*) AS countUsers
            FROM orders;"
        ;

        $data = $this->db->query($query);
        return $data->row();
    }

    public function viewOrderTransaction($orderId) {
        $query = 
            "SELECT u.username, oi.line_number, o.order_date, p.product_name, oi.price, oi.quantity, oi.subtotal
            FROM orders o
            JOIN order_items oi
                USING(order_id)
            JOIN product p
                USING(product_id)
            JOIN user u
                ON o.order_by = u.user_id
            WHERE order_id = " . $orderId . ";"
        ;

        $data = $this->db->query($query);
        return $data->result();
    }

    public function getOrdersFromProduct($productId) {
        $query = 
            "SELECT u.username, o.order_date, oi.quantity, oi.order_items_id
            FROM order_items oi
            JOIN orders o
                USING(order_id)
            JOIN user u
                ON o.order_by = u.user_id
            WHERE product_id = " . $productId . ";"
        ;

        $data = $this->db->query($query);
        return $data->result();
    }
}