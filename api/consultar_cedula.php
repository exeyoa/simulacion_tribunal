<?php
// Endpoint de consulta de cédula del Tribunal Electoral (simulación académica).
// Uso: api/consultar_cedula.php?cedula=8-123-456&api_key=XXXXX

require __DIR__ . '/../config/conexion.php';
require __DIR__ . '/../config/api.php';

// CORS: solo se permite consumir desde el origen configurado en config/api.php
header('Access-Control-Allow-Origin: ' . ORIGEN_PERMITIDO);

header('Content-Type: application/json; charset=utf-8');

// 1) Autenticación con api_key
if (!isset($_GET['api_key']) || $_GET['api_key'] !== API_KEY) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// 2) Validación de la cédula
if (!isset($_GET['cedula']) || trim($_GET['cedula']) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Debe proporcionar una cédula']);
    exit;
}

$cedula = trim($_GET['cedula']);

// 3) Consulta con sentencia preparada (evita inyección SQL).
//    nombres y apellidos se devuelven concatenados (primer + segundo),
//    manteniendo el contrato JSON que consume sistema_hospital.
$stmt = $conexion->prepare(
    'SELECT cedula,
            CONCAT_WS(\' \', primer_nombre, segundo_nombre) AS nombres,
            CONCAT_WS(\' \', primer_apellido, segundo_apellido) AS apellidos,
            fecha_nacimiento, sexo, lugar_nacimiento, donacion_organos,
            nombre_padre, nombre_madre, fecha_expedicion, fecha_expiracion, foto
     FROM ciudadanos WHERE cedula = :cedula'
);
$stmt->execute([':cedula' => $cedula]);
$ciudadano = $stmt->fetch(PDO::FETCH_ASSOC);

// 4) Respuestas
if (!$ciudadano) {
    http_response_code(404);
    echo json_encode(['error' => 'Cédula no encontrada']);
    exit;
}

// bool(false/true) limpio en el JSON para donacion_organos
$respuesta = $ciudadano;
$respuesta['donacion_organos'] = (bool)$ciudadano['donacion_organos'];

http_response_code(200);
echo json_encode($respuesta);
?>