<?php

namespace App\Entity;

use App\Repository\FechaConmemorativaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FechaConmemorativaRepository::class)
 */
class FechaConmemorativa
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $conmemoracion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recurrencia;

    /**
     * @ORM\Column(type="boolean")
     */
    private $feriado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getConmemoracion(): ?string
    {
        return $this->conmemoracion;
    }

    public function setConmemoracion(string $conmemoracion): self
    {
        $this->conmemoracion = $conmemoracion;

        return $this;
    }

    public function getRecurrencia(): ?string
    {
        return $this->recurrencia;
    }

    public function setRecurrencia(?string $recurrencia): self
    {
        $this->recurrencia = $recurrencia;

        return $this;
    }

    public function isFeriado(): ?bool
    {
        return $this->feriado;
    }

    public function setFeriado(bool $feriado): self
    {
        $this->feriado = $feriado;

        return $this;
    }
}
