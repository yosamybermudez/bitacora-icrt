<?php

namespace App\Entity;

use App\Repository\TareaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TareaRepository::class)
 */
class Tarea
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
    private $titulo;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

    /**
     * @ORM\ManyToOne(targetEntity=TareaTipo::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipo;

    /**
     * @ORM\ManyToMany(targetEntity=Area::class)
     */
    private $areas;

    /**
     * @ORM\Column(type="boolean")
     */
    private $periodica;

    /**
     * @ORM\OneToOne(targetEntity=TareaRecurrencia::class, mappedBy="tarea", cascade={"persist", "remove"})
     */
    private $tarea_recurrencia;

    /**
     * @ORM\OneToOne(targetEntity=TareaEspecifica::class, mappedBy="tarea", cascade={"persist", "remove"})
     */
    private $tarea_especifica;

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


    public function __construct()
    {
        $this->areas = new ArrayCollection();
        $this->asignado_a = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }


    public function getTipo(): ?TareaTipo
    {
        return $this->tipo;
    }

    public function setTipo(?TareaTipo $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return Collection<int, Area>
     */
    public function getAreas(): Collection
    {
        return $this->areas;
    }

    public function addArea(Area $area): self
    {
        if (!$this->areas->contains($area)) {
            $this->areas[] = $area;
        }

        return $this;
    }

    public function removeArea(Area $area): self
    {
        $this->areas->removeElement($area);

        return $this;
    }

    public function getTareaRecurrencia(): ?TareaRecurrencia
    {
        return $this->tarea_recurrencia;
    }

    public function setTareaRecurrencia(?TareaRecurrencia $tarea_recurrencia): self
    {
        // unset the owning side of the relation if necessary
        if ($tarea_recurrencia === null && $this->tarea_recurrencia !== null) {
            $this->tarea_recurrencia->setTarea(null);
        }

        // set the owning side of the relation if necessary
        if ($tarea_recurrencia !== null && $tarea_recurrencia->getTarea() !== $this) {
            $tarea_recurrencia->setTarea($this);
        }

        $this->tarea_recurrencia = $tarea_recurrencia;

        return $this;
    }

    public function getTareaEspecifica(): ?TareaEspecifica
    {
        return $this->tarea_especifica;
    }

    public function setTareaEspecifica(?TareaEspecifica $tarea_especifica): self
    {
        // unset the owning side of the relation if necessary
        if ($tarea_especifica === null && $this->tarea_especifica !== null) {
            $this->tarea_especifica->setTarea(null);
        }

        // set the owning side of the relation if necessary
        if ($tarea_especifica !== null && $tarea_especifica->getTarea() !== $this) {
            $tarea_especifica->setTarea($this);
        }

        $this->tarea_especifica = $tarea_especifica;

        return $this;
    }

    public function isPeriodica(): ?bool
    {
        return $this->periodica;
    }

    public function setPeriodica(bool $periodica): self
    {
        $this->periodica = $periodica;

        return $this;
    }

    public function getCreadoEn(): ?\DateTimeInterface
    {
        return $this->creadoEn;
    }

    public function setCreadoEn(\DateTimeInterface $creadoEn): self
    {
        $this->creadoEn = $creadoEn;

        return $this;
    }

    public function getActualizadoEn(): ?\DateTimeInterface
    {
        return $this->actualizadoEn;
    }

    public function setActualizadoEn(\DateTimeInterface $actualizadoEn): self
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
}
