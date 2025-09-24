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
class DataUserEntity {
    
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public ?string $num_phone,
        public ?string $num_phone_alt,
        public ?string $num_identification,
        public ?string $identification_type,
        public ?string $address,
        public ?string $emergency_contact,
        public ?string $emergency_phone,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

}
