<?php
    if (!isset($isSigned)) {
        $isSigned = false;
    }
    
    if (!isset($urlPrefix)) {
        $urlPrefix = '';
    }
    
    if (!isset($errorMsg)) {
        $errorMsg = null;
    }
    
    require __DIR__ . '/parts/header.php';
?>
    <div class='content signin-page'>
        <div class='panel panel-info text-center'>
            <?php if (!$isSigned) { ?>
            <form class="panel-body" action='<?= $urlPrefix ?>index.php?action=login' method="POST">
                <div class='row'>
                    <div class='col-lg-6 text-right'>
                        <label>Login</label>
                    </div>
                    <div class='col-lg-6 text-left'>
                        <input type='text' name='username' class='input-sm' />
                    </div>
                </div>
                <div class='row'>
                    <div class='col-lg-6 text-right'>
                        <label>Password</label>
                    </div>
                    <div class='col-lg-6 text-left'>
                        <input type='password' name='password' class='input-sm' />
                    </div>
                </div>
                <div class='row'>
                    <div class='col-lg-6 text-right'>
                        <input type="submit" class='btn btn-sm btn-success' value='Login' />
                    </div>
                </div>
                <?php if (null !== $errorMsg) { ?>
                <div class='row'>
                    <div class='col-lg-12 text-center'>
                        <label class='error-msg'><?= $errorMsg ?></label>
                    </div>
                </div>
                <?php } ?>
            </form>
            <?php } else { ?>
            <div class="panel-body">
                <div class='row'>
                    <div class='col-lg-12 text-center'>
                        <label>You are already authorized</label>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
<?php 
    require __DIR__ . '/parts/footer.php';
