$('#table-pending-open').on('click', function(e) {
    $('#table-pending-modal').modal('show');
});

$('#table-pending-close').on('click', function(e) {
    $('#table-pending-modal').modal('hide');
});

$('#table-products-open').on('click', function(e) {
    $('#table-products-modal').modal('show');
});

$('#table-products-close').on('click', function(e) {
    $('#table-products-modal').modal('hide');
})

//Tabs
$('#admins-tab a').click(function (e) {
    e.preventDefault();
    
    $.ajax({
        url: site_url + 'admin/viewPendingAdmins',
        type: 'GET',
        dataType: 'html',
        success: function(data) {
            $('.table-pending-admins').html(data);
        }
    });
    $(this).tab('show');
});

$('#customers-tab a').click(function (e) {
    e.preventDefault();
    
    $.ajax({
        url: site_url + 'admin/viewPendingCustomers',
        type: 'GET',
        dataType: 'html',
        success: function(data) {
            $('.table-pending-customers').html(data);
        }
    });
    $(this).tab('show');
});

//Approve admin
$(document).on('click', '#admin-active-btn', function(e) {
    var id = $(this).attr('admin-id');
    var username = $('.greeting-username').html();

    $.ajax({
        url: site_url + 'admin/makeUserActive/' + id + '/' + username,
        type: 'GET',
        dataType: 'json',
        success: function(message) {
            $('.badge.badge-danger.admin-number').html(message.countAdmin);
            $('.badge.badge-danger.total-number').html(message.countTotal);

            $('#approve-modal').html(message.message);
            $("#approve-modal").show();
            reloadAdminTable();

            setTimeout(function() {
                $("#approve-modal").hide();
            }, 2000);
        },
        error: function(error) {
            $('#disapprove-modal').html(error);
            $('#disapprove-modal').show();

            setTimeout(function() {
                $("#disapprove-modal").hide();
            }, 2000);
        }
    });
});

//Disapprove admin
$(document).on('click', '#admin-inactive-btn', function(e) {
    var id = $(this).attr('admin-id');
    var username = $('.greeting-username').html();

    $.ajax({
        url: site_url + 'admin/makeUserInactive/' + id + '/' + username,
        type: 'GET',
        dataType: 'json',
        success: function(message) {
            $('.badge.badge-danger.admin-number').html(message.countAdmin);
            $('.badge.badge-danger.total-number').html(message.countTotal);

            $('#disapprove-modal').html(message.message);
            $('#disapprove-modal').show();

            setTimeout(function() {
                $("#disapprove-modal").hide();
            }, 2000);

            reloadAdminTable();
        },
        error: function(error) {
            $('#disapprove-modal').html(error);
            $('#disapprove-modal').show();

            setTimeout(function() {
                $("#disapprove-modal").hide();
            }, 2000);
        }
    });
});

//Approve customer
$(document).on('click', '#customer-active-btn', function(e) {
    var id = $(this).attr('customer-id');
    var username = $('.greeting-username').html();

    $.ajax({
        url: site_url + 'admin/makeUserActive/' + id + '/' + username,
        type: 'GET',
        dataType: 'json',
        success: function(message) {
            $('.badge.badge-danger.customer-number').html(message.countCustomer);
            $('.badge.badge-danger.total-number').html(message.countTotal);
            
            $('#approve-modal').html(message.message);
            $("#approve-modal").show();
            reloadCustomerTable();

            setTimeout(function() {
                $("#approve-modal").hide();
            }, 2000);
        },
        error: function(error) {
            $('#disapprove-modal').html(error);
            $('#disapprove-modal').show();

            setTimeout(function() {
                $("#disapprove-modal").hide();
            }, 2000);
        }
    });
});

//Disapprove customer
$(document).on('click', '#customer-inactive-btn', function(e) {
    var id = $(this).attr('customer-id');
    var username = $('.greeting-username').html();
    e.preventDefault();

    $.ajax({
        url: site_url + 'admin/makeUserInactive/' + id + '/' + username,
        type: 'GET',
        dataType: 'json',
        success: function(message) {
            $('.badge.badge-danger.customer-number').html(message.countCustomer);
            $('.badge.badge-danger.total-number').html(message.countTotal);
            
            $('#disapprove-modal').html(message.message);
            $('#disapprove-modal').show();

            setTimeout(function() {
                $("#disapprove-modal").hide();
            }, 2000);

            reloadCustomerTable();
        },
        error: function(error) {
            $('#disapprove-modal').html(error);
            $('#disapprove-modal').show();

            setTimeout(function() {
                $("#disapprove-modal").hide();
            }, 2000);
        }
    });
})

