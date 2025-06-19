<?php
require_once '../config_sesion.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 2) {
    header("Location: login_cliente.php");
    exit();
}

if (isset($_SESSION['registro_error'])) {
    $mensaje = $_SESSION['registro_error'];
    unset($_SESSION['registro_error']);
} else {
    $mensaje = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8" />
    <title>Registro Cliente - Aurora Boutique</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: #eef2f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .registro-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        h3 {
            margin: 30px 0 10px;
            color: #34495e;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        label {
            font-weight: 600;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
            transition: border 0.3s;
        }

        input:focus,
        select:focus {
            border-color: #3498db;
            outline: none;
        }

        button,
        input[type="submit"] {
            margin-top: 20px;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-agregar {
            background-color: #2980b9;
            color: white;
        }

        .btn-agregar:hover {
            background-color: #2471a3;
        }

        input[type="submit"] {
            background-color: #27ae60;
            color: white;
        }

        input[type="submit"]:hover {
            background-color: #219150;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #e74c3c;
            background-color: #fdecea;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .field-group {
            margin-bottom: 10px;
        }
    </style>
    <script>
        function agregarTelefono() {
            const container = document.getElementById('telefonos');
            const index = container.children.length;
            const html = `
                <div class="form-grid">
                    <div class="field-group">
                        <label>Teléfono:</label>
                        <input type="text" name="telefonos[${index}][numero]" required>
                    </div>
                    <div class="field-group">
                        <label>Tipo:</label>
                        <select name="telefonos[${index}][tipo]">
                            <option value="personal">Personal</option>
                            <option value="trabajo">Trabajo</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        function agregarCorreo() {
            const container = document.getElementById('correos');
            const index = container.children.length;
            const html = `
                <div class="form-grid">
                    <div class="field-group">
                        <label>Correo:</label>
                        <input type="email" name="correos[${index}][correo]" required>
                    </div>
                    <div class="field-group">
                        <label>Tipo:</label>
                        <select name="correos[${index}][tipo]">
                            <option value="personal">Personal</option>
                            <option value="trabajo">Trabajo</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
</head>
<body>

<div class="registro-container">
    <h2>Registro Cliente</h2>

    <?php if (!empty($mensaje)): ?>
        <p class="error-message"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <form action="procesar_registro_cliente.php" method="post">
        <div class="form-grid">
            <div class="field-group">
                <label>Identificación:</label>
                <input type="text" name="identificacion" required>
            </div>

            <div class="field-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="field-group">
                <label>Apellidos:</label>
                <input type="text" name="apellido" required>
            </div>
        </div>

        <h3>Dirección:</h3>
        <div class="form-grid">
            <div class="field-group">
                <label>País:</label>
                <input type="text" name="pais" required>
            </div>
            <div class="field-group">
                <label>Provincia:</label>
                <input type="text" name="provincia" required>
            </div>
            <div class="field-group">
                <label>Cantón:</label>
                <input type="text" name="canton" required>
            </div>
            <div class="field-group">
                <label>Distrito:</label>
                <input type="text" name="distrito" required>
            </div>
            <div class="field-group">
                <label>Barrio:</label>
                <input type="text" name="barrio" required>
            </div>
        </div>

        <h3>Teléfonos:</h3>
        <div id="telefonos">
            <div class="form-grid">
                <div class="field-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefonos[0][numero]" required>
                </div>
                <div class="field-group">
                    <label>Tipo:</label>
                    <select name="telefonos[0][tipo]">
                        <option value="personal">Personal</option>
                        <option value="trabajo">Trabajo</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="button" class="btn-agregar" onclick="agregarTelefono()">Agregar Otro Teléfono</button>

        <h3>Correos Electrónicos:</h3>
        <div id="correos">
            <div class="form-grid">
                <div class="field-group">
                    <label>Correo:</label>
                    <input type="email" name="correos[0][correo]" required>
                </div>
                <div class="field-group">
                    <label>Tipo:</label>
                    <select name="correos[0][tipo]">
                        <option value="personal">Personal</option>
                        <option value="trabajo">Trabajo</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="button" class="btn-agregar" onclick="agregarCorreo()">Agregar Otro Correo</button>

        <h3>Credenciales:</h3>
        <div class="form-grid">
            <div class="field-group">
                <label>Usuario:</label>
                <input type="text" name="usuario" required>
            </div>

            <div class="field-group">
                <label>Contraseña:</label>
                <input type="password" name="password" required>
            </div>
        </div>

        <input type="submit" value="Registrar">
    </form>

    <p class="login-link">
        ¿Ya tienes cuenta? <a href="login_cliente.php">Inicia sesión aquí</a>
    </p>
</div>

</body>
</html>
