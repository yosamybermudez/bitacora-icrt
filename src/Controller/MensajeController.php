<?php

namespace App\Controller;

use App\Repository\TrabajadorRepository;
use App\Service\BotTelegram;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MensajeController extends AppController
{
    /**
     * @Route("/mensaje", name="app_mensaje")
     */
    public function index(Request $request, BotTelegram $botTelegram, TrabajadorRepository $trabajadorRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('mensaje', TextareaType::class,[
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('nombres', TextType::class, [
                'label' => 'Enviar a',
                'attr' => [
                    'class' => 'taginput taginput-nombres'
                ],
                'label_attr' => [
                    'class' => 'label-tagsinput label-nombres'
                ],
                'required' => false
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($form->getData()['mensaje']){
                try{
                    if($request->request->get('ids_hidden') && $request->request->get('ids_hidden') !== ''){
                        $ids = explode(',', $request->request->get('ids_hidden'));
                        foreach ($ids as $id){
                            $botTelegram->sendMessage($form->getData()['mensaje'],$this->getUser()->getTrabajador()->getNombreCompleto(), $id);
                        }
                    } else {
                        $botTelegram->sendMessage($form->getData()['mensaje'], $this->getUser()->getTrabajador()->getNombreCompleto());
                    }
                    $this->addCustomSuccess('Mensajes enviado correctamente');
                } catch (\Exception $e){
                    $this->addCustomError('No se pudo enviar el mensaje');
                    $this->addCustomError($e->getMessage());
                }
            }
            return $this->redirectToRoute('app_mensaje');
        }

        return $this->render('mensaje/index.html.twig', [
            'form' => $form->createView(),
            'trabajadores_telegram' => $trabajadorRepository->findTrabajadoresTelegram()
        ]);
    }
}
