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
      <a class="navbar-brand" href="<?php echo base_url();?>">Order System</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $session['firstName'] . ' ' . $session['lastName'] . ' ';?></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo base_url()?>customer/logout"><strong>Logout</strong></a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  <div class="panel panel-default">
    <div class="panel-body bg-primary">
      <h2>Hello <?php echo $session['firstName'] . ' ' . $session['lastName'];?>!</h2>
      <h4>Username: <span class="greeting-username"><?php echo $session['username'];?></span></h4>
      <h4>Customer since: <span class="greeting-username"><?php echo $session['createdDate'];?></span></h4>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-body text-center">
      <button class="btn btn-primary" id="table-order-open">New order</button>
    </div>
  </div>

  <!-- Modal -->
  <div id="table-products-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" id="table-order-close">&times;</button>
          <h4 class="modal-title text-center">New order</h4>
        </div>
        <div class="modal-body">
          <?php 
            $attribute = array(
              'id'        =>  'order-form',
              'class'     =>  'form-inline',
              'name'      =>  'order-form'
            );
            echo form_open('', $attribute);
          ?>
            <input type="hidden" id="order-item-line-number" value="1">
            <div class="form-group">
              <select class="form-control" id="product-list">
                <option value="0">Choose products</option>
                <?php foreach($loadProductsTable as $row) {?>
                  <option value="<?php echo $row->product_id;?>"><?php echo $row->product_name;?></option>
                <?php }?>
              </select>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" id="product-price" placeholder="Price" disabled>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" id="product-availability" placeholder="Available" disabled>
            </div>
            <div class="form-group">
              <input type="number" class="form-control" id="product-quantity" placeholder="Quantity" disabled>
            </div>
            <div class="form-group">
              <button type="button" class="btn btn-primary btn-add-order-item" disabled>Add Item</button>
            </div>
          <?php echo form_close();?>
        </div>
        <div class="modal-body">
          <table class="table table-bordered order-list-table">
            <thead class="bg-primary">
              <tr>
                <td>Line number</td>
                <td>Product</td>
                <td>Price</td>
                <td>Quantity</td>
                <td>Action</td>
                <td>Subtotal</td>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
              <tr>
                  <td colspan="5" class="text-right"><strong>Total</strong.</td>
                  <td class="text-right" id="order-total-row">0</td>
              </tr>
             </tfoot>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="btn-save-order-items" disabled>Save</button>
        </div>
      </div>
    </div>
  </div>

  <div>
    <table class="table table-bordered table-orders">
      <thead class="bg-primary">
        <tr>
          <td>Order Number</td>
          <td>Date ordered</td>
          <td>Action</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach($loadOrdersTable as $row) {?>
          <tr id="order-transaction" order-row-id="<?php echo $row->order_id;?>">
            <td><?php echo $row->order_id;?></td>
            <td><?php echo date('F j, Y', strtotime($row->order_date));?></td>
            <td class="text-center">
              <button class="btn btn-primary" id="btn-view-order-items" order-row-id-view="<?php echo $row->order_id;?>">View</button>
              <button class="btn btn-danger" id="btn-delete-order-items" order-row-id-delete="<?php echo $row->order_id;?>">Delete</button>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <!-- Modal -->
  <div id="view-order-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title text-center">View your order</h4>
        </div>
        <div class="modal-body">
          <div class="view-order-number">
            <strong>Order number:</strong>
            <span></span>
          </div>
          <div class="view-order-date">
            <strong>Date ordered:</strong>
            <span></span>
          </div>
          <table class="table table-bordered view-order-table">
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
                  <td class="text-right" id="order-transation-total-row"></td>
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