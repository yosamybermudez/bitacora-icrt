<?php

namespace App\Entity;

use App\Repository\CredencialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CredencialRepository::class)
 */
class Credencial
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
    private $destino;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $creadoPor;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creadoEn;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $actualizadoPor;

    /**
     * @ORM\Column(type="datetime")
     */
    private $actualizadoEn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $usuario;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $ips = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $protocolos = [];

    /**
     * @ORM\ManyToMany(targetEntity=Area::class, inversedBy="credenciales")
     */
    private $areas;

    /**
     * @ORM\OneToMany(targetEntity=Incidencia::class, mappedBy="credencial")
     */
    private $incidencias;

    public function __construct()
    {
        $this->areas = new ArrayCollection();
        $this->incidencias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestino(): ?string
    {
        return $this->destino;
    }

    public function setDestino(string $destino): self
    {
        $this->destino = $destino;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        if (false == empty($password)) {
            $this->password = $password;
        }

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

    public function getCreadoEn(): ?\DateTimeInterface
    {
        return $this->creadoEn;
    }

    public function setCreadoEn(\DateTimeInterface $creadoEn): self
    {
        $this->creadoEn = $creadoEn;

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

    public function getActualizadoEn(): ?\DateTimeInterface
    {
        return $this->actualizadoEn;
    }

    public function setActualizadoEn(\DateTimeInterface $actualizadoEn): self
    {
        $this->actualizadoEn = $actualizadoEn;

        return $this;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getIps(): ?array
    {
        return $this->ips;
    }

    public function setIps(array $ips): self
    {
        $this->ips = $ips;

        return $this;
    }

    public function getProtocolos(): ?array
    {
        return $this->protocolos;
    }

    public function setProtocolos(?array $protocolos): self
    {
        $this->protocolos = $protocolos;

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

    /**
     * @return Collection<int, Incidencia>
     */
    public function getIncidencias(): Collection
    {
        return $this->incidencias;
    }

    public function addIncidencia(Incidencia $incidencia): self
    {
        if (!$this->incidencias->contains($incidencia)) {
            $this->incidencias[] = $incidencia;
            $incidencia->setCredencial($this);
        }

        return $this;
    }

    public function removeIncidencia(Incidencia $incidencia): self
    {
        if ($this->incidencias->removeElement($incidencia)) {
            // set the owning side to null (unless already changed)
            if ($incidencia->getCredencial() === $this) {
                $incidencia->setCredencial(null);
            }
        }

        return $this;
    }
}
