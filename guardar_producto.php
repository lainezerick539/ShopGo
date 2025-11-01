<?php
header('Content-Type: application/json');

$host = "localhost";
$db = "empresa";
$user = "root";
$pass = "";
$respuesta = [];

// Habilita el reporte de errores de MySQLi para verlos claramente
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['nombre'], $_POST['descripcion'], $_POST['precio'], $_POST['ubicacion'], $_FILES['imagen'])) {
            if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $directorioUploads = "uploads/";
                if (!is_dir($directorioUploads)) {
                    mkdir($directorioUploads, 0777, true);
                }

                $nombreImagen = basename($_FILES['imagen']['name']);
                $extension = pathinfo($nombreImagen, PATHINFO_EXTENSION);
                $nombreUnico = uniqid('prod_', true) . '.' . $extension;
                $rutaDestino = $directorioUploads . $nombreUnico;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $nombre = $_POST['nombre'];
                    $descripcion = $_POST['descripcion'];
                    $precio = $_POST['precio'];
                    $ubicacion = $_POST['ubicacion'];

                    $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen, ubicacion) VALUES (?, ?, ?, ?, ?)";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssdss", $nombre, $descripcion, $precio, $rutaDestino, $ubicacion);
                    $stmt->execute();

                    $respuesta = ['success' => true, 'message' => "Producto guardado correctamente."];
                    $stmt->close();
                } else {
                    $respuesta = ['success' => false, 'error' => "No se pudo guardar la imagen en el servidor."];
                }
            } else {
                $respuesta = ['success' => false, 'error' => "Error al subir la imagen. Código: " . $_FILES['imagen']['error']];
            }
        } else {
            $respuesta = ['success' => false, 'error' => "Faltan datos obligatorios en el formulario."];
        }
    } else {
        $respuesta = ['success' => false, 'error' => "Método de solicitud no permitido."];
    }
} catch (mysqli_sql_exception $e) {
    $respuesta = ['success' => false, 'error' => "Error de Base de Datos: " . $e->getMessage()];
} catch (Exception $e) {
    $respuesta = ['success' => false, 'error' => "Error General: " . $e->getMessage()];
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    echo json_encode($respuesta);
}
?>

