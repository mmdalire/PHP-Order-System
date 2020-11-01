//Open order items modal
$('#table-order-open').on('click', function(e) {
    $('#table-products-modal').modal('show');
})

//Close order items modal
$('#table-order-close').on('click', function(e) {
    $('#table-products-modal').modal('hide');
})

//Select option in products
$('#product-list').on('change', function(e) {
    var productId = parseInt($(this).val());

    e.preventDefault();

    //Disabled quantity input and add order button if no product id
    if(productId !== 0) {
        $('#product-quantity').removeAttr('disabled');
        $('.btn-add-order-item').removeAttr('disabled');
    } else {
        $('#product-quantity').attr('disabled', 'disabled');
        $('.btn-add-order-item').attr('disabled', 'disabled');
    }

    $.ajax({
        url: site_url + 'customer/getProductId/' + productId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            //If the product is unavailable
            if(data.status_id === '6' || productId === 0) {
                $('#product-quantity').attr('disabled', 'disabled');
                $('.btn-add-order-item').attr('disabled', 'disabled');
            } 
            else {
                $('#product-quantity').removeAttr('disabled');
                $('.btn-add-order-item').removeAttr('disabled');
            }

            $('#product-price').val(data.price);
            $('#product-availability').val(data.status_name);
        },
        error: function(error) {
            alert(error);
        }
    })
});

//Add order item
$('.btn-add-order-item').on('click', function(e) {
    e.preventDefault();

    //If the quantity field is empty
    if($('#product-quantity').val() === '') {
        alert('Please fill the quantity first!');
        return;
    }

    //If the quantity field is not a number
    if(isNaN($('#product-quantity').val())) {
        alert('Please enter a number!');
        return;
    }

    var productId = parseInt($('#product-list').val());
    var productName = $("#product-list option:selected").text()
    var price = parseInt($('#product-price').val());
    var quantity = parseInt($('#product-quantity').val());
    var orderItem = {
        'productId': productId,
        'productName': productName,
        'price': price,
        'quantity': quantity,
        'subtotal': price * quantity
    };
    currentOrderItems.push(orderItem);
    productIdList.push(orderItem.productId);
    clearOrderFields();
    viewOrderItemFields();
    
    //Remove the selected product in the drop down
    removeSelectedProduct(productIdList);
})

//Delete order item
$(document).on('click', '.btn-order-delete', function(e) {
    var lineId = parseInt($(this).attr('line-number-id'));
    var selectedProductId = parseInt($('tr#' + lineId).attr('product-number-id'));

    //Order item will be removed from the list
    for(var i = 0; i < currentOrderItems.length; i++) {
        if(i === lineId) {
            currentOrderItems.splice(i, 1);
            productIdList.splice(productIdList.indexOf(selectedProductId), 1);
            removeSelectedProduct(productIdList);
            break;
        }
    }  

    viewOrderItemFields();
})

//Change quantity in a product
$(document).on('blur', '.change-quantity-input', function(e) {
    var productId = parseInt($(this).attr('change-quantity-productid'));
    var newQuantity = parseInt($(this).val());
    
    //Change the quantity and the subtotal
    for(var i = 0; i < currentOrderItems.length; i++) {
        if(currentOrderItems[i].productId === productId) {
            currentOrderItems[i].quantity = newQuantity;
            currentOrderItems[i].subtotal = currentOrderItems[i].quantity * currentOrderItems[i].price;
            viewOrderItemFields();
            break;
        }
    }
})

//Save order items to database
$('#btn-save-order-items').on('click', function(e) {
    var username = $('.greeting-username').html();
    e.preventDefault();

    //Clear first
    $('.order-list-table').find('tbody').html('');
    $('#order-total-row').html(0);

    $.ajax({
        url: site_url + 'customer/addOrderItems',
        type: 'POST',
        dataType: 'json',
        data: {
            'username': username,
            'orderItems': currentOrderItems
        },
        success: function(data) {
            if(data.errorCode === -1) {
                alert(data.error);
                viewOrderItemFields();
                return;
            }
            alert(data);
            $('#table-products-modal').modal('hide');
            
            //Empty fields
            currentOrderItems = [];
            productIdList = [];

            //Clear all tables and fields necessary
            clearOrderFields();
            reloadOrderTable(username);
            viewOrderItemFields();
            viewAllProductsTable();
        },
        error: function(error) {
            alert(error);
        }
    })
})

