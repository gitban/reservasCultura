<?php

require_once "mrbs_sql.inc";
require_once "ValidadorAutorizacion.php";
require_once "GestorAutorizacion.php";
function table_autorizaciones()
{
  // Consulta simple: DataTables se encargará de filtrar y paginar en el cliente
  $sql = "SELECT *
FROM reservas_autorizaciones AS T1
INNER JOIN reservas_entry AS T2
ON T1.reserva_id = T2.id
INNER JOIN reservas_room AS T3
ON T2.room_id = T3.id;
";
  $estados = [0 => 'RECIBIDO', 1 => 'APROBADO', 2 => 'RECHAZADO'];

  $res = sql_query($sql);
  ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="css/modal.css">

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
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i = 0; ($row = sql_row_keyed($res, $i)); $i++):?>
            <tr>

              <td class="fw-bold text-center"><?php echo $row['id']; ?></td>
              <td class="small"><?php echo $row['inicio_reserva']; ?></td>
              <td><span class="badge bg-info text-dark"><?php echo $row['tipo_solicitante']; ?></span></td>
              <td><?php echo $row['legajo']; ?></td>
              <td><?php echo $row['apellido_nombre']; ?></td>
              <td><?php echo $row['contact_entity']; ?></td>
              <td><?php echo $row['name']; ?></td>
              <td><?php echo $row['room_name']; ?></td>
              <td class="text-center"><?php echo ($row['vigilancia'] ? 'SI (' . $row['horas_vigilancia'] . 'hs)' : 'NO'); ?>
              </td>
              <td class="text-center"><?php echo ($row['limpieza'] ? 'SI (' . $row['horas_limpieza'] . 'hs)' : 'NO'); ?>
              </td>
              <td class="text-center"><?php echo ($row['mayordomia'] ? 'SI (' . $row['horas_mayordomia'] . 'hs)' : 'NO'); ?>
              </td>
              <td><small><?php echo $row['email'] . "-" . $row['telefono']; ?></small></td>
              <td 
              <?php switch ($row['estado_autorizacion']){
              case 1:
               echo 'style="background-color:#b3ffb3"';
               break;
              case 2:
               echo 'style="background-color:#ffa6a6"';
               break;
                default:
                break;
               }
              ?>
              >
              <small><?php echo $estados[$row['estado_autorizacion']]; ?></small></td>
              <td>
                <button id="openModal" type="button" class="btn btn-primary btn-sm btn-editar" data-bs-toggle="modal"
                  data-bs-target="#modalGestion" data-id="<?php echo $row['id']; ?>"
                  data-reserva="<?php echo htmlspecialchars($row['reserva_id']); ?>"
                  data-nombre="<?php echo htmlspecialchars($row['apellido_nombre']); ?>"
                  data-evento="<?php echo htmlspecialchars($row['nombre_evento']); ?>"
                  data-sala="<?php echo htmlspecialchars($row['sala_nombre']); ?>"
                  data-estado="<?php echo $row['estado']; ?>">
                  Modificar Estado
                </button>
              </td>
            </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php
  // Obtenemos el estado actual de la base de datos
  $estado_actual = (int) $row['estado_autorizacion'];
  $bloqueado = ($estado_actual !== 0); // Si no es 'Recibido', se bloquea
  ?>
  <!-- Modal de Gestión de Autorizaciones -->
  <dialog id="myModal" class="modalAutorizar">

    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Encabezado con color oscuro para resaltar -->
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title" id="modalGestionLabel">
            Gestionar Autorización N°: <span id="modal-id-title"></span>
          </h5>
          <button type="button" class="btn-close btn-close-white btn-cerrar" data-bs-dismiss="modal"
            aria-label="Close"></button>

        </div>
        <!-- Formulario que procesará el cambio -->

        <form class="form-autorizacion">
          <div class="modal-body">
            <!-- Campo oculto para enviar el ID al PHP -->
            <input type="hidden" id="reserva_id_modal" />
            <div class="mb-3">
              <p class="mt-2 mb-1 text-muted small">Solicitante:</p>
              <p id="modal-nombre" class="fw-bold"></p>
            </div>

            <div class="mb-3">
              <p class="mb-1 text-muted small">Evento:</p>
              <p id="modal-evento"></p>
            </div>

            <div class="mb-3">
              <p class="mb-1 text-muted small">Sala:</p>
              <p id="modal-sala"></p>
            </div>

            <hr>

            <!-- Selector de Estado -->
            <div class="mb-3">
              <label for="input-estado" class="form-label fw-bold">Nuevo Estado:</label>
              <select class="form-select border-primary" name="nuevo_estado" <?php echo ($bloqueado) ? 'disabled' : ''; ?>
                id="nuevo_estado_modal">
                <option value="0" <?php echo ($estado_actual == 0) ? 'selected' : ''; ?>> 📩 Recibido</option>
                <option value="1" <?php echo ($estado_actual == 1) ? 'selected' : ''; ?>> ✅ Autorizado</option>
                <option value="2" <?php echo ($estado_actual == 2) ? 'selected' : ''; ?>> ❌ Rechazado</option>
              </select>
            </div>
          </div>

          <div class="modal-footer bg-light">
            <?php if (!$bloqueado): ?>
              <button type="button" class="btn btn-success px-4" onclick="enviarCambioEstado()">Confirmar Cambio</button>
            <?php else: ?>
              <div class="alerta-finalizado">
                <strong>Trámite Finalizado:</strong>
                <?php echo ($estado_actual == 1) ? "La reserva está autorizada." : "La reserva ha sido rechazada."; ?>
              </div>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary btn-cerrar" data-bs-dismiss="myModal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </dialog>

  <!--Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/bootstrap.bundle.min.js"></script>

  <!--DataTables -->
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

      const modal = document.querySelector("#myModal");
      const btnOpen = document.querySelector("#openModal");
      const btnClose = document.querySelector("#closeModal");

      // Abrir modal
      $(document).on("click", ".btn-editar", function (e) {
        modal.showModal();
        e.preventDefault();

        $("#modal-id-title").text($(this).data("id"));
        $("#reserva_id_modal").val($(this).data("reserva"));
        $("#modal-nombre").text($(this).data("nombre"));
        $("#modal-evento").text($(this).data("evento"));
        $("#modal-sala").text($(this).data("sala"));
        $("#input-estado").val($(this).data("estado"));
      });


      // Cerrar modal con el botón
      // btnClose.addEventListener("click", () => modal.close());
      $(document).on("click", ".btn-cerrar", function (e) {
        modal.close();
      });

      // Cerrar al hacer clic fuera (en el backdrop)
      modal.addEventListener("click", (e) => {
        if (e.target === modal) modal.close();
      });
    });

    function enviarCambioEstado() {
      const id = document.getElementById('reserva_id_modal').value;
      const estado = document.getElementById('nuevo_estado_modal').value;
      if (!confirm('¿Está seguro? Esta acción es irreversible una vez autorizada o rechazada.')) return;
      
      // Usamos FormData para enviar los datos de forma limpia
      const datos = new FormData();
      datos.append('id_reserva', id);
      datos.append('nuevo_estado', estado);
      fetch('procesar_autorizacion.php', {
        method: 'POST',
        body: datos
      })
        .then(response => response.json()) // Esperamos una respuesta JSON del PHP
        .then(data => {
          if (data.status === 'success') {
            alert(data.msg);
            location.reload(); // Recargamos para ver los cambios y el bloqueo
          } else {
            alert('Error: ' + data.msg);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Ocurrió un error al procesar la solicitud.');
        });
    }
  </script>
  <?php

} // Cierre de la función


