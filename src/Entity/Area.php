<?php

namespace App\Entity;

use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AreaRepository::class)
 */
class Area
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
     * @ORM\OneToMany(targetEntity=Trabajador::class, mappedBy="area")
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\ManyToMany(targetEntity=Credencial::class, mappedBy="areas")
     */
    private $credenciales;

    /**
     * @ORM\ManyToMany(targetEntity=Documento::class, mappedBy="areas")
     */
    private $documentos;

    public function __construct()
    {
        $this->trabajadores = new ArrayCollection();
        $this->credenciales = new ArrayCollection();
        $this->documentos = new ArrayCollection();
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
            $trabajadore->setArea($this);
        }

        return $this;
    }

    public function removeTrabajadore(Trabajador $trabajadore): self
    {
        if ($this->trabajadores->removeElement($trabajadore)) {
            // set the owning side to null (unless already changed)
            if ($trabajadore->getArea() === $this) {
                $trabajadore->setArea(null);
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection<int, Credencial>
     */
    public function getCredenciales(): Collection
    {
        return $this->credenciales;
    }

    public function addCredenciale(Credencial $credenciale): self
    {
        if (!$this->credenciales->contains($credenciale)) {
            $this->credenciales[] = $credenciale;
            $credenciale->addArea($this);
        }

        return $this;
    }

    public function removeCredenciale(Credencial $credenciale): self
    {
        if ($this->credenciales->removeElement($credenciale)) {
            $credenciale->removeArea($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Documento>
     */
    public function getDocumentos(): Collection
    {
        return $this->documentos;
    }

    public function addDocumento(Documento $documento): self
    {
        if (!$this->documentos->contains($documento)) {
            $this->documentos[] = $documento;
            $documento->addArea($this);
        }

        return $this;
    }

    public function removeDocumento(Documento $documento): self
    {
        if ($this->documentos->removeElement($documento)) {
            $documento->removeArea($this);
        }

        return $this;
    }
}
