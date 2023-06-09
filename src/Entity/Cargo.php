<?php

namespace App\Entity;

use App\Repository\CargoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CargoRepository::class)
 */
class Cargo
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
     * @ORM\Column(type="string", length=5)
     */
    private $escala_salarial;

    /**
     * @ORM\Column(type="float")
     */
    private $salario_escala;

    /**
     * @ORM\OneToMany(targetEntity=Trabajador::class, mappedBy="cargo")
     */
    private $trabajadores;

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
        $this->trabajadores = new ArrayCollection();
    }

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

    public function getEscalaSalarial(): ?string
    {
        return $this->escala_salarial;
    }

    public function setEscalaSalarial(string $escala_salarial): self
    {
        $this->escala_salarial = $escala_salarial;

        return $this;
    }

    public function getSalarioEscala(): ?float
    {
        return $this->salario_escala;
    }

    public function setSalarioEscala(float $salario_escala): self
    {
        $this->salario_escala = $salario_escala;

        return $this;
    }

    /**
     * @return Collection<int, Trabajador>
     */
    public function getTrabajadores(): Collection
    {
        return $this->trabajadores;
    }

    public function addTrabajadore(Trabajador $trabajadore): self
    {
        if (!$this->trabajadores->contains($trabajadore)) {
            $this->trabajadores[] = $trabajadore;
            $trabajadore->setCargo($this);
        }

        return $this;
    }

    public function removeTrabajadore(Trabajador $trabajadore): self
    {
        if ($this->trabajadores->removeElement($trabajadore)) {
            // set the owning side to null (unless already changed)
            if ($trabajadore->getCargo() === $this) {
                $trabajadore->setCargo(null);
            }
        }

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

}
