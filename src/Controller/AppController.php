<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Interfaces\ModuloEnlace;
use App\Repository\AppConfiguracionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Flasher\Toastr\Prime\ToastrFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;

class AppController extends AbstractController
{
    protected $flasher;
    protected $toastr;
    protected $transport;
    protected $manager;
    protected $config;

    public function __construct(FlasherInterface $flasher, ToastrFactory $toastr, TransportInterface $transport, EntityManagerInterface $manager, AppConfiguracionRepository $config)
    {
        $this->flasher = $flasher->create('toastr');
        $this->toastr = $toastr;
        $this->transport = $transport;
        $this->manager = $manager;
        $this->config = $config;
    }

    public function addCreated()
    {
        $this->toastr->title('Notificación')
            ->success('Elemento registrado satisfactoriamente')
            ->timeOut(2000)
            ->progressBar()
            ->flash();
    }

    public function addCreatedError()
    {
        $this->toastr->title('Notificación')
            ->error('Ocurrió un error al registrar el elemento')
            ->timeOut(2000)
            ->progressBar()
            ->flash();
    }

    public function addEmailSent()
    {
        $this->toastr->title('Notificación')
            ->success('Email enviado satisfactoriamente')
            ->timeOut(2000)
            ->progressBar()
            ->flash();
    }

    public function addUpdated()
    {
        $this->toastr->title('Notificación')
            ->success('Elemento modificado satisfactoriamente')
            ->timeOut(2000)
            ->progressBar()
            ->flash();
    }

    public function addUpdatedError()
    {
        $this->toastr->title('Notificación')
            ->error('Ocurrió un error al modificar el elemento')
            ->timeOut(2000)
            ->progressBar()
            ->flash();
    }

    public function addDeleted()
    {
        $this->toastr->title('Notificación')
            ->success('Elemento eliminado satisfactoriamente')
            ->timeOut(2000)
            ->progressBar()
            ->flash();
    }

    public function addDeletedError()
    {
        $this->toastr->title('Notificación')
            ->error('Ocurrió un error al eliminar el elemento')
            ->timeOut(2000)
            ->progressBar()
            ->flash();
    }

    public function addCustomSuccess(string $message)
    {
        $timeout = strlen($message) > 50 ? 5000 : 2000;
        $this->toastr->title('Notificación')
            ->success($message)
            ->timeOut($timeout)
            ->progressBar()
            ->flash();
    }

    public function addCustomError(string $message)
    {
        $timeout = strlen($message) > 50 ? 5000 : 2000;
        $this->toastr->title('Notificación')
            ->error($message)
            ->timeOut($timeout)
            ->progressBar()
            ->flash();
    }

}
