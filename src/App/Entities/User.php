<?php

namespace Api\App\Entities;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;

#[Entity]
#[Table("users")]
class User implements JsonSerializable
{
    #[Id]
    #[Column(), GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[Column(unique: true)]
    private string $email;

    #[Column]
    private string $password;

    #[Column(name: "mfa_secret", nullable: true)]
    private string $mfa_secret;

    #[Column(name: "tmp_secret", nullable: true)]
    private string $tmp_secret;

    #[Column(name: "is_admin")]
    private bool $isAdmin = false;

    #[Column(name: "is_active")]
    private bool $isActive = true;

    #[Column(name: "created_at", type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;

    #[Column(name: "updated_at", type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $updatedAt = null;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'isAdmin' => $this->isAdmin,
            'isActive' => $this->isActive,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getMfaSecret(): string
    {
        return $this->mfa_secret;
    }

    public function setMfaSecret(string $mfa_secret): self
    {
        $this->mfa_secret = $mfa_secret;

        return $this;
    }

    public function getTmpSecret(): string
    {
        return $this->tmp_secret;
    }

    public function setTmpSecret(string $tmp_secret): self
    {
        $this->tmp_secret = $tmp_secret;

        return $this;
    }

    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
