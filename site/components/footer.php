<!-- если профиля нет, то проверку не прошли -->
<?php if (!isset($profile)) {
    header('Location: ../pages/login.php');
} ?>

/div>
    </section>    
      
        
<?php

for ($i = 0; $i < count($scriptsSrc); $i++) {
   echo $scriptsSrc[$i] . '<br>';
}; 

?> 

</body>
</html>


