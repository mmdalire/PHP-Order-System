<?php
class Login extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('login_model');

        if($this->session->userdata('userType')) {
            if($this->session->userdata('userType') == 1){
                redirect('admin');
            }
            if($this->session->userdata('userType') == 2){
                redirect('customer');
            }
        }
    }

    public function index() {
        $this->load->view('header');
        $this->load->view('login/login');
        $this->load->view('footer');
    }

    public function login() {
        $username = trim($_POST['login-username']);
        $password = trim($_POST['login-password']);

        if(isset($_POST['login-btn'])) {
            $data = $this->login_model->getLoginUser($username, $password);
            //If some fields are empty
            if(empty($username) ||empty($password)) {
                $error = array(
                    'msg'       =>  'Must complete all fields!',
                    'type'      =>  'danger'
                );

                $this->load->view('header');
                $this->load->view('errors/error', $error);
                $this->load->view('footer');
                return;
            }

            //If no account exists
            if(empty($data)) {
                $error = array(
                    'msg'       =>  'No user account exists! Create a new account first!',
                    'type'      =>  'danger'
                );

                $this->load->view('header');
                $this->load->view('errors/error', $error);
                $this->load->view('footer');
                return;
            }

            //If some credentials are incorrect
            if($username != $data['username'] || $password != $data['password']) {
                $error = array(
                    'msg'       =>  'Invalid username or password!',
                    'type'      =>  'danger'
                );

                $this->load->view('header');
                $this->load->view('errors/error', $error);
                $this->load->view('footer');
                return;
            }

            //If the account is still pending
            if($data['user_status_id'] == 3) {
                $error = array(
                    'msg'       =>  'Your account is still pending! Wait for your administrator to activate your account!',
                    'type'      =>  'warning'
                );

                $this->load->view('header');
                $this->load->view('errors/error', $error);
                $this->load->view('footer');
                return;
            }

            //If the account is already disapproved
            if($data['user_status_id'] == 2) {
                $error = array(
                    'msg'       =>  'Your account isn\'t activated by the administrator. Please contact administrator for more information.',
                    'type'      =>  'danger'
                );

                $this->load->view('header');
                $this->load->view('errors/error', $error);
                $this->load->view('footer');
                return;
            }

            //If either admin or customer
            $newSession = array(
                'firstName'     =>  $data['first_name'],
                'lastName'      =>  $data['last_name'],
                'username'      =>  $data['username'],
                'userType'      =>  $data['user_type_id'],
                'createdDate'   =>  date('F j, Y', strtotime($data['created_date']))
            );
            $this->session->set_userdata($newSession);

            if($data['user_type_id'] == 1) {
                $this->session->set_userdata('currentUser', 'admin');
                redirect('admin');
            } 
            else {
                $this->session->set_userdata('currentUser', 'customer');
                redirect('customer');
            }
        }
        else {
            redirect('login');
        }
    }

    public function register() {
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $username = trim($_POST['username']);
        $userType = trim($_POST['userType']);
        $password = trim($_POST['password']);
        $confirmedPassword = trim($_POST['confirmPassword']);
        $status = 3; //Pending status

        //When some fields are empty
        if(empty($firstName) || empty($lastName) || empty($username) || empty($userType) || empty($password) || empty($confirmedPassword)) {
            echo json_encode('Must fill all fields!');
            return;
        }

        //First name must not contain any special characters
        if(preg_match("/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\=\_+\¬\`\]]-/", $firstName)) {
            echo json_encode('Your first name must not contain any special characters!');
            return;
        }

        //Last name must not contain any special characters
        if(preg_match("/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\=\_+\¬\`\]]-/", $lastName)) {
            echo json_encode('Your last name must not contain any special characters!');
            return;
        }

        //Username must not contain any special characters
        if(preg_match("/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\=\+\¬\`\]]-/", $username)) {
            echo json_encode('Your username must not contain any special characters!');
            return;
        }

        //Password must be at least 8 characters long
        if(strlen($password) < 8) {
            echo json_encode('Password must be at least 8 characters long!');
            return;
        }

        //Password and confirmed password must be the same
        if($password != $confirmedPassword) {
            echo json_encode('Your password must be the same as confirmed password!');
            return;
        }

        //Check if there are duplicates
        if($this->login_model->checkDuplicateUser($username)) {
            echo json_encode('User account exists!');
            return;
        }

        //Inserting to database
        if($this->login_model->registerUser($firstName, $lastName, $userType, $password, $username, $status)) {
            echo json_encode('Successfully created an account. Please login.');
        }
        else {
            echo json_encode('Network error!');
        }
    }
}