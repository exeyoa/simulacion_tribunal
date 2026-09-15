<?php
require_once __DIR__ . '/config/conexion.php';

$errores = [];
$exito = '';

$cedula = '';
$primer_nombre = '';
$segundo_nombre = '';
$primer_apellido = '';
$segundo_apellido = '';
$fecha_nacimiento = '';
$sexo = 'M';
$lugar_nacimiento = '';
$donacion_organos = '';
$nombre_padre = '';
$nombre_madre = '';
$fecha_expedicion = '';
$fecha_expiracion = '';
$foto = 'fotos_cedulas/placeholder.jpg';

// -----------------------------------------------------------
// Procesamiento del alta de un ciudadano
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cedula = trim($_POST['cedula'] ?? '');
    $primer_nombre = trim($_POST['primer_nombre'] ?? '');
    $segundo_nombre = trim($_POST['segundo_nombre'] ?? '');
    $primer_apellido = trim($_POST['primer_apellido'] ?? '');
    $segundo_apellido = trim($_POST['segundo_apellido'] ?? '');
    $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $sexo = $_POST['sexo'] ?? '';
    $lugar_nacimiento = trim($_POST['lugar_nacimiento'] ?? '');
    $donacion_organos = $_POST['donacion_organos'] ?? '';
    $nombre_padre = trim($_POST['nombre_padre'] ?? '');
    $nombre_madre = trim($_POST['nombre_madre'] ?? '');
    $fecha_expedicion = trim($_POST['fecha_expedicion'] ?? '');
    $fecha_expiracion = trim($_POST['fecha_expiracion'] ?? '');
    $foto = trim($_POST['foto'] ?? '');

    // Campos obligatorios
    if ($cedula === '') {
        $errores[] = 'La cédula es obligatoria.';
    } elseif (!preg_match('/^(?:[A-Z]{1,2}-)?\d{1,4}-\d{1,4}(?:-\d{1,4})?$/', $cedula)) {
        $errores[] = 'La cédula no tiene un formato válido (ejemplo: 8-123-456 o PE-1-2345-6789).';
    }
    if ($primer_nombre === '') {
        $errores[] = 'El primer nombre es obligatorio.';
    }
    if ($primer_apellido === '') {
        $errores[] = 'El primer apellido es obligatorio.';
    }
    if ($lugar_nacimiento === '') {
        $errores[] = 'El lugar de nacimiento es obligatorio.';
    }
    if ($sexo !== 'M' && $sexo !== 'F') {
        $errores[] = 'El sexo debe ser M o F.';
    }
    if ($donacion_organos !== '1' && $donacion_organos !== '0') {
        $errores[] = 'Indica si el ciudadano dona órganos (sí/no).';
    }
    if ($foto === '') {
        $foto = 'fotos_cedulas/placeholder.jpg';
    }

    // Validación de fechas
    foreach (['fecha_nacimiento' => 'nacimiento', 'fecha_expedicion' => 'expedición', 'fecha_expiracion' => 'expiración'] as $campo => $etiqueta) {
        $valor = trim($_POST[$campo] ?? '');
        if ($$campo === '') {
            $errores[] = "La fecha de {$etiqueta} es obligatoria.";
        } else {
            $fecha = DateTime::createFromFormat('Y-m-d', $valor);
            $erroresFecha = DateTime::getLastErrors();
            if (!$fecha || ($erroresFecha !== false && ($erroresFecha['warning_count'] + $erroresFecha['error_count'] > 0))) {
                $errores[] = "La fecha de {$etiqueta} no es válida.";
            }
        }
    }

    // Cédula duplicada (es clave primaria)
    if (!$errores) {
        $stmt = $conexion->prepare('SELECT COUNT(*) FROM ciudadanos WHERE cedula = :cedula');
        $stmt->execute([':cedula' => $cedula]);
        if ($stmt->fetchColumn() > 0) {
            $errores[] = 'La cédula ya está registrada.';
        }
    }

    // Inserción con sentencia preparada
    if (!$errores) {
        try {
            $stmt = $conexion->prepare(
                'INSERT INTO ciudadanos
                    (cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                     fecha_nacimiento, sexo, lugar_nacimiento, donacion_organos, nombre_padre,
                     nombre_madre, fecha_expedicion, fecha_expiracion, foto)
                 VALUES
                    (:cedula, :primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido,
                     :fecha_nacimiento, :sexo, :lugar_nacimiento, :donacion_organos, :nombre_padre,
                     :nombre_madre, :fecha_expedicion, :fecha_expiracion, :foto)'
            );
            $stmt->execute([
                ':cedula'           => $cedula,
                ':primer_nombre'    => $primer_nombre,
                ':segundo_nombre'   => $segundo_nombre !== '' ? $segundo_nombre : null,
                ':primer_apellido'  => $primer_apellido,
                ':segundo_apellido' => $segundo_apellido !== '' ? $segundo_apellido : null,
                ':fecha_nacimiento' => $fecha_nacimiento,
                ':sexo'             => $sexo,
                ':lugar_nacimiento' => $lugar_nacimiento,
                ':donacion_organos' => $donacion_organos === '1' ? 1 : 0,
                ':nombre_padre'     => $nombre_padre !== '' ? $nombre_padre : null,
                ':nombre_madre'     => $nombre_madre !== '' ? $nombre_madre : null,
                ':fecha_expedicion' => $fecha_expedicion,
                ':fecha_expiracion' => $fecha_expiracion,
                ':foto'             => $foto,
            ]);

            $exito = 'Ciudadano registrado con éxito.';

            $cedula = '';
            $primer_nombre = '';
            $segundo_nombre = '';
            $primer_apellido = '';
            $segundo_apellido = '';
            $fecha_nacimiento = '';
            $sexo = 'M';
            $lugar_nacimiento = '';
            $donacion_organos = '';
            $nombre_padre = '';
            $nombre_madre = '';
            $fecha_expedicion = '';
            $fecha_expiracion = '';
            $foto = 'fotos_cedulas/placeholder.jpg';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errores[] = 'La cédula ya está registrada.';
            } else {
                error_log('Error al insertar ciudadano: ' . $e->getMessage());
                $errores[] = 'No se pudo registrar el ciudadano. Intenta nuevamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tribunal Electoral - Agregar ciudadano</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="barra-superior">
        <span class="marca">Tribunal Electoral de Panamá</span>
        <a href="index.php" class="btn">Volver al listado</a>
    </div>

    <div class="contenido-panel">
        <h2>Agregar ciudadano</h2>

        <?php if ($exito !== ''): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?></div>
        <?php elseif (count($errores) > 0): ?>
            <div class="mensaje-error">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="agregar_ciudadano.php">
            <div class="campo">
                <label for="cedula">Cédula</label>
                <?php
                $sel_cedula_id = 'cedula';
                $sel_cedula_nombre = 'cedula';
                $sel_cedula_valor = $cedula;
                include __DIR__ . '/componentes/selector_cedula.php';
                ?>
            </div>

            <div class="campo">
                <label for="primer_nombre">Primer nombre</label>
                <input type="text" id="primer_nombre" name="primer_nombre" value="<?= htmlspecialchars($primer_nombre, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="campo">
                <label for="segundo_nombre">Segundo nombre (opcional)</label>
                <input type="text" id="segundo_nombre" name="segundo_nombre" value="<?= htmlspecialchars($segundo_nombre, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="campo">
                <label for="primer_apellido">Primer apellido</label>
                <input type="text" id="primer_apellido" name="primer_apellido" value="<?= htmlspecialchars($primer_apellido, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="campo">
                <label for="segundo_apellido">Segundo apellido (opcional)</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido" value="<?= htmlspecialchars($segundo_apellido, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="campo">
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($fecha_nacimiento, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="campo">
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo">
                    <option value="M" <?= $sexo === 'M' ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= $sexo === 'F' ? 'selected' : '' ?>>Femenino</option>
                </select>
            </div>

            <div class="campo">
                <label for="lugar_nacimiento">Lugar de nacimiento</label>
                <input type="text" id="lugar_nacimiento" name="lugar_nacimiento" value="<?= htmlspecialchars($lugar_nacimiento, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="campo-checkbox">
                <input type="hidden" name="donacion_organos" value="0">
                <input type="checkbox" id="donacion_organos" name="donacion_organos" value="1" <?= $donacion_organos === '1' ? 'checked' : '' ?>>
                <label for="donacion_organos">Donante de órganos</label>
            </div>

            <div class="campo">
                <label for="nombre_padre">Nombre del padre</label>
                <input type="text" id="nombre_padre" name="nombre_padre" value="<?= htmlspecialchars($nombre_padre, ENT_QUOTES, 'UTF-8') ?>">
                <?php
                $sel_cedula_id = 'cedula_padre';
                $sel_cedula_nombre = 'cedula_padre';
                $sel_cedula_valor = '';
                include __DIR__ . '/componentes/selector_cedula.php';
                ?>
                <button type="button" class="btn" id="btn-buscar-padre">Buscar</button>
                <div id="estado-padre" aria-live="polite"></div>
            </div>

            <div class="campo">
                <label for="nombre_madre">Nombre de la madre</label>
                <input type="text" id="nombre_madre" name="nombre_madre" value="<?= htmlspecialchars($nombre_madre, ENT_QUOTES, 'UTF-8') ?>">
                <?php
                $sel_cedula_id = 'cedula_madre';
                $sel_cedula_nombre = 'cedula_madre';
                $sel_cedula_valor = '';
                include __DIR__ . '/componentes/selector_cedula.php';
                ?>
                <button type="button" class="btn" id="btn-buscar-madre">Buscar</button>
                <div id="estado-madre" aria-live="polite"></div>
            </div>

            <div class="campo">
                <label for="fecha_expedicion">Fecha de expedición</label>
                <input type="date" id="fecha_expedicion" name="fecha_expedicion" value="<?= htmlspecialchars($fecha_expedicion, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="campo">
                <label for="fecha_expiracion">Fecha de expiración</label>
                <input type="date" id="fecha_expiracion" name="fecha_expiracion" value="<?= htmlspecialchars($fecha_expiracion, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="campo">
                <label for="foto">Foto (ruta relativa)</label>
                <input type="text" id="foto" name="foto" value="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <button type="submit" class="btn">Registrar ciudadano</button>
        </form>
    </div>

    <script>
    // Autocompletado del nombre de padre/madre buscándolos por cédula
    // dentro de la propia tabla ciudadanos.
    function buscarProgenitor(inputCedula, inputNombre, divEstado, boton) {
        var cedula = inputCedula.value.trim();

        if (cedula === '') {
            divEstado.textContent = 'Escribe una cédula para buscar.';
            divEstado.className = 'mensaje-info';
            return;
        }

        divEstado.textContent = 'Buscando cédula…';
        divEstado.className = 'mensaje-info';
        boton.disabled = true;

        var datos = new URLSearchParams();
        datos.append('cedula', cedula);

        fetch('ajax/buscar_persona.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: datos
        })
        .then(function (resp) {
            return resp.json().then(function (datosResp) {
                return { estado: resp.status, datos: datosResp };
            });
        })
        .then(function (resultado) {
            if (resultado.estado === 200 && resultado.datos.encontrado) {
                inputNombre.value = resultado.datos.nombres + ' ' + resultado.datos.apellidos;
                divEstado.textContent = 'Datos encontrados en el padrón electoral';
                divEstado.className = 'mensaje-exito';
            } else if (resultado.estado === 200 && !resultado.datos.encontrado) {
                inputNombre.value = '';
                divEstado.textContent = 'No se encontró a esa persona en el padrón; escribe el nombre manualmente.';
                divEstado.className = 'mensaje-info';
            } else {
                console.error('Error al buscar la persona:', resultado.estado, resultado.datos);
                inputNombre.value = '';
                divEstado.textContent = 'No se pudo verificar la cédula; escribe el nombre manualmente.';
                divEstado.className = 'mensaje-error';
            }
        })
        .catch(function (error) {
            console.error('No se pudo contactar la búsqueda:', error);
            inputNombre.value = '';
            divEstado.textContent = 'No se pudo verificar la cédula; escribe el nombre manualmente.';
            divEstado.className = 'mensaje-error';
        })
        .finally(function () {
            boton.disabled = false;
        });
    }

    document.getElementById('btn-buscar-padre').addEventListener('click', function () {
        buscarProgenitor(
            document.getElementById('cedula_padre'),
            document.getElementById('nombre_padre'),
            document.getElementById('estado-padre'),
            this
        );
    });

    document.getElementById('btn-buscar-madre').addEventListener('click', function () {
        buscarProgenitor(
            document.getElementById('cedula_madre'),
            document.getElementById('nombre_madre'),
            document.getElementById('estado-madre'),
            this
        );
    });
    </script>
</body>
</html>