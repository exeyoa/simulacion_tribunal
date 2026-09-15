<?php
// Endpoint interno del simulador: busca a una persona por cédula dentro de
// la misma tabla ciudadanos (sin sesión ni CSRF: proyecto de uso local).
// Uso: ajax/buscar_persona.php  (POST con cedula)

require __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Solo se acepta POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$cedula = trim($_POST['cedula'] ?? '');

if ($cedula === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Debe proporcionar una cédula']);
    exit;
}

// Búsqueda con sentencia preparada
$stmt = $conexion->prepare(
    'SELECT CONCAT_WS(\' \', primer_nombre, segundo_nombre) AS nombres,
            CONCAT_WS(\' \', primer_apellido, segundo_apellido) AS apellidos
     FROM ciudadanos WHERE cedula = :cedula'
);
$stmt->execute([':cedula' => $cedula]);
$persona = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$persona) {
    http_response_code(200);
    echo json_encode(['encontrado' => false]);
    exit;
}

http_response_code(200);
echo json_encode([
    'encontrado' => true,
    'nombres'    => $persona['nombres'],
    'apellidos'  => $persona['apellidos'],
]);