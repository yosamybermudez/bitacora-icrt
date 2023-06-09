<?php

namespace App\Entity;

use App\Repository\TareaEspecificaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TareaEspecificaRepository::class)
 */
class TareaEspecifica
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
    private $estado = 'pendiente';

    /**
     * @ORM\ManyToMany(targetEntity=Usuario::class)
     */
    private $asignado_a;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha_cumplimiento;

    /**
     * @ORM\OneToOne(targetEntity=Tarea::class, inversedBy="tarea_especifica", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $tarea;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observaciones;

    public function __construct()
    {
        $this->asignado_a = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFechaCumplimiento(): ?\DateTimeInterface
    {
        return $this->fecha_cumplimiento;
    }

    public function setFechaCumplimiento(?\DateTimeInterface $fecha_cumplimiento): self
    {
        $this->fecha_cumplimiento = $fecha_cumplimiento;

        return $this;
    }

    /**
     * @return Collection<int, Usuario>
     */
    public function getAsignadoA(): Collection
    {
        return $this->asignado_a;
    }

    public function addAsignadoA(Usuario $asignadoA): self
    {
        if (!$this->asignado_a->contains($asignadoA)) {
            $this->asignado_a[] = $asignadoA;
        }

        return $this;
    }

    public function removeAsignadoA(Usuario $asignadoA): self
    {
        $this->asignado_a->removeElement($asignadoA);

        return $this;
    }

    public function getTareaRecurrencia(): ?TareaRecurrencia
    {
        return $this->tareaRecurrencia;
    }

    public function getTarea(): ?Tarea
    {
        return $this->tarea;
    }

    public function setTarea(Tarea $tarea): self
    {
        $this->tarea = $tarea;

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

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }
}
