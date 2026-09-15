<?php
require_once __DIR__ . '/config/conexion.php';

$stmt = $conexion->query(
    'SELECT cedula,
            CONCAT_WS(\' \', primer_nombre, segundo_nombre) AS nombres,
            CONCAT_WS(\' \', primer_apellido, segundo_apellido) AS apellidos,
            fecha_nacimiento, sexo
     FROM ciudadanos
     ORDER BY primer_apellido, primer_nombre'
);
$ciudadanos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tribunal Electoral - Ciudadanos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="barra-superior">
        <span class="marca">Tribunal Electoral de Panamá</span>
        <a href="agregar_ciudadano.php" class="btn">Agregar ciudadano</a>
    </div>

    <div class="contenido-panel">
        <h2>Ciudadanos registrados</h2>
        <p>Total de registros: <?= count($ciudadanos) ?></p>

        <table class="tabla">
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Fecha de nacimiento</th>
                    <th>Sexo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ciudadanos as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['cedula'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['nombres'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['apellidos'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['fecha_nacimiento'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['sexo'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>