//Open order transaction modal
$(document).on('click', '#btn-view-order-items', function() {
    $('#view-order-modal').modal('show');

    //Get the order id of that transaction 
    var orderId = parseInt($(this).attr('order-row-id-view'));
    
    $.ajax({
        url: site_url + 'customer/getOrderTransaction/' + orderId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var total = 0;

            //Clear table first
            $('.view-order-number span').html(orderId);
            $('.view-order-date span').html(data[0].order_date);

            //Populate the table
            $('.view-order-table').find('tbody').html('');
            for(let i = 0; i < data.length; i++) {
                //Compute the total
                total += parseInt(data[i].subtotal);

                $('.view-order-table').find('tbody')
                .append($('<tr>')
                    .append($('<td>').html(data[i].line_number))
                    .append($('<td>').html(data[i].product_name))
                    .append($('<td>').html(data[i].price)
                        .attr('class', 'text-center')
                    )
                    .append($('<td>').html(data[i].quantity)
                        .attr('class', 'text-center')
                    )
                    .append($('<td>')
                        .attr('class', 'text-right')
                        .html(data[i].subtotal)
                    )
                )
            }
            $('#order-transation-total-row').html(total);
            total = 0;
        },
        error: function(error) {
            alert(error);
        }
    });
})

//Delete transaction record
$(document).on('click', '#btn-delete-order-items', function() {
    //Get the order id of that transaction 
    var orderId = parseInt($(this).attr('order-row-id-delete'));

    //Get username
    var username = $('.greeting-username').html();

    //Confirmation
    if(confirm("Are you sure you want to delete this transaction?")) {
        $.ajax({
            url: site_url + 'customer/deleteOrderTransaction/' + orderId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                alert(data);
                reloadOrderTable(username);
            },
            error: function(error) {
                alert(error);
            }
        });
    }
}) 

function viewOrderItemFields() {
    //Clear first
    $('.order-list-table').find('tbody').html('');

    //Iterate each row
    for(var i = 0; i < currentOrderItems.length; i++) {
        $('.order-list-table').find('tbody')
        .append($('<tr>')
        .attr('product-number-id', currentOrderItems[i].productId)
        .attr('id', i)
            .append(($('<td>')).append(i+1))
            .append(($('<td>'))
                .append(currentOrderItems[i].productName)
            )
            .append(($('<td>')).append(currentOrderItems[i].price))
            .append(($('<td class="col-sm-2">'))
                .append($('<input>')
                    .attr('type', 'number')
                    .attr('min', '1')
                    .attr('class', 'form-control change-quantity-input')
                    .attr('change-quantity-productId', currentOrderItems[i].productId)
                    .val(currentOrderItems[i].quantity)    
                )
            )
            .append(($('<td>'))
                .attr('class', 'text-center')
                .append($('<button>')
                    .attr('class', 'btn btn-danger btn-order-delete')
                    .attr('line-number-id', i)   
                    .html('Delete order') 
                )
            )
            .append(($('<td>'))
                .attr('class', 'text-right subtotal')
                .attr('id', 'subtotal-' + currentOrderItems[i].productId)
                .append(currentOrderItems[i].subtotal))
        );
    }

    var total = 0;
    for(var i = 0; i < currentOrderItems.length; i++) {
        total += currentOrderItems[i].subtotal;
    }
    $('#order-total-row').html(total);

    //Save button enabled
    if(currentOrderItems.length !== 0) {
        $('#btn-save-order-items').removeAttr('disabled');
    }  
    else {
        $('#btn-save-order-items').attr('disabled', 'disabled');
    }
}

function viewAllProductsTable() {
    $.ajax({
        url: site_url + 'customer/reloadProductsList',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var select = $("#product-list");
            //Append the choose product option
            select.html('');
            select.append($('<option>')
                .attr('value', 0)
                .html('Choose product')
            );

            //Display the updated select box
            for(var i = 0; i < data.length; i++) {
                select.append($('<option>')
                    .attr('value', data[i].product_id)
                    .text(data[i].product_name)
                );
            }
        }
    });
}

function reloadOrderTable(username) {
    $.ajax({
        url: site_url + 'customer/reloadOrder/' + username,
        type: 'GET',
        dataType: 'html',
        success: function(data) {
            $('.table-orders').html(data);
        }
    });
}

function clearOrderFields() {
    $('#product-list').val(0);
    $('#product-price').val('');
    $('#product-availability').val('');
    $('#product-quantity').val('');

    $('#product-quantity').attr('disabled', 'disabled');
    $('.btn-add-order-item').attr('disabled', 'disabled');
    $('#btn-save-order-items').attr('disabled', 'disabled');
}

function removeSelectedProduct(productIdList) {
    $.ajax({
        url: site_url + 'customer/removeSelectedProduct',
        type: 'POST',
        dataType: 'json',
        data: {
            'productIdList': productIdList
        },
        success: function(data) {
            var select = $("#product-list");
            //Append the choose product option
            select.html('');
            select.append($('<option>')
                .attr('value', 0)
                .html('Choose product')
            );

            //Display the updated select box
            for(var i = 0; i < data.length; i++) {
                select.append($('<option>')
                    .attr('value', data[i].product_id)
                    .text(data[i].product_name)
                );
            }
        } 
    });
}

var currentOrderItems = [];
var productIdList = [];