<body>
    <div class="container text-center">
        <div class="alert alert-danger collapse" id="error-main-msg">
            <strong></strong>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="text-center">Log In</h4>
            </div>
            <div class="panel-body">
                <?php
                    $attributes = array(
                        'id'        =>  'login-form',
                        'class'     =>  'form-horizontal',
                        'name'      =>  'login-form'
                    );
                    echo form_open('/login/login', $attributes);
                ?>
                    <div class="form-group">
                        <label class="control-label col-sm-5" for="login-username">Username:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="login-username" name="login-username" placeholder="Enter last name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-5" for="login-password">Password:</label>
                        <div class="col-sm-5">
                            <input type="password" class="form-control" id="login-password" name="login-password" placeholder="Enter password">
                        </div>
                    </div>
                    <div class="text-center">
                        <input type="submit" class="btn btn-primary" id="login-btn" name="login-btn" value="Login"></input>
                    </div>
                <?php echo form_close();?>
            </div>
            <div class="panel-footer text-center">
                <p>Don't have an account?</p>
                <button type="button" class="btn btn-primary" id="register-open-modal">Click here!</button>
            </div>
        </div>
    
        <div class="modal fade" id="register-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-center">
                        <button type="button" class="close" id="register-close-modal">&times;</button>
                        <h4 class="modal-title">Registration form</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger collapse" id="error-modal-msg">
                            <strong></strong>
                        </div>
                        <?php
                        $attributes = array(
                            'id'        =>  'registration-form',
                            'class'     =>  'form-horizontal',
                            'name'      =>  'registration-form'
                        );
                        echo form_open('', $attributes);
                        ?>
                        <div class="form-group">
                            <label class="control-label col-sm-5" for="register-first-name">First Name:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="register-first-name" placeholder="Enter first name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-5" for="register-last-name">Last Name:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="register-last-name" placeholder="Enter last name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-5" for="register-username">Username:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="register-username" placeholder="Enter username">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-5">User type:</label>
                            <div class="col-sm-5">
                                <div class="form-check col-sm-5">
                                    <input class="radio-inline" type="radio" name="register-user-type" id="register-admin-user" value="1">
                                    <label class="form-check-label" for="register-admin-user">
                                        Admin
                                    </label>
                                </div>
                                <div class="form-check col-sm-7">
                                    <input class="radio-inline" type="radio" name="register-user-type" id="register-customer-user" value="2">
                                    <label class="form-check-label" for="register-customer-user">
                                        Customer
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-5" for="register-password">Password:</label>
                            <div class="col-sm-5">
                                <input type="password" class="form-control" id="register-password" placeholder="Enter password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-5" for="register-confirm-password">Confirm Password:</label>
                            <div class="col-sm-5">
                                <input type="password" class="form-control" id="register-confirm-password" placeholder="Confirm password">
                            </div>
                        </div>
                        <?php echo form_close();?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="register-btn">Register</button>
                    </div>
                </div>
            </div>
        </div>
    </div>