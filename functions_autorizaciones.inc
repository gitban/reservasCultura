<?php

require_once "mrbs_sql.inc";
require_once "ValidadorAutorizacion.php";

function table_autorizaciones()
{
  // Consulta simple: DataTables se encargará de filtrar y paginar en el cliente
  $sql = "SELECT * FROM `reservas_autorizaciones` ORDER BY id DESC";
  $res = sql_query($sql);
  ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  
  <div class="container-fluid mt-4">
    <h2 class="mb-4">Listado de Autorizaciones</h2>

    <div class="table-responsive shadow-sm p-3 mb-5 bg-white rounded">
      <table id="tablaAdmin" class="table table-striped table-hover table-bordered align-middle w-100">
        <thead class="table-dark text-center">
          <tr>
            <th>ID</th>
            <th>Inicio</th>
            <th>Tipo</th>
            <th>Legajo</th>
            <th>Apellido Nombre</th>
            <th>Entidad</th>
            <th>Evento</th>
            <th>Sala</th>
            <th>Vigilancia</th>
            <th>Limpieza</th>
            <th>Mayordomía</th>
            <th>Contacto</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i = 0; ($row = sql_row_keyed($res, $i)); $i++): ?>
            <tr>
              <td class="fw-bold text-center"><?php echo $row['id']; ?></td>
              <td class="small"><?php echo $row['inicio_reserva']; ?></td>
              <td><span class="badge bg-info text-dark"><?php echo $row['tipo_solicitante']; ?></span></td>
              <td><?php echo $row['legajo']; ?></td>
              <td><?php echo $row['apellido_nombre']; ?></td>
              <td><?php echo $row['entidad']; ?></td>
              <td><?php echo $row['nombre_evento']; ?></td>
              <td><?php echo $row['sala_nombre']; ?></td>
              <td class="text-center"><?php echo ($row['vigilancia'] ? 'SI (' . $row['horas_vigilancia'] . 'hs)' : 'NO'); ?>
              </td>
              <td class="text-center"><?php echo ($row['limpieza'] ? 'SI (' . $row['horas_limpieza'] . 'hs)' : 'NO'); ?>
              </td>
              <td class="text-center"><?php echo ($row['mayordomia'] ? 'SI (' . $row['horas_mayordomia'] . 'hs)' : 'NO'); ?>
              </td>
              <td><small><?php echo $row['email_celular']; ?></small></td>
              <td>
              <button
                id="openModal"
                type="button"
                class="btn btn-primary btn-sm btn-editar"
                data-bs-toggle="modal" 
                data-bs-target="#modalGestion"
                data-id="<?php echo $row['id']; ?>"
                data-nombre="<?php echo htmlspecialchars($row['apellido_nombre']); ?>"
                data-evento="<?php echo htmlspecialchars($row['nombre_evento']); ?>"
                data-estado="<?php echo $row['estado']; ?>"
              >
                Gestionar
              </button>
              </td>
            </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- 2. Bootstrap Bundle (ESTE ES EL QUE FALTABA COMPLETAR) -->
  <script src="https://cdn.jsdelivr.net/bootstrap.bundle.min.js"></script>
  
  <!-- 3. DataTables -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#tablaAdmin').DataTable({
        "language": {
          "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" // Traducción a español
        },
        "pageLength": 10,
        "responsive": true,
        "order": [[0, "desc"]], // Ordenar por ID (columna 0) descendente
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip' // Estructura de Bootstrap
      });

    // Solo llenamos los campos (ya no llamamos a 'new bootstrap.Modal')
    $(document).on("click", ".btn-editar", function (e) {
      e.preventDefault();
    let id = $(this).data("id");

      alert("presionado"+id)
        $("#modal-id-title").text($(this).data("id"));
        $("#input-id").val($(this).data("id"));
        $("#modal-nombre").text($(this).data("nombre"));
        $("#modal-evento").text($(this).data("evento"));
        $("#input-estado").val($(this).data("estado"));
    // FORZAR APERTURA: Intentamos de dos formas por si acaso
      //  try {
            const myModal = new bootstrap.Modal(document.getElementById('modalGestion'));
            myModal.show();
        // } catch (err) {
        //     console.error("Error al abrir modal:", err);
        //     // Si falla lo anterior, intentamos la vía jQuery (necesita bootstrap funcional)
        //     $("#modalGestion").modal('show');
        // }
    
      });

    // Envío AJAX
    $('#formActualizar').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'actualizar_estado.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error: " + response);
                }
            }
        });
    });
    
    });
    // Cargar datos en el modal al hacer clic en el botón

  </script>


<!-- Modal de Gestión de Autorizaciones -->
<dialog id="myModal">

    <div class="modal-dialog">
      <div class="modal-content">
      <!-- Encabezado con color oscuro para resaltar -->
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalGestionLabel">
          Gestionar Autorización #<span id="modal-id-title"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Formulario que procesará el cambio -->
      <form id="formActualizar">
        <div class="modal-body">
          <!-- Campo oculto para enviar el ID al PHP -->
          <input type="hidden" id="input-id" name="id">

          <div class="mb-3">
            <p class="mb-1 text-muted small">Solicitante:</p>
            <p id="modal-nombre" class="fw-bold"></p>
          </div>
          
          <div class="mb-3">
            <p class="mb-1 text-muted small">Evento:</p>
            <p id="modal-evento"></p>
          </div>

          <hr>

          <!-- Selector de Estado -->
          <div class="mb-3">
            <label for="input-estado" class="form-label fw-bold">Nuevo Estado:</label>
            <select class="form-select border-primary" id="input-estado" name="estado">
              <option value="RECIBIDO">RECIBIDO</option>
              <option value="APROBADO">APROBADO</option>
              <option value="RECHAZADO">RECHAZADO</option>
            </select>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</dialog>

