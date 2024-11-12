<?php

require_once __DIR__ . '/../config.php';
?>

<pre>
<?php var_dump($_ENV); ?>
</pre>

<p>
    <?php
    
        if(password_verify("admins", $_ENV['ADMIN_PASSWORD'])) {
            echo " admins == OK";
        } else{
            echo " admins == NON";
        }

        if(password_verify("admin", $_ENV['ADMIN_PASSWORD'])) {
            echo " admin == OK";
        } else{
            echo " admin == NON";
        }

    ?>
</p>