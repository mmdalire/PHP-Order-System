<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand brand" href="<?php echo base_url();?>">Order System</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <?php echo $session['firstName'] . ' ' . $session['lastName'] . ' ';?>
            <span class="badge badge-danger total-number"><?php echo $countPendingUsers?></span> 
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            <li><a type="button" class="btn" id="table-pending-open">Pending new user accounts <span class="badge badge-danger total-number"><?php echo $countPendingUsers?></span></a></li>
            <li role="separator" class="divider"></li>
            <li><a href="<?php echo base_url()?>admin/logout"><strong>Logout</strong></a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <!-- Modal for pending users-->
  <div id="table-pending-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" id="table-pending-close">&times;</button>
          <h4 class="modal-title text-center">Pending accounts</h4>
        </div>
        <div class="modal-body">
          <div>
            <div class="alert alert-success text-center collapse" id="approve-modal"></div>
            <div class="alert alert-danger text-center collapse" id="disapprove-modal"></div>
              <!-- Nav tabs -->
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active" id="admins-tab"><a href="#admins"  role="tab">Admins <span class="badge badge-danger admin-number"><?php echo $countPendingAdmins;?></span></a></li>
                <li role="presentation" id="customers-tab"><a href="#customers" role="tab">Customers <span class="badge badge-danger customer-number"><?php echo $countPendingCustomers;?></span></a></li>
              </ul>

              <!-- Tab panes -->
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="admins">
                  <table class="table table-bordered table-pending-admins">
                    <thead class="bg-primary">
                      <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                        <th>Date created</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($loadAdminTable as $row) {?>
                          <tr>
                              <td><?php echo $row->first_name;?></td>
                              <td><?php echo $row->last_name;?></td>
                              <td><?php echo $row->username;?></td>
                              <td><?php echo $row->created_date;?></td>
                              <td class="text-center">
                                <button class="btn btn-success btn-sm" id="admin-active-btn" admin-id="<?php echo $row->user_id?>"><strong>&check;</strong></button>
                                <button class="btn btn-danger btn-sm" id="admin-inactive-btn" admin-id="<?php echo $row->user_id?>"><strong>&times;</strong></button>
                              </td>
                          </tr>
                      <?php }?>
                    </tbody>
                  </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="customers">
                  <table class="table table-bordered table-pending-customers">
                    <thead class="bg-primary">
                      <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                        <th>Date created</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    
                  </table>
                </div>
              </div>
            </div>
          </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for adding product-->
  <div id="table-products-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-center">
          <button type="button" class="close" id="table-products-close">&times;</button>
          <h4 class="modal-title">Add Product</h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger collapse" id="error-product-modal-msg">
              <strong></strong>
          </div>
          <?php
          $attributes = array(
              'id'        =>  'product-form',
              'class'     =>  'form-horizontal',
              'name'      =>  'product-form'
          );
          echo form_open('', $attributes);
          ?>
          <input type="hidden" name="product-id" id="product-id" value="0">
          <div class="form-group">
              <label class="control-label col-sm-5" for="product-name">Product Name:</label>
              <div class="col-sm-5">
                  <input type="text" class="form-control" id="product-name" placeholder="Enter product name">
              </div>
          </div>
          <div class="form-group">
              <label class="control-label col-sm-5" for="product-quantity">Quantity:</label>
              <div class="col-sm-5">
                  <input type="number" min="1" class="form-control" id="product-quantity" placeholder="Enter quantity">
              </div>
          </div>
          <div class="form-group">
              <label class="control-label col-sm-5" for="product-price">Price:</label>
              <div class="col-sm-5">
                  <input type="number" min="0" class="form-control" id="product-price" placeholder="Enter price">
              </div>
          </div>
          <?php echo form_close();?>
      </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="enter-product">Enter product</button>
        </div>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-body bg-primary">
      <h2>Hello <?php echo $session['firstName'] . ' ' . $session['lastName'];?>!</h2>
      <h4>Username: <span class="greeting-username"><?php echo $session['username'];?></span></h4>
      <h4>Admin since: <span class="greeting-username"><?php echo $session['createdDate'];?></span></h4>
    </div>
  </div>

