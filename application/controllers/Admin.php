<?php
class Admin extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('admin_model');

        if(!$this->session->userdata('userType')) {
            redirect('login');
        }
        else {
            //Check whenever the customer tries to access the admin page
            if($this->session->userdata('userType') == 2 && $this->session->userdata('currentUser') == 'customer') {
                redirect('customer');
            }
        }
    }

    public function index() {
        $session = $this->session->userdata();

        //For first loading of pending applications in modal
        $loadPendingAdminTable = $this->admin_model->viewPendingAdmins();
        $loadPendingCustomerTable = $this->admin_model->viewPendingCustomers();
        $countPendingUsers = (array) $this->admin_model->countPendingUsers();
        $countPendingAdmins = (array) $this->admin_model->countPendingAdmins();
        $countPendingCustomers = (array) $this->admin_model->countPendingCustomers();

        //For first loading of products table 
        $loadProductsTable = $this->admin_model->viewAllProducts();

        //For first loading of orders table
        $loadOrdersTable = $this->admin_model->viewAllOrders();
        $loadOrdersTotal = (array) $this->admin_model->viewOrdersTotal();
        $loadOrdersCount = (array) $this->admin_model->viewOrdersCount();

        //Sending data to view
        $viewData = array(
            'loadAdminTable'        =>  $loadPendingAdminTable,
            'loadCustomerTable'     =>  $loadPendingCustomerTable,
            'countPendingUsers'     =>  $countPendingUsers['COUNT(*)'] == 0 ? '' : $countPendingUsers['COUNT(*)'], 
            'countPendingAdmins'    =>  $countPendingAdmins['COUNT(*)'] == 0 ? '' : $countPendingAdmins['COUNT(*)'],
            'countPendingCustomers' =>  $countPendingCustomers['COUNT(*)'] == 0 ? '' : $countPendingCustomers['COUNT(*)'],
            'loadProductTable'      =>  $loadProductsTable,
            'loadOrdersTable'       =>  $loadOrdersTable,
            'loadOrdersTotal'       =>  $loadOrdersTotal['total'],
            'loadOrdersCount'       =>  $loadOrdersCount['countUsers'],
            'session'               =>  $session
        );

        $this->load->view('header');
        $this->load->view('admin/admin', $viewData);
        $this->load->view('footer');
    }

    public function logOut() {
        $this->session->sess_destroy();
        redirect('login');
    }

    public function viewPendingAdmins() {
        $data['admins'] = $this->admin_model->viewPendingAdmins();
        $this->load->view('admin/adminsTable', $data);
    }

    public function viewPendingCustomers() {
        $data['customers'] = $this->admin_model->viewPendingCustomers();
        $this->load->view('admin/customersTable', $data);
    }

    public function makeUserActive($id, $username) {
        if($this->admin_model->makeUserActive($id, $username)) {
            $userType = (array) $this->admin_model->viewUserType($id);
            $msg = array(
                'message'       =>  'User successfully approved!'
            );

            //Admin
            if($userType['user_type_id'] == 1) {
                $countAdmin = (array) $this->admin_model->countPendingAdmins();
                $countTotal = (array) $this->admin_model->countPendingUsers();
                $msg['countAdmin'] = $countAdmin['COUNT(*)'] > 0 ? $countAdmin['COUNT(*)'] : '';
                $msg['countTotal'] = $countTotal['COUNT(*)'] > 0 ? $countTotal['COUNT(*)'] : '';
                echo json_encode($msg);
            }
            //Customer
            else {
                $countCustomer = (array) $this->admin_model->countPendingCustomers();
                $countTotal = (array) $this->admin_model->countPendingUsers();
                $msg['countCustomer'] = $countCustomer['COUNT(*)'] > 0 ? $countCustomer['COUNT(*)'] : '';
                $msg['countTotal'] = $countTotal['COUNT(*)'] > 0 ? $countTotal['COUNT(*)'] : '';
                echo json_encode($msg);
            }
        } 
        else {
            echo json_encode('Network error!');
        }
    }

    public function makeUserInactive($id, $username) {
        if($this->admin_model->makeUserInactive($id, $username)) {
            $userType = (array) $this->admin_model->viewUserType($id);
            $msg = array(
                'message'       =>  'User successfully disapproved!'
            );

            //Admin
            if($userType['user_type_id'] == 1) {
                $countAdmin = (array) $this->admin_model->countPendingAdmins();
                $countTotal = (array) $this->admin_model->countPendingUsers();
                $msg['countAdmin'] = $countAdmin['COUNT(*)'] > 0 ? $countAdmin['COUNT(*)'] : '';
                $msg['countTotal'] = $countTotal['COUNT(*)'] > 0 ? $countTotal['COUNT(*)'] : '';
                echo json_encode($msg);
            }
            //Customer
            else {
                $countCustomer = (array) $this->admin_model->countPendingCustomers();
                $countTotal = (array) $this->admin_model->countPendingUsers();
                $msg['countCustomer'] = $countCustomer['COUNT(*)'] > 0 ? $countCustomer['COUNT(*)'] : '';
                $msg['countTotal'] = $countTotal['COUNT(*)'] > 0 ? $countTotal['COUNT(*)'] : '';
                echo json_encode($msg);
            }
        } 
        else {
            echo json_encode('Network error!');
        }
    }

    public function getProductId($id) {
        $data = $this->admin_model->getProductById($id);
        echo json_encode($data);
    }

    public function viewAllProducts() {
        $data['products'] = $this->admin_model->viewAllProducts();
        $this->load->view('admin/productsTable', $data);
    }

    public function enterProduct() {
        $productId = $_POST['productId'];
        $productName = trim($_POST['productName']);
        $quantity = (int)trim($_POST['quantity']);
        $price = (int)trim($_POST['price']);
        $username = trim($_POST['username']);

        //When some fields are empty
        if(empty($productName) || empty($quantity) || empty($price)) {
            echo json_encode('Must fill all fields!');
            return;
        }

        //Product name must not contain any special characters
        if(preg_match("/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\=\+\¬\`\]]-/", $productName)) {
            echo json_encode('Your product name must not contain any special characters!');
            return;
        }

        //Quantity should not be less than 1
        if($quantity < 1) {
            echo json_encode('Quantity must be greater than 0!');
            return;
        }

        //Price should not be less than 0
        if($price < 0) {
            echo json_encode('Price must not be less than 0!');
            return;
        }

        //Check if the product has duplicates
        if($this->admin_model->checkDuplicateProduct($productName) && $productId == 0) {
            echo json_encode('Product exists!');
            return;
        }

        //Inserting to database
        //If adding product
        if($productId == 0) {
            if($this->admin_model->addProduct($productName, $quantity, $price, $username)) {
                echo json_encode('Successfully added a product!');
            }
            else {
                echo json_encode('Network error!');
            }
        } 
        else {
            if($this->admin_model->updateProduct($productName, $quantity, $price, $username, $productId)) {
                echo json_encode('Successfully updated a product!');
            }
            else {
                echo json_encode('Network error!');
            }
        }
    }

    public function deleteProductId($productId) {
        $productCount = $this->admin_model->deleteProductId($productId);

        if($productCount) {
            echo json_encode('Successfully deteled product!');
        }
        else {
            echo json_encode('Cannot delete the product due to item being bought by customer!');
        }
    }

    public function viewOrderTransaction($orderId) {
        $data = (array) $this->admin_model->viewOrderTransaction($orderId);
        echo json_encode($data);
    }

    public function getOrdersFromProduct($productId) {
        $data = (array) $this->admin_model->getOrdersFromProduct($productId);
        echo json_encode($data);
    }
}