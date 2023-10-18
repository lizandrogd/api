<?php
header("Content-Type: application/json");

// Configura la conexión a la base de datos
$servername = "";
$username = "";
$password = "";
$database = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Ruta para eliminar un usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "delete") {
    if (isset($_POST["id"])) {
        $id = $_POST["id"];

        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["mensaje" => "Usuario eliminado exitosamente"]);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(["mensaje" => "ID de usuario no proporcionado"]);
    }
}

// Ruta para generar nombres aleatorios y guardarlos en la base de datos
if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($_POST["action"])) {

    // Generar nombres aleatorios
    $nombres = ["Juan", "María", "Carlos", "Ana", "Pedro", "Laura", "Diego", "Sofía", "Luis", "Valentina"];
    $apellidos = ["Pérez", "Gómez", "Rodríguez", "Fernández", "López", "Martínez", "García", "Díaz", "González", "Torres"];

    $nombreAleatorio = $nombres[array_rand($nombres)];
    $apellidoAleatorio = $apellidos[array_rand($apellidos)];
    $edadAleatoria = rand(18, 60);

    // Guardar en la base de datos
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, edad) VALUES (?, ?, ?)");
    $stmt->execute([$nombreAleatorio, $apellidoAleatorio, $edadAleatoria]);
    echo json_encode(["mensaje" => "Usuario agregado exitosamente"]);
}

// Ruta para obtener la lista de usuarios
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $conn->query("SELECT * FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
}

// Ruta para actualizar un usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" & isset($_POST["action"]) && $_POST["action"] === "update") {
    parse_str(file_get_contents("php://input"), $datos);

    if (isset($datos["id"]) && isset($datos["nombre"]) && isset($datos["apellido"]) && isset($datos["edad"])) {
        $id = $datos["id"];
        $nombre = $datos["nombre"];
        $apellido = $datos["apellido"];
        $edad = $datos["edad"];

        try {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, edad = ? WHERE id = ?");
            $stmt->execute([$nombre, $apellido, $edad, $id]);
            echo json_encode(["mensaje" => "Usuario actualizado exitosamente"]);
        } catch (PDOException $e) {
            echo json_encode(["mensaje" => "Error al actualizar el usuario: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["mensaje" => "Parámetros de actualización incompletos"]);
    }
}