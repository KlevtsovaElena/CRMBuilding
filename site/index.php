<?php require('handler/check-profile.php'); ?>

<script src="./../assets/js/main.js"></script>

<?php 

// домашняя страница для админа
if ($role == "1") { ?>
    <script type="text/javascript">
         window.location.href = mainUrl + '/pages/admin-main.php';
    </script>
<?php } ?>


<?php

// домашняя страница для поставщика
if ($role == "2") { ?>
   <script type="text/javascript">
        window.location.href = mainUrl + '/pages/vendor-list-orders.php';
   </script>
<?php } ?>