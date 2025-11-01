<?php
$conexion = new mysqli('localhost', 'root', '', 'empresa');
if ($conexion->connect_error) die("Error de conexión");

if (isset($_POST['token'], $_POST['contrasena'])) {
    $token = $_POST['token'];
    $nueva = $_POST['contrasena'];

    $stmt = $conexion->prepare("SELECT id, token_expira FROM usuarios WHERE token_recuperacion=?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (strtotime($row['token_expira']) > time()) {
            $update = $conexion->prepare("UPDATE usuarios SET contrasena=SHA2(?,256), token_recuperacion=NULL, token_expira=NULL WHERE id=?");
            $update->bind_param('si', $nueva, $row['id']);
            $update->execute();
            echo "Contraseña actualizada correctamente.";
        } else {
            echo "El enlace ha expirado.";
        }
    } else {
        echo "Token inválido.";
    }
}
$conexion->close();
?>
