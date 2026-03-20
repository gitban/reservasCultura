<?php
class VerificadorAutorizacion
{
    private $area_id;
    private $start_time;
    private $end_time;
    private $day_of_week;
    private $h_start;
    private $h_end;

    public function __construct($booking)
    {
        $this->room_id = (int) $booking['room_id'];
        $this->start_time = $booking['start_time'];
        $this->end_time = $booking['end_time'];
        $this->day_of_week = (int) date('w', $this->start_time);

        // Convertimos a horas decimales (ej: 13:30 -> 13.5)
        $this->h_start = (int) date('H', $this->start_time) + ((int) date('i', $this->start_time) / 60);
        $this->h_end = (int) date('H', $this->end_time) + ((int) date('i', $this->end_time) / 60);
    }

    public function determinarNecesidades()
    {
        $res = array(
            'vigilancia' => 0,
            'h_vigilancia' => 0,
            'mayordomia' => 0,
            'h_mayordomia' => 0,
            'limpieza' => 0,
            'h_limpieza' => 0,
            'req_auth' => 0
        );
        switch ($this->room_id) {
            // COLON -
            case '11': // Aula Magna
            case '12': // Bergé Vila
            case '13': // Fundadores

                //  Mayordomía: L a V 7:00 a 20:00
                $res['h_mayordomia'] = $this->calcularHorasExtras(1, 5, 7.0, 20.0);

                // Seguridad: L a S 6:00 a 14:00
                $res['h_vigilancia'] = $this->calcularHorasExtras(1, 6, 6.0, 14.0);

                // Limpieza (Borlenghi): L a V 6:00 a 20:00
                $res['h_limpieza'] = $this->calcularHorasExtras(1, 5, 6.0, 20.0);
                break;

            // RONDEAU
            case '34':
            case '36':
            case '39':
            case '40':
            case '48':
            case '51':

                // Mayordomía: L a V 7:00 a 20:00
                $res['h_mayordomia'] = $this->calcularHorasExtras(1, 7, 0.0, 23.59);

                // Seguridad: L a S 6:00 a 14:00
                $res['h_vigilancia'] = $this->calcularHorasExtras(1, 5, 7.0, 19.0);

                // Limpieza (Borlenghi): L a V 7:00 a 14:00
                $res['h_limpieza'] = $this->calcularHorasExtras(1, 5, 7.0, 14.0);
                break;

            // CASA DE LA CULTURA
            case '15':
            case '49':

                // Mayordomía: L a V 7:00 a 20:00
                $res['h_mayordomia'] = $this->calcularHorasExtras(1, 1, 8.0, 16.00);
                $res['h_mayordomia'] = $this->calcularHorasExtras(2, 5, 8.0, 20.00);
                $res['h_mayordomia'] = $this->calcularHorasExtras(6, 6, 8.0, 20.00);

                // Seguridad: L a S 6:00 a 14:00
                $res['h_vigilancia'] = $this->calcularHorasExtras(1, 7, 0.0, 23.59);

                // Limpieza (Borlenghi): L a V 7:00 a 14:00
                $res['h_limpieza'] = $this->calcularHorasExtras(1, 5, 7.0, 14.0);
                break;

            // ALEM - Playón y Salón de Actos
            case '41':
            case '16':

                // Mayordomía: L a V 6:00 a 20:00
                $res['h_mayordomia'] = $this->calcularHorasExtras(1, 5, 6.0, 20.00);

                // Seguridad: L a S 6:00 a 14:00
                $res['h_vigilancia'] = $this->calcularHorasExtras(1, 7, 0.0, 23.59);

                // Limpieza (Borlenghi): L a V 7:00 a 14:00
                $res['h_limpieza'] = $this->calcularHorasExtras(1, 5, 6.0, 20.0);
                $res['h_limpieza'] = ($this->room_id == '41')? $this->calcularHorasExtras(6, 6, 6.0, 14.0):'';
                break;
                
                }
                
        $res['h_audiovisuales'] = $this->calcularHorasExtras(1, 5, 8.0, 19.0);

        // Activar flags si hay horas calculadas
        $res['mayordomia'] = ($res['h_mayordomia'] > 0) ? 1 : 0;
        $res['vigilancia'] = ($res['h_vigilancia'] > 0) ? 1 : 0;
        $res['limpieza'] = ($res['h_limpieza'] > 0) ? 1 : 0;
        $res['audiovisuales'] = ($res['h_audiovisuales'] > 0) ? 1 : 0;

        if ($res['mayordomia'] || $res['vigilancia'] || $res['limpieza'] || $res['audiovisuales']) {
            $res['req_auth'] = 1;
        }
        return $res;
    }

    /**
     * Calcula cuántas horas de la reserva caen fuera del rango de cobertura
     */
    private function calcularHorasExtras($diaInicio, $diaFin, $hApertura, $hCierre)
    {
        // Si el día no tiene cobertura, toda la duración son horas extras
        if ($this->day_of_week < $diaInicio || $this->day_of_week > $diaFin) {
            return ($this->end_time - $this->start_time) / 3600;
        }

        $extras = 0;
        // Horas antes de la apertura
        if ($this->h_start < $hApertura) {
            $extras += min($hApertura, $this->h_end) - $this->h_start;
        }
        // Horas después del cierre
        if ($this->h_end > $hCierre) {
            $extras += $this->h_end - max($hCierre, $this->h_start);
        }

        return max(0, $extras);
    }
}