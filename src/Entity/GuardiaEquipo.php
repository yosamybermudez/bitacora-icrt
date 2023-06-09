<?php

namespace App\Entity;

use App\Repository\GuardiaEquipoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GuardiaEquipoRepository::class)
 */
class GuardiaEquipo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity=Trabajador::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $informatico_vsn_1;

    /**
     * @ORM\ManyToOne(targetEntity=Trabajador::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $informatico_vsn_2;

    /**
     * @ORM\ManyToOne(targetEntity=Trabajador::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $informatico_corporativa;

    /**
     * @ORM\ManyToOne(targetEntity=Trabajador::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $tecnico_estudio;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha_referencia;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color_calendario;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $creadoPor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $creadoEn;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $actualizadoPor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $actualizadoEn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $recurrencia;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getInformaticoVsn1(): ?Trabajador
    {
        return $this->informatico_vsn_1;
    }

    public function setInformaticoVsn1(?Trabajador $informatico_vsn_1): self
    {
        $this->informatico_vsn_1 = $informatico_vsn_1;

        return $this;
    }

    public function getInformaticoVsn2(): ?Trabajador
    {
        return $this->informatico_vsn_2;
    }

    public function setInformaticoVsn2(?Trabajador $informatico_vsn_2): self
    {
        $this->informatico_vsn_2 = $informatico_vsn_2;

        return $this;
    }

    public function getInformaticoCorporativa(): ?Trabajador
    {
        return $this->informatico_corporativa;
    }

    public function setInformaticoCorporativa(?Trabajador $informatico_corporativa): self
    {
        $this->informatico_corporativa = $informatico_corporativa;

        return $this;
    }

    public function getTecnicoEstudio(): ?Trabajador
    {
        return $this->tecnico_estudio;
    }

    public function setTecnicoEstudio(?Trabajador $tecnico_estudio): self
    {
        $this->tecnico_estudio = $tecnico_estudio;

        return $this;
    }

    public function getFechaReferencia(): ?\DateTimeInterface
    {
        return $this->fecha_referencia;
    }

    public function setFechaReferencia(\DateTimeInterface $fecha_referencia): self
    {
        $this->fecha_referencia = $fecha_referencia;

        return $this;
    }

    public function getColorCalendario(): ?string
    {
        return $this->color_calendario;
    }

    public function setColorCalendario(?string $color_calendario): self
    {
        $this->color_calendario = $color_calendario;

        return $this;
    }

    public function getCreadoEn(): ?\DateTimeInterface
    {
        return $this->creadoEn;
    }

    public function setCreadoEn(?\DateTimeInterface $creadoEn): self
    {
        $this->creadoEn = $creadoEn;

        return $this;
    }

    public function getActualizadoEn(): ?\DateTimeInterface
    {
        return $this->actualizadoEn;
    }

    public function setActualizadoEn(?\DateTimeInterface $actualizadoEn): self
    {
        $this->actualizadoEn = $actualizadoEn;

        return $this;
    }

    public function getCreadoPor(): ?Usuario
    {
        return $this->creadoPor;
    }

    public function setCreadoPor(?Usuario $creadoPor): self
    {
        $this->creadoPor = $creadoPor;

        return $this;
    }

    public function getActualizadoPor(): ?Usuario
    {
        return $this->actualizadoPor;
    }

    public function setActualizadoPor(?Usuario $actualizadoPor): self
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    public function getRecurrencia(): ?string
    {
        return $this->recurrencia;
    }

    public function setRecurrencia(string $recurrencia): self
    {
        $this->recurrencia = $recurrencia;

        return $this;
    }
}
