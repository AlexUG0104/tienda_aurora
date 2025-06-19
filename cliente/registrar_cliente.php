<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Cliente - Aurora Boutique</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            width: 100%;
            max-width: 1200px;
        }

        h2, h3 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        h3 {
            margin-top: 30px;
            color: #34495e;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .form-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        input:focus, select:focus {
            border-color: #3498db;
            outline: none;
        }

        button, input[type="submit"] {
            padding: 8px 14px;
            font-size: 13px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-agregar {
            background-color: #2980b9;
            color: white;
            width: fit-content;
        }

        .btn-agregar:hover {
            background-color: #2471a3;
        }

        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            align-self: center;
            width: 50%;
        }

        input[type="submit"]:hover {
            background-color: #219150;
        }

        .error-message {
            color: #e74c3c;
            background-color: #fdecea;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .password-icon {
            position: absolute;
            top: 35px;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            user-select: none;
        }
    </style>
</head>
<body>
<div class="registro-container">
    <h2>Registro Cliente</h2>
    <?php if (!empty($mensaje)): ?>
        <p class="error-message"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <form action="procesar_registro_cliente.php" method="post">
        <div class="form-section">
            <div class="field-group">
                <label>Identificación:</label>
                <input type="text" name="identificacion" placeholder="Ej: 123456789" required>
            </div>
            <div class="field-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" placeholder="Ej: Juan" required>
            </div>
            <div class="field-group">
                <label>Apellidos:</label>
                <input type="text" name="apellido" placeholder="Ej: Pérez González" required>
            </div>
        </div>

        <h3>Dirección:</h3>
        <div class="form-section">
            <div class="field-group">
                <label>País:</label>
                <select name="pais" id="pais" onchange="cambiarPais()" required>
                    <option value="">Seleccione un país</option>
                    <option value="Costa Rica">Costa Rica</option>
                    <option value="México">México</option>
                    <option value="Colombia">Colombia</option>
                    <option value="Argentina">Argentina</option>
                    <option value="España">España</option>
                    <option value="Estados Unidos">Estados Unidos</option>
                    <option value="Panamá">Panamá</option>
                    <option value="Otro">Otro</option>
                </select>
                <input type="text" name="pais_otro" id="pais_otro" placeholder="Escriba el país" style="display:none;">
            </div>

            <div class="field-group">
                <label>Provincia:</label>
                <select name="provincia" id="provincia_select" required>
                    <option value="">Seleccione una provincia</option>
                    <option value="San José">San José</option>
                    <option value="Alajuela">Alajuela</option>
                    <option value="Cartago">Cartago</option>
                    <option value="Heredia">Heredia</option>
                    <option value="Guanacaste">Guanacaste</option>
                    <option value="Puntarenas">Puntarenas</option>
                    <option value="Limón">Limón</option>
                </select>
                <input type="text" name="provincia" id="provincia_text" placeholder="Escriba la provincia" style="display:none;">
            </div>

            <div class="field-group">
                <label>Cantón:</label>
                <input type="text" name="canton" placeholder="Ej: Central" required>
            </div>
            <div class="field-group">
                <label>Distrito:</label>
                <input type="text" name="distrito" placeholder="Ej: Catedral" required>
            </div>
            <div class="field-group">
                <label>Barrio:</label>
                <input type="text" name="barrio" placeholder="Ej: Barrio Amón" required>
            </div>
        </div>

        <h3>Teléfonos:</h3>
        <div id="telefonos" class="form-section">
            <div class="field-group">
                <label>Teléfono:</label>
                <input type="text" name="telefonos[0][numero]" placeholder="Ej: 88888888" pattern="\d+" inputmode="numeric" required>
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
        <button type="button" class="btn-agregar" onclick="agregarTelefono()">➕ Agregar otro Teléfono</button>

        <h3>Correos Electrónicos:</h3>
        <div id="correos" class="form-section">
            <div class="field-group">
                <label>Correo:</label>
                <input type="email" name="correos[0][correo]" placeholder="Ej: usuario@email.com" required>
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
        <button type="button" class="btn-agregar" onclick="agregarCorreo()">➕ Agregar otro Correo</button>

        <h3>Credenciales:</h3>
        <div class="form-section">
            <div class="field-group">
                <label>Usuario:</label>
                <input type="text" name="usuario" placeholder="Ej: juan123" required>
            </div>
            <div class="field-group" style="position: relative;">
                <label>Contraseña:</label>
                <input type="password" name="password" id="password" placeholder="Cree una contraseña" required>
                <span class="password-icon" onclick="togglePassword()">👁</span>
            </div>
        </div>

        <input type="submit" value="Registrar">
    </form>

    <p class="login-link">
        ¿Ya tienes cuenta? <a href="login_cliente.php">Inicia sesión aquí</a>
    </p>
</div>
<script>
function cambiarPais() {
    const pais = document.getElementById("pais").value;
    const pais_otro = document.getElementById("pais_otro");
    const provincia_select = document.getElementById("provincia_select");
    const provincia_text = document.getElementById("provincia_text");

    if (pais === "Costa Rica") {
        pais_otro.style.display = "none";
        pais_otro.value = "";
        provincia_select.style.display = "block";
        provincia_select.required = true;
        provincia_text.style.display = "none";
        provincia_text.required = false;
    } else {
        pais_otro.style.display = pais === "Otro" ? "block" : "none";
        provincia_select.style.display = "none";
        provincia_select.required = false;
        provincia_text.style.display = "block";
        provincia_text.required = true;
    }
}

function agregarTelefono() {
    const container = document.getElementById('telefonos');
    const index = container.querySelectorAll('.field-group').length / 2;
    const html = `
        <div class="field-group">
            <label>Teléfono:</label>
           <input type="text" name="telefonos[0][numero]" placeholder="Ej: 88888888" pattern="\d+" inputmode="numeric" required>
        </div>
        <div class="field-group">
            <label>Tipo:</label>
            <select name="telefonos[${index}][tipo]">
                <option value="personal">Personal</option>
                <option value="trabajo">Trabajo</option>
                <option value="otros">Otros</option>
            </select>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
}

function agregarCorreo() {
    const container = document.getElementById('correos');
    const index = container.querySelectorAll('.field-group').length / 2;
    const html = `
        <div class="field-group">
            <label>Correo:</label>
            <input type="email" name="correos[${index}][correo]" placeholder="Ej: usuario@email.com" required>
        </div>
        <div class="field-group">
            <label>Tipo:</label>
            <select name="correos[${index}][tipo]">
                <option value="personal">Personal</option>
                <option value="trabajo">Trabajo</option>
                <option value="otros">Otros</option>
            </select>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
}

function togglePassword() {
    const input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
}
</script>
</body>
</html>