<!--Tabs for orders and products -->
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#products">Products</a></li>
    <li><a data-toggle="tab" href="#orders">Orders</a></li>
  </ul>

  <div class="tab-content">
    <div id="products" class="tab-pane fade in active">
      <!--Products table-->
      <div>
        <div class="panel panel-default">
          <div class="panel-body text-center">
            <button class="btn btn-primary" id="table-products-open">Add Product</button>
          </div>
        </div>

        <table class="table table-bordered table-products">
          <thead class="bg-primary">
            <tr>
              <th class="text-center">ID</th>
              <th class="text-center">Product name</th>
              <th class="text-center">Quantity</th>
              <th class="text-center">Price</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($loadProductTable as $row) { ?>
              <tr>
                <td class="col-md-1"><strong><?php echo $row->product_id;?></strong></td>
                <td class="col-md-6"><strong><?php echo $row->product_name;?></strong></td>
                <td class="col-md-1"><strong><?php echo $row->quantity;?></strong></td>
                <td><strong><?php echo $row->price;?></strong></td>
                <td class="text-center">
                  <button class="btn btn-info" id="btn-product-order" product-id="<?php echo $row->product_id;?>">Orders</button>
                  <button class="btn btn-warning" id="btn-product-edit" product-id="<?php echo $row->product_id;?>">Edit</button>
                  <button class="btn btn-danger" id="btn-product-delete" product-id="<?php echo $row->product_id;?>">Delete</button>
                </td>
              </tr>
            <?php }?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div id="view-admin-order-products-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title text-center">Customers who ordered this product</h4>
          </div>
          <div class="modal-body">
            <table class="table table-bordered view-admin-order-products-table">
              <thead class="bg-primary">
                <th>Order ID</th>
                <th>Customer</th>
                <th>Order Date</th>
                <th>Quantity</th>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="orders" class="tab-pane fade">
      <div class="panel panel-default">
        <div class="panel-body text-center">
          <strong>Total earnings: <span class="badge badge-primary">Php <?php echo number_format($loadOrdersTotal, 2);?></span></strong><br>
          <strong>Total number of orders: <span class="badge badge-primary"><?php echo $loadOrdersCount;?></span></strong><br>
        </div>
      </div>

      <table class="table table-bordered table-orders">
        <thead class="bg-primary">
          <tr>
            <td>Order Number</td>
            <td>Customer</td>
            <td>Date ordered</td>
            <td>Action</td>
          </tr>
        </thead>
        <tbody>
          <?php foreach($loadOrdersTable as $row) {?>
            <tr id="order-transaction" order-admin-row-id="<?php echo $row->order_id;?>">
              <td><?php echo $row->order_id;?></td>
              <td><?php echo $row->username;?></td>
              <td><?php echo date('F j, Y', strtotime($row->order_date));?></td>
              <td class="text-center">
                <button class="btn btn-primary" id="btn-admin-view-order-items" order-admin-row-id-view="<?php echo $row->order_id;?>">View</button>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div id="view-admin-order-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title text-center">View your order</h4>
          </div>
          <div class="modal-body">
            <div class="view-admin-order-number">
              <strong>Order number:</strong>
              <span></span>
            </div>
            <div class="view-admin-order-username">
              <strong>Username:</strong>
              <span></span>
            </div>
            <div class="view-admin-order-date">
              <strong>Date ordered:</strong>
              <span></span>
            </div>
            <table class="table table-bordered view-admin-order-table">
              <thead class="bg-primary">
                <th>Line number</th>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
              </thead>
              <tbody></tbody>
              <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>Total</strong></td>
                    <td class="text-right" id="order-admin-transation-total-row"></td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>