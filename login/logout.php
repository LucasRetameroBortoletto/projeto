<?php

session_start();



if(isset($_SESSION['usuario'])) {
$_SESSION = array();
session_destroy();

echo "<script>
    alert('Logout realizado com sucesso');
    window.location.href = '/MINI SISTEMA/index.php';
</script>";

} else {
    echo "<script>  
    alert('Você não possui um login ativo !!');
    window.location.href = '/MINI SISTEMA/index.php';
    </script>" ;
}
exit;
?>