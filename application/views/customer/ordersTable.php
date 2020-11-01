<table class="table table-bordered table-orders">
    <thead class="bg-primary">
    <tr>
        <td>Order Number</td>
        <td>Date ordered</td>
        <td>Action</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach($orders as $row) {?>
        <tr>
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