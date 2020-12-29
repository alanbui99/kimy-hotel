<?php include 'includes/header.php';?>
<?php
    require('./utils/today.utils.php');
    $data = getCheckinsData();

?>
<div class='page-heading display-4 mb-4'>
    <img src='./images/schedule.png' width='64px' height='64px' class='page-icon mr-2'>Today's Check-ins
</div>

<div class="card mb-4">
    <div class="card-header lead">
        <i class="fas fa-table mr-2"></i>All Check-ins Expected
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <?php foreach (array_keys($data[0]) as $fieldName) : ?>
                        <?php if (!in_array($fieldName, ["CustomerID", "BsClass"])): ?>
                        <th class="text-xs font-weight-bold text-uppercase" scope="col"><?php echo $fieldName ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <th class="action"><span class="d-none"></span></th>

                </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $occ) : ?>
                    <tr class="text-muted">
                        <?php foreach ($occ as $field=>$val) : ?>
                            <?php if ($field == "Status"):?>
                                <td><span class="badge badge-pill badge-<?php echo $occ['BsClass']; ?>"><?php echo $val ?></span></td>
                            <?php elseif (in_array($field, ["CustomerID", "BsClass"]) == false) : ?>
                                <td><?php $id=$occ['CustomerID']; echo in_array($field, ["First name", "Last name"]) ? "<a href='./customer.php?id=$id'>$val</a>" : $val ?></td>
                            <?php endif; ?>

                        <?php endforeach; ?>
                        
                        
                        <td>
                        <?php if ($occ['Status'] == 'confirmed'): ?>
                            <form method="POST" action="./search.checkin-out.php">
                                <input type="hidden" name="first-name" value="<?php echo $occ['First name']?>">
                                <input type="hidden" name="last-name" value="<?php echo $occ['Last name']?>">
                                <button type="submit" name="submit" class="btn btn-sm btn-outline-success">
                                    <i class="far fa-calendar-check mr-2"></i>Check in
                                </button>
                            </form>
                        <?php endif;?>

                        </td>
                    </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<?php include 'includes/footer.php';?>