<script>
   const modal = document.querySelector("#myModal");
    const btnOpen = document.querySelector("#openModal");
    const btnClose = document.querySelector("#closeModal");

    // Abrir modal
    btnOpen.addEventListener('click', function (e) {
      modal.showModal();
      e.preventDefault();
    let id = $(this).data("id");

      alert("presionado"+id)
        $("#modal-id-title").text($(this).data("id"));
        $("#input-id").val($(this).data("id"));
        $("#modal-nombre").text($(this).data("nombre"));
        $("#modal-evento").text($(this).data("evento"));
        $("#input-estado").val($(this).data("estado"));
    
    // Envío AJAX
    $('#formActualizar').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'actualizar_estado.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error: " + response);
                }
            }
        });
    });      
  
          })
    // Cerrar modal con el botón
    btnClose.addEventListener("click", () => modal.close());

    // Cerrar al hacer clic fuera (en el backdrop)
    modal.addEventListener("click", (e) => {
      if (e.target === modal) modal.close();
    });
</script>
<?php

} // Cierre de la función

/**
 * Funcion para revizar si las reserva necesita ser autorizada de acuerdo a la sala y el horario.
 *
*/
function necesita_autorizar($booking)
{

} // Cierre de la función


/**
 * Inserta registros calculando horas excedentes según el horario de cobertura
 */
function insertar_autorizacion_individual($booking)
{
 $organizador = ['U' => 'UNS', 'S' => 'Secretaría', 'O' => 'Otro'];
 global $tbl_room;
 
 // 2. Instanciar el verificador para obtener las horas excedentes
 // Asegúrate de que el archivo de la clase esté incluido
 $verificador = new VerificadorAutorizacion($booking);
 $datos = $verificador->determinarNecesidades();
 // Obtener el nombre de la sala de forma segura primero
 // 1. Obtenemos el ID de la sala
 $room_id = (int) $booking['room_id'];
 
 // 2. En lugar de sql_query1, usamos sql_query normal y validamos el resultado
 $res_sala = sql_query("SELECT room_name FROM $tbl_room WHERE id=$room_id LIMIT 1");
 
 $sala_nombre = "Sala ID: " . $room_id; // Valor por defecto
 
 if ($res_sala) {
   if (sql_count($res_sala) > 0) {
     $row_sala = sql_row($res_sala, 0);
     $sala_nombre = $row_sala[0];
     }
     // Liberamos el recurso para evitar otros warnings
     if (function_exists('mysql_free_result')) {
       @mysql_free_result($res_sala);
       }
       }
       //formateo la fecha para insertarla en la tabla con el formato correcto
  $fecha_formateada = date("Y-m-d H:i:s", $booking['start_time']);

  // Preparar el SQL con las horas calculadas
  $sql = "INSERT INTO reservas_autorizaciones (
                    inicio_reserva,
                    tipo_solicitante, 
                    legajo, 
                    apellido_nombre,
                    entidad, 
                    nombre_evento,
                    motivo_evento,
                    sala_nombre,
                    vigilancia,
                    horas_vigilancia, 
                    limpieza,
                    horas_limpieza, 
                    mayordomia,
                    horas_mayordomia, 
                    email_celular
                ) VALUES (
                    '$fecha_formateada',
                    1,
                    12345,
                    '" . sql_escape($booking['create_by']) . "',
                    '" . sql_escape($organizador[$booking['organiza']]) . "',
                    '" . sql_escape($booking['name']) . "',
                    '" . sql_escape($booking['description']) . "',
                    '" . sql_escape($sala_nombre) . "',
                    " . (int) $datos['vigilancia'] . ",
                    " . number_format((float) $datos['h_vigilancia'], 2, '.', '') . ",
                    " . (int) $datos['mayordomia'] . ",
                    " . number_format((float) $datos['h_mayordomia'], 2, '.', '') . ",
                    " . (int) $datos['limpieza'] . ",
                    " . number_format((float) $datos['h_limpieza'], 2, '.', '') . ",
                    '" . sql_escape(isset($booking['email_celular']) ? $booking['email_celular'] : 'sin') . "'
                    )";
                    
                    sql_command($sql);
                    
                    $res = sql_query($sql);
                    if ($res === -1) {
                      // Si la función devuelve -1, hubo un error en la base de datos
    echo "<h3>Error en la inserción de autorizaciones:</h3>";
    echo "<strong>SQL ejecutado:</strong> " . htmlspecialchars($sql) . "<br>";
    echo "<strong>Error de la DB:</strong> " . sql_error() . "<br>";
    //exit; // Detenemos la ejecución para poder leer el error

  }
}
?>