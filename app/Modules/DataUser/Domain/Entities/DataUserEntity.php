<?php

namespace App\Modules\DataUser\Domain\Entities;

/**
 * Entidad que representa los datos adicionales de un usuario.
 *
 * @property int|null $id Identificador único de la entidad.
 * @property int|null $user_id Identificador del usuario asociado.
 * @property string|null $num_phone Número de teléfono principal del usuario.
 * @property string|null $num_phone_alt Número de teléfono alternativo del usuario.
 * @property string|null $num_identification Número de identificación del usuario.
 * @property string|null $identification_type Tipo de identificación (por ejemplo, DNI, pasaporte).
 * @property string|null $address Dirección del usuario.
 * @property string|null $emergency_contact Nombre del contacto de emergencia.
 * @property string|null $emergency_phone Teléfono del contacto de emergencia.
 * @property string|null $createdAt Fecha de creación del registro.
 * @property string|null $updatedAt Fecha de última actualización del registro.
 *
 * @author Tu Nombre
 */
class DataUserEntity
{
    private ?int $id;

    private ?int $user_id;

    private ?string $num_phone;

    private ?string $num_phone_alt;

    private ?string $num_identification;

    private ?string $identification_type;

    private ?string $address;

    private ?string $emergency_contact;

    private ?string $emergency_phone;

    private ?string $createdAt;

    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        ?int $user_id,
        ?string $num_phone,
        ?string $num_phone_alt,
        ?string $num_identification,
        ?string $identification_type,
        ?string $address,
        ?string $emergency_contact,
        ?string $emergency_phone,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->num_phone = $num_phone;
        $this->num_phone_alt = $num_phone_alt;
        $this->num_identification = $num_identification;
        $this->identification_type = $identification_type;
        $this->address = $address;
        $this->emergency_contact = $emergency_contact;
        $this->emergency_phone = $emergency_phone;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getNumPhone(): ?string
    {
        return $this->num_phone;
    }

    public function getNumPhoneAlt(): ?string
    {
        return $this->num_phone_alt;
    }

    public function getNumIdentification(): ?string
    {
        return $this->num_identification;
    }

    public function getIdentificationType(): ?string
    {
        return $this->identification_type;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getEmergencyContact(): ?string
    {
        return $this->emergency_contact;
    }

    public function getEmergencyPhone(): ?string
    {
        return $this->emergency_phone;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    // Setters (opcional, según si necesitas modificar los valores)
    // public function setNumPhone(?string $num_phone): void { $this->num_phone = $num_phone; }
    // ...otros setters según necesidad...

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'num_phone' => $this->getNumPhone(),
            'num_phone_alt' => $this->getNumPhoneAlt(),
            'num_identification' => $this->getNumIdentification(),
            'identification_type' => $this->getIdentificationType(),
            'address' => $this->getAddress(),
            'emergency_contact' => $this->getEmergencyContact(),
            'emergency_phone' => $this->getEmergencyPhone(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