/**
 * Inserta registros calculando horas excedentes según el horario de cobertura
 */
function insertar_autorizacion_individual($booking, $id)
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

  // Preparar el SQL con las horas calculadas
  $sql = "REPLACE INTO reservas_autorizaciones (
                    reserva_id,
                    tipo_solicitante, 
                    legajo, 
                    apellido_nombre,
                    vigilancia,
                    horas_vigilancia, 
                    limpieza,
                    horas_limpieza, 
                    mayordomia,
                    horas_mayordomia 
                ) VALUES (
                    $id,
                    1,
                    12345,
                    '" . sql_escape($booking['create_by']) . "',
                    " . (int) $datos['vigilancia'] . ",
                    " . number_format((float) $datos['h_vigilancia'], 2, '.', '') . ",
                    " . (int) $datos['limpieza'] . ",
                    " . number_format((float) $datos['h_limpieza'], 2, '.', '') . ",
                    " . (int) $datos['mayordomia'] . ",
                    " . number_format((float) $datos['h_mayordomia'], 2, '.', '') . "
                    )";
  sql_command($sql);

  $res = sql_query($sql);

  if ($res === false) {
    // Si la función devuelve -1, hubo un error en la base de datos
    echo "<h3>Error en la inserción de autorizaciones:</h3>";
    echo "<strong>SQL ejecutado:</strong> " . htmlspecialchars($sql) . "<br>";
    echo "<strong>Error de la DB:</strong> " . sql_error() . "<br>";
    //exit; // Detenemos la ejecución para poder leer el error

  }
}
?>