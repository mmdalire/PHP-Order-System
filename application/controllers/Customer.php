<?php
class Customer extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('customer_model');

        if(!$this->session->userdata('userType')) {
            redirect('login');
        }
        else {
            //Check whenever the admin tries to access the customer page
            if($this->session->userdata('userType') == 1 && $this->session->userdata('currentUser') == 'admin') {
                redirect('admin');
            }
        }
    }

    public function index() {
        $session = $this->session->userdata();

        //For first loading of products table
        $loadProductsTable = $this->customer_model->viewAllProductsName();
        $loadOrdersTable = $this->customer_model->viewAllOrders($session['username']);

        //Sending data to view
        $viewData = array(
            'loadProductsTable'     =>  $loadProductsTable,
            'loadOrdersTable'       =>  $loadOrdersTable,
            'session'               =>  $session
        );

        $this->load->view('header');
        $this->load->view('customer/customer', $viewData);
        $this->load->view('footer');
    }

    public function logOut() {
        $this->session->sess_destroy();
        redirect('login');
    }

    public function reloadOrder($username) {
        $data['orders'] = $this->customer_model->viewAllOrders($username);
        $this->load->view('customer/ordersTable', $data);
    }

    public function getOrderTransaction($orderId) {
        $data['order'] = (array)$this->customer_model->getOrderTransaction($orderId);
        echo json_encode($data['order']);
    }

    public function deleteOrderTransaction($orderId) {
        if($this->customer_model->deleteOrderTransaction($orderId)) {
            echo json_encode('Successfully deleted an order!');
        }
        else {
            echo json_encode('Network error!');
        }
    }

    public function reloadProductsList() {
        $data = $this->customer_model->viewAllProductsName();
        echo json_encode($data);
    }

    public function getProductId($productId) {
        if($productId == 0) {
            $noResult = array(
                'price'         =>  '',
                'status_name'   =>  ''
            );
            echo json_encode($noResult);
            return;
        }
        $data = $this->customer_model->getProductId($productId);
        echo json_encode($data);
    }

    public function removeSelectedProduct() {
        //If the order items are empty
        if(empty($_POST)) { 
            $data['products'] = $this->customer_model->removeSelectedProduct();
            echo json_encode($data['products']);
            return;
        }

        $productIdList = $_POST['productIdList'];
        $data['products'] = $this->customer_model->removeSelectedProduct($productIdList);
        echo json_encode($data['products']);
        return;
    }

    public function addOrderItems() {
        $userId = 0;
        $username = $_POST['username'];
        $orderItems = (array)$_POST['orderItems'];

        //Get user id from username
        $userId = (array)$this->customer_model->getUserId($username);

        for($i = 0; $i < count($orderItems); $i++) {
            //If the quantity isn't a number
            if(is_int($orderItems[$i]['quantity'])) {
                $error = 'Enter a valid number!';
                echo json_encode($error);
                return; 
            }

            //If ordered quantity is greater than the specified quantity
            $remainingQuantity = $this->customer_model->getTotalQuantity($orderItems[$i]['productId'])->quantity;

            if($remainingQuantity < $orderItems[$i]['quantity']) {
                $msg = array(
                    'errorCode' => -1,
                    'error'     => "The quantity of " . $orderItems[$i]['productName'] . " cannot be greater than the specified quantity (Remaining: " . $remainingQuantity . ")!"
                );

                echo json_encode($msg);
                return;
            }
        }

        //Add an order transaction
        $orderId = (array)$this->customer_model->addOrder($userId['user_id']);

        //Insert to order item table
        if($this->customer_model->addToOrderItem($orderItems, $userId['user_id'], $orderId['order_id'])) {
            echo json_encode('Successfully added to your order items!');
        }
        else {
            echo json_encode('Network error!');
        }
    }
}