<?php

namespace Tests\Unit;

use App\Modules\Auth\Domain\Entities\UserEntity;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    /** @test */
    public function it_can_create_a_user_entity()
    {
        $user = new UserEntity(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password',
            role_id: 'admin',
            position: 'manager',
            is_active: true,
            last_login: null
        );

        $this->assertInstanceOf(UserEntity::class, $user);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john@example.com', $user->getEmail());
        $this->assertEquals('password', $user->getPassword());
        $this->assertEquals('admin', $user->getRoleId());
        $this->assertEquals('manager', $user->getPosition());
        $this->assertTrue($user->getIsActive());
        $this->assertNull($user->getLastLogin());
    }

    /** @test */
    public function it_can_set_and_get_the_name()
    {
        $user = new UserEntity(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password',
            role_id: 'admin',
            position: 'manager',
            is_active: true,
            last_login: null
        );

        $user->setName('Jane Doe');

        $this->assertEquals('Jane Doe', $user->getName());
    }

    /** @test */
    public function it_can_activate_and_deactivate_a_user()
    {
        $user = new UserEntity(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password',
            role_id: 'admin',
            position: 'manager',
            is_active: false,
            last_login: null
        );

        $this->assertFalse($user->getIsActive());

        $user->setIsActive(true);

        $this->assertTrue($user->getIsActive());
        $this->assertTrue($user->isActive());
    }

    /** @test */
    public function it_can_create_a_user_entity_from_an_array()
    {
        $data = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'role_id' => 'admin',
            'position' => 'manager',
            'is_active' => true,
            'last_login' => null,
        ];

        $user = UserEntity::fromArray($data);

        $this->assertInstanceOf(UserEntity::class, $user);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john@example.com', $user->getEmail());
        $this->assertEquals('password', $user->getPassword());
        $this->assertEquals('admin', $user->getRoleId());
        $this->assertEquals('manager', $user->getPosition());
        $this->assertTrue($user->getIsActive());
        $this->assertNull($user->getLastLogin());
    }

    /** @test */
    public function it_can_convert_a_user_entity_to_an_array()
    {
        $user = new UserEntity(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password',
            role_id: 'admin',
            position: 'manager',
            is_active: true,
            last_login: null
        );

        $data = $user->toArray();

        $this->assertIsArray($data);
        $this->assertEquals(1, $data['id']);
        $this->assertEquals('John Doe', $data['name']);
        $this->assertEquals('john@example.com', $data['email']);
        $this->assertEquals('password', $data['password']);
        $this->assertEquals('admin', $data['role_id']);
        $this->assertEquals('manager', $data['position']);
        $this->assertTrue($data['is_active']);
        $this->assertNull($data['last_login']);
    }
}
