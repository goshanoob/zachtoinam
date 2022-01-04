<?php if (!empty($_SESSION['success'])) : ?>
    <div class="success">
        <p>
            <?php
            echo $_SESSION['success'];
            $_SESSION['success'] = '';
            ?>
        </p>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])) : ?>
    <div class="error">
        <p>
            <?php
            echo $_SESSION['error'];
            $_SESSION['error'] = '';
            ?>
        </p>
    </div>
<?php endif; ?>