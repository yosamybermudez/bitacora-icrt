<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/tablero", name="app_admin_dashboard")
     */
    public function index(): Response
    {
        $tarea_mensajes_diarios_telegram_status = $this->tareaMensajesDiariosTelegram();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'tarea_mensajes_diarios_telegram_status' => $tarea_mensajes_diarios_telegram_status
        ]);
    }

    private function tareaMensajesDiariosTelegram(){
        $cmd = 'schtasks /query /tn "Bitacora Mensajes diarios Telegram" /fo LIST /v';


        exec($cmd, $output, $return);
        if($return === 1){
            return ['clase' => 'danger' , 'mensaje' => 'Tarea inexistente'];
        }


        $output = $this->outputArray($output);

        switch ($output['estado']   ) {
            case 'Deshabilitado': {
                return ['clase' => 'secondary' , 'mensaje' => 'Tarea inactiva', 'parametros' => $output];
            }
            case 'Listo': {
                return ['clase' => 'success' , 'mensaje' => 'Tarea activa', 'parametros' => $output];
            }
            default: {
                return ['clase' => 'warning', 'mensaje' => 'Estado no identificado'];
            }
        }
    }

    private function outputCSV($output, string $parameter){
        $parameter_pos = array_search($parameter, explode(',', $output[0]));
        if(!$parameter_pos){
            return 'N/E';
        }
        return str_replace('"','',explode(',',$output[1])[$parameter_pos]);
    }

    private function outputLIST($output, string $parameter)
    {
        $parameter .= ':';
        $parameter = utf8_decode($parameter);

        $match_key = array_key_first(array_filter($output, function($var) use ($parameter) { return stristr($var, $parameter); }));
        if(!$match_key){
            return 'N/A';
        }

        return utf8_encode(ltrim(str_replace($parameter,'',$output[$match_key])));
    }

    private function outputArray($output){
        return [
          'estado' => $this->outputLIST($output,'Estado'),
          'tipo_programacion' => $this->outputLIST($output,'Tipo de programación'),
          'hora_inicio' => $this->outputLIST($output,'Hora de inicio'),
          'fecha_inicio' => $this->outputLIST($output,'Fecha de inicio'),
          'dias' => $this->outputLIST($output,'D¡as'),
          'meses' => $this->outputLIST($output,'Meses'),
          'hora_proxima_ejecucion' => $this->outputLIST($output,'Hora próxima ejecución'),
          'ultimo_tiempo_ejecucion' => $this->outputLIST($output,'Último tiempo ejecución'),
          'ultimo_resultado' => $this->outputLIST($output,'Último resultado'),
          'tarea' => $this->outputLIST($output,'Tarea que se ejecutar '),
        ];
    }
}
