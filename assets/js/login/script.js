//Registration modal
//Open
$('#register-open-modal').on('click', function(e) {
    $('#register-modal').modal('show');
})

//Close
$('#register-close-modal').on('click', function(e) {
    $('#register-modal').modal('hide');
})

//Register
$('#register-btn').on('click', function(e) {
    var firstName = $('#register-first-name').val();
    var lastName = $('#register-last-name').val();
    var username = $('#register-username').val();
    var userType = $('input[name=register-user-type]:checked', '#registration-form').val();
    var password = $('#register-password').val();
    var confirmPassword = $('#register-confirm-password').val();

    e.preventDefault();
    //When some fields are empty
    if(firstName === '' || lastName === '' || username === '' || userType === undefined || password === '' || confirmPassword === '') {
        $('#error-modal-msg strong').html('Must complete all fields!');
        $('#error-modal-msg').show();
        return;
    }
    
    //First name validation (avoid special characters)
    if(/[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test(firstName)) {
        $('#error-modal-msg strong').html('First name must not contain any special characters!');
        $('#error-modal-msg').show();
        return;
    }

    //Last name validation (avoid special characters)
    if(/[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test(lastName)) {
        $('#error-modal-msg strong').html('Last name must not contain any special characters!');
        $('#error-modal-msg').show();
        return;
    }

    //Username validation (avoid special characters except underscore)
    if(/[ `!@#$%^&*()+\-=\[\]{};':"\\|,.<>\/?~]/.test(username)) {
        $('#error-modal-msg strong').html('Username must not contain any special characters!');
        $('#error-modal-msg').show();
        return;
    }

    //Password must be at least 8 characters long
    if(password.length < 8) {
        $('#error-modal-msg strong').html('Password must be at least 8 characters long!');
        $('#error-modal-msg').show();
        return;
    }

    //Password must be the same with confirmed password
    if(password !== confirmPassword) {
        $('#error-modal-msg strong').html('Both passwords must be the same!');
        $('#error-modal-msg').show();
        return;
    }

    $.ajax({
        url: site_url + 'login/register',
        type: 'POST',
        dataType: 'json',
        data: {
            'firstName': firstName,
            'lastName': lastName,
            'username': username,
            'userType': userType,
            'password': password,
            'confirmPassword': confirmPassword
        },
        success: function(message) {
            alert(message);
            if(message !== 'User account exists!') {
                clearFields();
                $('#error-modal-msg').hide();
                $('#register-modal').modal('hide');
            }
        },
        error: function(error) {
            alert(error);
        }
    });
})

function clearFields() {
    //Clear login fields
    $('#login-username').val('');
    $('#login-password').val('');

    //Clear register fields
    $('#register-first-name').val('');
    $('#register-last-name').val('');
    $('#register-username').val('');
    $('input[name=register-user-type]:checked', '#registration-form').val(1);
    $('#register-password').val('');
    $('#register-confirm-password').val('');
}
