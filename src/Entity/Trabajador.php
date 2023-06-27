<?php

namespace App\Entity;

use App\Repository\TrabajadorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=TrabajadorRepository::class)
 * @Vich\Uploadable
 */
class Trabajador
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
    private $nombres;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apellidos;

    /**
     * @ORM\Column(type="string", length=11, unique=true, nullable=true)
     */
    private $carne_identidad;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $telefonos = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $direccion;


    /**
     * @ORM\ManyToOne(targetEntity=Area::class, inversedBy="trabajadores")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telefono_principal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telefono_alternativo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $correo_electronico;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha_alta;

    /**
     * @ORM\ManyToOne(targetEntity=Cargo::class, inversedBy="trabajadores")
     */
    private $cargo;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $sexo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $foto;

    /**
     * @Vich\UploadableField(mapping="trabajador_foto", fileNameProperty="foto")
     * @var File
     */
    private $fotoFile;

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
     * @ORM\Column(type="boolean")
     */
    private $fake;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telegram_id;

    /**
     * @ORM\OneToOne(targetEntity=Usuario::class, mappedBy="trabajador", cascade={"persist", "remove"})
     */
    private $usuario;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $municipio;

    public function __construct()
    {
        $this->fake = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getNombreCompleto(): ?string
    {
        return $this->nombres . " " . $this->apellidos;
    }

    public function getNombreCorto(): ?string
    {
        return $this->nombres . " " . (isset($this->apellidos[0]) ? $this->apellidos[0] . '.' : '');
    }

    public function getCarneIdentidad(): ?string
    {
        return $this->carne_identidad;
    }

    public function setCarneIdentidad(string $carne_identidad): self
    {
        $this->carne_identidad = $carne_identidad;

        return $this;
    }

//    public function getTelefonos(): ?array
//    {
//        return $this->telefonos;
//    }

    public function setTelefonos(?array $telefonos): self
    {
        $this->telefonos = $telefonos;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }


    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getTelefonoPrincipal(): ?string
    {
        return $this->telefono_principal;
    }

    public function setTelefonoPrincipal(string $telefono_principal): self
    {
        $this->telefono_principal = $telefono_principal;

        return $this;
    }

    public function getTelefonoAlternativo(): ?string
    {
        return $this->telefono_alternativo;
    }

    public function setTelefonoAlternativo(?string $telefono_alternativo): self
    {
        $this->telefono_alternativo = $telefono_alternativo;

        return $this;
    }

    public function getCorreoElectronico(): ?string
    {
        return $this->correo_electronico;
    }

    public function setCorreoElectronico(?string $correo_electronico): self
    {
        $this->correo_electronico = $correo_electronico;

        return $this;
    }

    public function getFechaAlta(): ?\DateTimeInterface
    {
        return $this->fecha_alta;
    }

    public function setFechaAlta(\DateTimeInterface $fecha_alta): self
    {
        $this->fecha_alta = $fecha_alta;

        return $this;
    }

    public function getCargo(): ?Cargo
    {
        return $this->cargo;
    }

    public function setCargo(?Cargo $cargo): self
    {
        $this->cargo = $cargo;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(?string $sexo): self
    {
        $this->sexo = $sexo;

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

    public function isFake(): ?bool
    {
        return $this->fake;
    }

    public function setFake(bool $fake): self
    {
        $this->fake = $fake;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getFotoFile(): ?File
    {
        return $this->fotoFile;
    }

    public function setFotoFile(?File $fotoFile): self
    {
        $this->fotoFile = $fotoFile;

        return $this;
    }

    public function getTelegramId(): ?string
    {
        return $this->telegram_id;
    }

    public function setTelegramId(?string $telegram_id): self
    {
        $this->telegram_id = $telegram_id;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        // unset the owning side of the relation if necessary
        if ($usuario === null && $this->usuario !== null) {
            $this->usuario->setTrabajador(null);
        }

        // set the owning side of the relation if necessary
        if ($usuario !== null && $usuario->getTrabajador() !== $this) {
            $usuario->setTrabajador($this);
        }

        $this->usuario = $usuario;

        return $this;
    }

    public function getTelefonos(){
        return $this->getTelefonoPrincipal() . ($this->getTelefonoAlternativo() ? ', ' . $this->getTelefonoAlternativo() : '');
    }

    public function getMunicipio(): ?string
    {
        return $this->municipio;
    }

    public function setMunicipio(?string $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }

}
