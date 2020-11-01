<?php
class Customer_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function viewAllProductsName() {
        $query = 
            "SELECT product_id, product_name
            FROM product;"
        ;

        $data = $this->db->query($query);
        return $data->result();
    }

    public function viewAllOrders($username) {
        $query = 
            "SELECT u.username, o.order_id, o.order_by, o.order_date
            FROM orders o
            JOIN user u
                ON o.order_by = u.user_id
            WHERE u.username = '" . $username . "';"
        ;

        $data = $this->db->query($query);
        return $data->result();
    }

    public function getOrderTransaction($orderId) {
        $query = 
            "SELECT o.order_date, oi.line_number, p.product_name, p.price, oi.quantity, oi.subtotal
            FROM order_items oi
            JOIN orders o
                USING(order_id)
            JOIN product p
                USING(product_id)
            WHERE order_id = " . $orderId . ";"
        ;

        $data = $this->db->query($query);
        return $data->result();
    }

    public function deleteOrderTransaction($orderId) {
        //Delete from order_items table
        $queryOrderItems = 
            "DELETE FROM order_items
            WHERE order_id = " . $orderId . ";"
        ;

        if(!$this->db->query($queryOrderItems)) {
            return FALSE;
        }
        
        //Delete from orders table
        $queryOrder = 
            "DELETE FROM orders
            WHERE order_id = " . $orderId . ";"
        ;

        if(!$this->db->query($queryOrder)) {
            return FALSE;
        }

        return TRUE;
    }

    public function getTotalQuantity($productId) {
        $query = 
            "SELECT quantity
            FROM product
            WHERE product_id = " . $productId . ";"
        ;

        $count = $this->db->query($query);
        return $count->row();
    }

    public function getProductId($productId) {
        $query = 
            "SELECT p.product_id, p.product_name, p.quantity, p.price, status_id, s.status_name
            FROM product p
            JOIN status s
                USING (status_id)
            WHERE product_id = " . $productId . ";"
        ;

        $data = $this->db->query($query);
        return $data->row();
    }

    public function removeSelectedProduct($productIdList = []) {
        //If the order items are empty
        if(empty($productIdList)) {
            $query = 
                "SELECT *
                FROM product;"
            ;
            $data = $this->db->query($query);
            return $data->result();
        }

        //Otherwise
        $query = 
            "SELECT *
            FROM product
            WHERE product_id NOT IN ("
        ;

        for($i = 0; $i < count($productIdList); $i++) {
            $query = $query . $productIdList[$i];
            if($i === count($productIdList) - 1) {
                $query = $query . ');';
                break;
            }
            $query = $query . ',';
        }

        $data = $this->db->query($query);
        return $data->result();
    }

    public function getUserId($username) {
        $query = 
            "SELECT user_id
            FROM user
            WHERE username = '" . $username . "';"
        ;

        $userId = $this->db->query($query);
        return $userId->row();
    }

    public function addOrder($userId) {
        //Insert new transaction first
        $insertQuery = 
            "INSERT INTO orders (order_by, order_date)
            VALUES (" . $userId . ", NOW());"
        ;

        $this->db->query($insertQuery);

        //Select the latest transaction
        $selectQuery = 
            "SELECT *
            FROM orders
            ORDER BY order_id desc
            LIMIT 1;"
        ;

        $orderId = $this->db->query($selectQuery);
        return $orderId->row();
    }

    public function addToOrderItem($orderItem, $userId, $orderId) {
        $insertQuery =  
            "INSERT INTO order_items (line_number, product_id, price, quantity, subtotal, order_id)
            VALUES ";

        for($i = 0; $i < count($orderItem); $i++) {
            $lineNumber = $i + 1;

            $insertQuery = $insertQuery . "( ". $lineNumber . ", " . $orderItem[$i]['productId'] . ", " . $orderItem[$i]['price'] . ", " . $orderItem[$i]['quantity'] . ", " . $orderItem[$i]['subtotal'] . ", " . $orderId . ")";

            if($i === count($orderItem) - 1) {
                $insertQuery = $insertQuery . ";";
                break;
            }
            $insertQuery = $insertQuery . ",";
        }

        if($this->db->query($insertQuery)) {
            //Update the quantity in products table and subtotal at order items table
            for($i = 0; $i < count($orderItem); $i++) {
                $updateQuantityQuery = 
                    "UPDATE product p
                    JOIN order_items oi
                        USING(product_id)
                    SET p.quantity = p.quantity - oi.quantity
                    WHERE p.product_id = " . $orderItem[$i]['productId'] . "
                        AND oi.order_id = ". $orderId . ";"
                ;

                //If updating fails
                if(!$this->db->query($updateQuantityQuery)) {
                    return FALSE;
                }

                //Check if there are no more stock of the updated product
                // 6 - Not Available
                $checkStockQuery = 
                    "UPDATE product
                    SET status_id = 6 
                    WHERE product_id = " .  $orderItem[$i]['productId'] . " AND quantity = 0;"
                ;

                //If updating fails
                if(!$this->db->query($checkStockQuery)) {
                    return FALSE;
                }

            }
            return TRUE;
        }
        return FALSE;
    }
}