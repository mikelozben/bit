<?php
    if ( !isset($exception) ) {
        $exception = new Exception('Server error');
    }
    
    require __DIR__ . '/parts/header.php';
?>
    <div class='content error-page'>
        <div class='panel panel-danger text-center'>
            <div class="panel-body">
                Error: <?= $exception->getMessage() ?>
            </div>            
        </div>
    </div>
<?php 
    require __DIR__ . '/parts/footer.php';
