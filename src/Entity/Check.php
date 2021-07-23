<?php

namespace App\Entity;

use App\Repository\CheckRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CheckRepository::class)
 * @ORM\Table(name="`check`")
 */
class Check
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Todo::class, inversedBy="checked")
     * @ORM\JoinColumn(nullable=false)
     */
    private $todo;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="checks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTodo(): ?Todo
    {
        return $this->todo;
    }

    public function setTodo(Todo $todo): self
    {
        $this->todo = $todo;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
