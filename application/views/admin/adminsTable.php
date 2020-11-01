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
        <?php foreach($admins as $row) {?>
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
