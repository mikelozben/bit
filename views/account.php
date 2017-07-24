<?php
    if (!isset($username)) {
        $username = '';
    }
    
    if (!isset($currentBalance)) {
        $currentBalance = 0.0;
    }
    
    if (!isset($errorMsg)) {
        $errorMsg = null;
    }
    
    require __DIR__ . '/parts/header.php';
?>
    <div class='content account-page'>
        <div class='panel panel-info text-center'>
            <div class="panel-body">
                <div class='row'>
                    <div class='col-lg-6 text-right'>
                        <label>Username:</label>
                    </div>
                    <div class='col-lg-6 text-left'>
                        <label><?= $username ?></label>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-lg-6 text-right'>
                        <label>Balance:</label>
                    </div>
                    <div class='col-lg-6 text-left'>
                        <label><?= number_format(($currentBalance/100), 2, ',', "'") ?></label>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-lg-6 text-right'></div>
                    <div class='col-lg-6 text-left'>
                        <input type="button" class='btn btn-sm btn-danger' value='Logout' onclick='location.href = "<?= $urlPrefix ?>index.php?action=logout"' />
                    </div>
                </div>
                <?php if (null !== $errorMsg) { ?>
                <div class='row'>
                    <div class='col-lg-12 text-center'>
                        <label class='error-msg'><?= $errorMsg ?></label>
                    </div>
                </div>
                <?php } ?>
                <form class='row' action='<?= $urlPrefix ?>index.php?action=account' method='POST'>
                    <div class='col-lg-6 text-right'>
                        <input type='text' name='amount' class='input-sm' />
                    </div>
                    <div class='col-lg-6 text-left'>
                        <input type="submit" class='btn btn-sm btn-success' value='Process transaction' />
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php 
    require __DIR__ . '/parts/footer.php';
