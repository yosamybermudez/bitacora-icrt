<?php

namespace App\Entity;

use App\Repository\IncidenciaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=IncidenciaRepository::class)
 * @Vich\Uploadable
 */
class Incidencia
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
     * @ORM\Column(type="string", length=255)
     */
    private $estado;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $etiquetas = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $problema;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $solucion;

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
     * @ORM\ManyToMany(targetEntity=Area::class)
     */
    private $areas;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $adjuntoProblema;

    /**
     * @Vich\UploadableField(mapping="incidencia_adjunto_problema", fileNameProperty="adjuntoProblema")
     * @var File
     */
    private $adjuntoProblemaFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $adjuntoSolucion;

    /**
     * @Vich\UploadableField(mapping="incidencia_adjunto_solucion", fileNameProperty="adjuntoSolucion")
     * @var File
     */
    private $adjuntoSolucionFile;

    /**
     * @ORM\ManyToOne(targetEntity=IncidenciaTipo::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity=Credencial::class, inversedBy="incidencias")
     */
    private $credencial;

    public function __construct()
    {
        $this->areas = new ArrayCollection();
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

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getEtiquetas(): ?array
    {
        return $this->etiquetas;
    }

    public function setEtiquetas(array $etiquetas): self
    {
        $this->etiquetas = $etiquetas;

        return $this;
    }

    public function getProblema(): ?string
    {
        return $this->problema;
    }

    public function setProblema(string $problema): self
    {
        $this->problema = $problema;

        return $this;
    }

    public function getSolucion(): ?string
    {
        return $this->solucion;
    }

    public function setSolucion(?string $solucion): self
    {
        $this->solucion = $solucion;

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

    public function getAdjuntoProblema(): ?string
    {
        return $this->adjuntoProblema;
    }

    public function setAdjuntoProblema(string $adjuntoProblema = null): self
    {
        $this->adjuntoProblema = $adjuntoProblema;

        return $this;
    }

    public function getAdjuntoProblemaFile(): ?File
    {
        return $this->adjuntoProblemaFile;
    }

    public function setAdjuntoProblemaFile(?File $adjuntoProblemaFile): self
    {
        $this->adjuntoProblemaFile = $adjuntoProblemaFile;

        return $this;
    }

    public function getAdjuntoSolucion(): ?string
    {
        return $this->adjuntoSolucion;
    }

    public function setAdjuntoSolucion(string $adjuntoSolucion = null): self
    {
        $this->adjuntoSolucion = $adjuntoSolucion;

        return $this;
    }

    public function getAdjuntoSolucionFile(): ?File
    {
        return $this->adjuntoSolucionFile;
    }

    public function setAdjuntoSolucionFile(?File $adjuntoSolucionFile): self
    {
        $this->adjuntoSolucionFile = $adjuntoSolucionFile;

        return $this;
    }

    public function getTipo(): ?IncidenciaTipo
    {
        return $this->tipo;
    }

    public function setTipo(?IncidenciaTipo $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getCredencial(): ?Credencial
    {
        return $this->credencial;
    }

    public function setCredencial(?Credencial $credencial): self
    {
        $this->credencial = $credencial;

        return $this;
    }

}