//Add products to products table
$('#enter-product').on('click', function(e) {
    e.preventDefault();
    var productId = $('#product-id').val();
    var productName = $('#product-name').val();
    var quantity = $('#product-quantity').val();
    var price = $('#product-price').val();
    var username = $('.greeting-username').html();

    //When some fields are empty
    if(productName === '' || quantity === '' || price === '') {
        $('#error-product-modal-msg strong').html('Must complete all fields!');
        $('#error-product-modal-msg').show();
        return;
    }

    //Product name validation (avoid special characters)
    if(/[ `!@#$%^&*()+\-=\[\]{};':"\\|,.<>\/?~]/.test(productName)) {
        $('#error-product-modal-msg strong').html('Product name must not contain any special characters!');
        $('#error-product-modal-msg').show();
        return;
    }

    //Quantity must be not be less than 1
    if(parseInt(quantity) < 1) {
        $('#error-product-modal-msg strong').html('Quantity must not be less than 1!');
        $('#error-product-modal-msg').show();
        return;
    }

    //price must be not be less than 0
    if(parseInt(price) < 0) {
        $('#error-product-modal-msg strong').html('Price must not be less than 0!');
        $('#error-product-modal-msg').show();
        return;
    }

    $.ajax({
        url: site_url + 'admin/enterProduct',
        type: 'POST',
        dataType: 'json',
        data: {
            'productId': productId,
            'productName': productName,
            'quantity': quantity,
            'price': price,
            'username': username
        },
        success: function(message) {
            alert(message);
            if(message !== 'Product exists!') {
                clearFields();
                $('#error-product-modal-msg').hide();
                $('#table-products-modal').modal('hide');

                reloadProductTable();
            }
        },
        error: function(error) {
            alert(error);
        }
    });
})

//View customers who order the product
$(document).on('click', '#btn-product-order', function(e) {
    e.preventDefault();
    $('#view-admin-order-products-modal').modal('show');

    var productId = $(this).attr('product-id');

    //Clear table
    $('.view-admin-order-products-table').find('tbody').html('');

    $.ajax({
        url: site_url + 'admin/getOrdersFromProduct/' + productId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            //Populate the table
            for(let i = 0; i < data.length; i++) {
                $('.view-admin-order-products-table').find('tbody')
                .append($('<tr>')
                    .append($('<td>').html(data[i].order_items_id))
                    .append($('<td>').html(data[i].username))
                    .append($('<td>').html(data[i].order_date))
                    .append($('<td>').html(data[i].quantity)
                        .attr('class', 'text-center')
                    )
                )
            }
        }
    })
});

//Edit products
$(document).on('click', '#btn-product-edit', function(e){
    e.preventDefault();
    var productId = $(this).attr('product-id');
    $.ajax({
        url: site_url + 'admin/getProductId/' + productId,
        type:'GET',
        dataType:'json',
        success:function(data){
            console.log(data);
            $('#product-id').val(data.product_id);
            $('#product-name').val(data.product_name);
            $('#product-quantity').val(data.quantity);
            $('#product-price').val(data.price);
            $('#table-products-modal').modal('show');
        },
    })  
});

//Delete product
$(document).on('click', '#btn-product-delete', function(e) {
    e.preventDefault();
    var productId = $(this).attr('product-id');
    if(confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: site_url + 'admin/deleteProductId/' + productId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                alert(data);
                reloadProductTable();
            },
            error: function(error) {
                alert(error);
            } 
        })
    }
})

//View orders
$(document).on('click', '#btn-admin-view-order-items', function(e) {
    var orderId = $(this).attr('order-admin-row-id-view');
    var orderTotal = 0;
    $('#view-admin-order-modal').modal('show');

    //Clear table first
    $('.view-admin-order-table').find('tbody').html('');

    $.ajax({
        url: site_url + 'admin/viewOrderTransaction/' + orderId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            //Populate the other fields
            $('.view-admin-order-number span').html(orderId);
            $('.view-admin-order-username span').html(data[0].username);
            $('.view-admin-order-date span').html(data[0].order_date);

            //Populate the table
            for(let i = 0; i < data.length; i++) {
                $('.view-admin-order-table').find('tbody')
                .append($('<tr>')
                    .append($('<td>').html(data[i].line_number))
                    .append($('<td>').html(data[i].product_name))
                    .append($('<td>').html(data[i].price)
                        .attr('class', 'text-center')
                    )
                    .append($('<td>').html(data[i].quantity)
                        .attr('class', 'text-center')
                    )
                    .append($('<td>').html(data[i].subtotal)
                        .attr('class', 'text-right')
                    )
                )

                orderTotal += parseInt(data[i].subtotal);
            }

            //Calculate total
            $('#order-admin-transation-total-row').html(orderTotal);
            orderTotal = 0;
        },
        error: function(error) {
            console.log(error);
        }
    });
})

function reloadAdminTable() {
    $.ajax({
        url: site_url + 'admin/viewPendingAdmins',
        type: 'GET',
        dataType: 'html',
        success: function(data) {
            $('.table-pending-admins').html(data);
        }
    });
}

function reloadCustomerTable() {
    $.ajax({
        url: site_url + 'admin/viewPendingCustomers',
        type: 'GET',
        dataType: 'html',
        success: function(data) {
            $('.table-pending-customers').html(data);
        }
    });
}

function reloadProductTable() {
    $.ajax({
        url: site_url + 'admin/viewAllProducts',
        type: 'GET',
        dataType: 'html',
        success: function(data) {
            $('.table-products').html(data);
        }
    });
}

function clearFields() {
    //Clear product fields
    $('#product-id').val(0);
    $('#product-name').val('');
    $('#product-quantity').val('');
    $('#product-price').val('');
}