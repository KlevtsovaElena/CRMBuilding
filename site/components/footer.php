<?php if (!isset($profile)) {
    // если профиля нет, то проверку не прошли
    header('Location: ' . $mainUrl . '/pages/login.php');
} ?>

    </section>    
      
        
<?php

for ($i = 0; $i < count($scriptsSrc); $i++) {
   echo $scriptsSrc[$i] . '<br>';
}; 

?> 
<script src='<?= $mainUrl; ?>/assets/js/authorization.js'></script>

</body>
</html>


