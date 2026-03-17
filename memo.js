//Capturo el radiobutton seleccionado para el tipo de consulta (POR EDIFICIO/POR SALA)
var tipo_consulta = 1
var rad = document.informes.radio_tipo_informe;
for (var i = 0; i < rad.length; i++) {
    rad[i].addEventListener('change', function () {
        switch (this.value) {
            case "1":
                document.getElementById("edificio").disabled = true
                document.getElementById("sala").disabled = true
                document.getElementById("casa_cultura").disabled = false
                document.getElementById("colon").disabled = false
                document.getElementById("rondeau").disabled = false
                document.getElementById("alem").disabled = false
                tipo_consulta = 1
                break
            case "2":
                document.getElementById("edificio").disabled = false
                document.getElementById("sala").disabled = false
                document.getElementById("casa_cultura").disabled = true
                document.getElementById("colon").disabled = true
                document.getElementById("rondeau").disabled = true
                document.getElementById("alem").disabled = true
                tipo_consulta = 2
                break
        }
        console.log(this.value)
    });
}
// Si el informe es por edificio, capturo el valor de los radio para saber que edificio esta seleccionado
var edificio = "alem" 
var radios_edificio = document.informes.radio_edificio;
for (var i = 0; i < radios_edificio.length; i++) {
    radios_edificio[i].addEventListener('change', function () {
        edificio = this.id
    })
}
// Capturamos el evento de envío del formulario

document.getElementById('informes').addEventListener('submit', function (event) {
    event.preventDefault() // Evitamos que se recargue la página
    fecha_desde = document.getElementById("desde_datepicker").value
    fecha_hasta = document.getElementById("hasta_datepicker").value

    if (tipo_consulta === 1) {
        var consulta = { tipo_consulta, fecha_desde, fecha_hasta, edificio }
    } else {
        edificio = document.getElementById("edificio").value
        sala = document.getElementById("sala").value
        console.log("tipoconsulta= "+ tipo_consulta)
        var consulta = { tipo_consulta, fecha_desde, fecha_hasta, edificio, sala }
    }
    const consulta_json = JSON.stringify(consulta)

    console.log(consulta_json)
    console.log(fecha_hasta)

    fetch('consultaMemo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: consulta_json
    })
        .then(response => response.text())
        .then(data => document.getElementById("reporte").innerHTML = data)
     
})



