<?php
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $confirmar = trim($_POST['confirmar']);

    if (empty($nombre) || empty($correo) || empty($password) || empty($confirmar)) {
        echo "<script>alert('Por favor, completa todos los campos.'); window.history.back();</script>";
        exit();
    }

    if ($password !== $confirmar) {
        echo "<script>alert('Las contraseñas no coinciden.'); window.history.back();</script>";
        exit();
    }

    $verificar = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $verificar->bind_param("s", $correo);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        echo "<script>alert('Este correo ya está registrado.'); window.history.back();</script>";
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $correo, $passwordHash);

    if ($stmt->execute()) {
        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error al registrar el usuario.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
