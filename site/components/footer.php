<?php if (!isset($profile)) {
    // если профиля нет, то проверку не прошли
    header('Location: http://localhost/pages/login.php');
} ?>

    </section>    
      
        
<?php

for ($i = 0; $i < count($scriptsSrc); $i++) {
   echo $scriptsSrc[$i] . '<br>';
}; 

?> 
<script src='http://localhost/assets/js/authorization.js'></script>
</body>
</html>


