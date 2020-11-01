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
        <?php foreach($products as $row) { ?>
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