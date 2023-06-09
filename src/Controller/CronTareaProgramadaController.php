<?php

namespace App\Controller;

use App\Entity\Area;
use App\Entity\CronTareaProgramada;
use App\Form\CronTareaProgramadaType;
use App\Repository\AreaRepository;
use App\Repository\CronTareaProgramadaRepository;
use App\Repository\GuardiaEquipoRepository;
use App\Repository\TareaRecurrenciaRepository;
use App\Service\BotTelegram;
use PhpParser\Node\Stmt\Do_;
use RRule\RRule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/cron/tarea-programada")
 */
class CronTareaProgramadaController extends AppController
{
    /**
     * @Route("/", name="app_cron_tarea_programada_index", methods={"GET"})
     */
    public function index(CronTareaProgramadaRepository $cronTareaProgramadaRepository): Response
    {
        //Eliminar las tareas que no tienen contraparte en el SO.
        $tareas = $cronTareaProgramadaRepository->findAll();
        foreach ($tareas as $tarea){
            $tn = $tarea->getNombre();
            $command = "schtasks /query ";
            if($tn) { $command.= '/tn "Bitacora ' . $tn . '" '; }
            exec($command,$output, $result);
            if($result !== 0){
                $cronTareaProgramadaRepository->remove($tarea, true);
            }
        }

        return $this->render('cron_tarea_programada/index.html.twig', [
            'cron_tarea_programadas' => $cronTareaProgramadaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/tarea-principal/main", name="app_cron_tarea_programada_main", methods={"POST"})
     */
    public function createMain(Request $request): Response
    {
        if(!is_null($request->request->get('modificar_tarea_programada'))){
            $query = 'schtasks /change /tn "Bitacora Mensajes diarios Telegram" ';
            $st = $request->request->get('time');
            if($st) { $query .= '/st ' . $st . ' '; }
            exec($query,$output, $return);
            if($return === 0){
                $this->addUpdated();
            } else {
                $this->addUpdatedError();
            }
        } elseif(!is_null($request->request->get('crear_tarea_programada'))){
            $cmd = 'schtasks /create /sc DAILY /tn "Bitacora Mensajes diarios Telegram" /ru SYSTEM ';
            $st = $request->request->get('time');
            if($st) { $cmd .= '/st ' . $st . ' '; }
            $token = $this->config->getVariable('cron_main_telegram_token');
            $tr = $this->generateUrl('app_cron_tarea_programada_send_telegram_message', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            if($tr) { $cmd .= '/tr "curl ' . $tr . '" '; }

            exec($cmd,$output, $return);
            if($return === 0){
                $this->addCreated();
            } else {
                $this->addCreatedError();
            }
        } elseif(!is_null($request->request->get('eliminar_tarea_programada'))){
            $cmd = 'schtasks /delete /tn "Bitacora Mensajes diarios Telegram" /f';

            exec($cmd,$output, $return);

            if($return === 0){
                $this->addDeleted();
            } else {
                $this->addDeletedError();
            }
        } elseif(!is_null($request->request->get('ejecutar_tarea_programada'))){
            $cmd = 'schtasks /run /tn "Bitacora Mensajes diarios Telegram"';

            exec($cmd,$output, $return);

            if($return === 0){
                $this->addCustomSuccess('Tarea ejecutada con éxito');
            } else {
                $this->addCustomError('No se pudo ejecutar la tarea programada');
            }
            sleep(5);
        } elseif(!is_null($request->request->get('habilitar_tarea_programada'))){
            $cmd = 'schtasks /change /tn "Bitacora Mensajes diarios Telegram" /enable';

            exec($cmd,$output, $return);

            if($return === 0){
                $this->addUpdated();
            } else {
                $this->addUpdatedError();
            }
        } elseif(!is_null($request->request->get('inhabilitar_tarea_programada'))){
            $cmd = 'schtasks /change /tn "Bitacora Mensajes diarios Telegram" /disable';

            exec($cmd,$output, $return);

            if($return === 0){
                $this->addUpdated();
            } else {
                $this->addUpdatedError();
            }
        } else {
            $this->addCustomError('No se realizó ninguna acción');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    /**
     * @Route("/new", name="app_cron_tarea_programada_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CronTareaProgramadaRepository $cronTareaProgramadaRepository): Response
    {
        $cronTareaProgramada = new CronTareaProgramada();
        $form = $this->createForm(CronTareaProgramadaType::class, $cronTareaProgramada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repeticion = $request->request->get('cron_tarea_programada')['repeticion'];
            $tn = $cronTareaProgramada->getNombre();
            $st = $repeticion['hora'];
            $sc = $repeticion['frequency'];
            $cmd = "curl http://localhost/bitacora/public/cron/telegram/";
            $token = $this->config->getVariable('cron_token');
            $tr = $cmd . $token;
            $d = (isset($repeticion['by_day']) && $repeticion['by_day']) ? implode(',', $repeticion['by_day']) : null;


            $command = "schtasks /create ";
            if($sc) { $command.= '/sc ' . $sc . ' '; }
            if($tn) { $command.= '/tn "Bitacora ' . $tn . '" '; }
            if($tr) { $command.= '/tr "' . $tr . '" '; }
            if($st) { $command.= '/st ' . $st . ' '; }
            if($d) { $command.= '/d ' . $d . ' '; }
            $command.= '/ru SYSTEM';

//            dd($command);

            exec($command,$output, $return);

            if($return === 0){
//                $cronTareaProgramadaRepository->add($cronTareaProgramada, true);
                $this->addCreated();
            } else {
                $this->addCustomError('No se pudo crear la tarea programada');
            }

            return $this->redirectToRoute('app_cron_tarea_programada_show', ['id' => $cronTareaProgramada->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cron_tarea_programada/new.html.twig', [
            'cron_tarea_programada' => $cronTareaProgramada,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_cron_tarea_programada_show", methods={"GET"})
     */
    public function show(CronTareaProgramada $cronTareaProgramada): Response
    {
        return $this->render('cron_tarea_programada/show.html.twig', [
            'cron_tarea_programada' => $cronTareaProgramada,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_cron_tarea_programada_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CronTareaProgramada $cronTareaProgramada, CronTareaProgramadaRepository $cronTareaProgramadaRepository): Response
    {
        $form = $this->createForm(CronTareaProgramadaType::class, $cronTareaProgramada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cronTareaProgramadaRepository->add($cronTareaProgramada, true);

            return $this->redirectToRoute('app_cron_tarea_programada_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cron_tarea_programada/edit.html.twig', [
            'cron_tarea_programada' => $cronTareaProgramada,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_cron_tarea_programada_delete", methods={"POST"})
     */
    public function delete(Request $request, CronTareaProgramada $cronTareaProgramada, CronTareaProgramadaRepository $cronTareaProgramadaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cronTareaProgramada->getId(), $request->request->get('_token'))) {
            $tn = $cronTareaProgramada->getNombre();
            $command = "schtasks /delete /f ";
            if($tn) { $command.= '/tn "' . $tn . '" '; }

            system($command,$output);

            if($output === 0){
                $cronTareaProgramadaRepository->remove($cronTareaProgramada, true);
                $this->addDeleted();
            } else {
                $this->addCustomError('No se pudo eliminar la tarea programada');
            }
        }

        return $this->redirectToRoute('app_cron_tarea_programada_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/telegram/{token}", name="app_cron_tarea_programada_send_telegram_message")
     */
    public function sendMessage(string  $token, BotTelegram $botTelegram, GuardiaEquipoRepository $guardiaEquipoRepository, TareaRecurrenciaRepository $tareaRecurrenciaRepository, AreaRepository $areaRepository): Response
    {
        if($this->config->getVariable('cron_main_telegram_token') and $token != $this->config->getVariable('cron_main_telegram_token')){
            $this->writeFile(sprintf('El token "%s" es invalido', $token));
            return new JsonResponse(['error' => sprintf('El token %s es invalido', $token)],400);
        }

        try{
            $this->sendMessageDiario($tareaRecurrenciaRepository, $guardiaEquipoRepository, $areaRepository, $botTelegram);
            $this->writeFile('Mensaje enviado correctamente');
            return new JsonResponse(['message' => 'OK'],400);
        } catch (\Exception $e){
            $this->writeFile($e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()],400);
        }
    }

    public function writeFile($txt){
        $file = $this->getParameter('kernel.project_dir') . "/var/cron-task-logs.txt";
        $this->tailCustom($file,10);
        $myfile = fopen($file, "a") or die("No se pudo encontrar el archivo");
        $fecha = new \DateTime();
        fwrite($myfile, $fecha->format('Y-m-d H:i:s') . " - " . $txt . PHP_EOL);
        fclose($myfile);
    }

    function tailCustom($filepath, $lines = 1, $adaptive = true) {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {

            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);

            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);

            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;

            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");

        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {

            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);

        }

        // Close file and return
        fclose($f);
        $output = trim($output) . "\r\n";
        $file = fopen($filepath, "w");
        fwrite($file, $output);
        fclose($file);
    }

    public function sendMessageDiario(
        TareaRecurrenciaRepository $tareaRecurrenciaRepository,
        GuardiaEquipoRepository $guardiaEquipoRepository,
        AreaRepository $areaRepository,
        BotTelegram $botTelegram)
    {

        $equipo = $guardiaEquipoRepository->findEquipoGuardiaHoy();

//        try{
            if($equipo !== null){
                $equipo_msg =
                    "&#128197; <strong>De guardia hoy</strong>\n\n" .
                    "&#x1F4BB; VSN: " . $equipo->getInformaticoVsn1()->getNombreCompleto() . "\n\n" .
                    "&#x1F4BB; VSN: " . $equipo->getInformaticoVsn2()->getNombreCompleto() . "\n\n" .
                    "&#x1F4BB; Corporativa: " . $equipo->getInformaticoCorporativa()->getNombreCompleto() . "\n\n" .
                    "&#128249; Estudio: " . $equipo->getTecnicoEstudio()->getNombreCompleto() . "\n\n";

                $botTelegram->sendMessage($equipo_msg);
                sleep(5);
            }


//        } catch (\Exception $e){
//            throw new \Exception('No se pudieron enviar los mensajes');
//        }

        $areas = $areaRepository->findAll();
        foreach ($areas as $area){
            $tareas = $tareaRecurrenciaRepository->findTareasPorAreaHoy($area);
            if(!$tareas){
                continue;
            }
            $tareas_periodicas_msg =
                "&#128197; <strong>Tareas periódicas de hoy: " . $area->getNombre() . "</strong>\n\n";
            foreach ($tareas as $tarea) {
                $tareas_periodicas_msg .= "&#x2705; " . $tarea->getTarea()->getTitulo() . "\n\n";
            }

            $botTelegram->sendMessage($tareas_periodicas_msg);
            sleep(5);
        }



        $cmd = 'schtasks /end /tn "Bitacora Mensajes diarios Telegram"';
        exec($cmd);
    }
}
