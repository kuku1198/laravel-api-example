<?php

namespace Tests\Unit;

use App\DTO\UserDTO;
use App\Exceptions\InvalidPasswordException;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $userService;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->userService = new UserService($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_get_all_users()
    {
        // Arrange
        $users = User::factory()->count(3)->make();
        $this->userRepository->shouldReceive('findAll')
            ->once()
            ->andReturn(new \Illuminate\Database\Eloquent\Collection($users));

        // Act
        $result = $this->userService->getAllUsers();

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    #[Test]
    public function it_can_get_user_by_id()
    {
        // Arrange
        $user = User::factory()->make(['id' => 1]);
        $this->userRepository->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->userService->getUser(1);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
    }

    #[Test]
    public function it_returns_null_when_user_not_found()
    {
        // Arrange
        $this->userRepository->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        // Act
        $result = $this->userService->getUser(999);

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_can_create_a_user()
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ];
        $userDTO = UserDTO::from($userData);
        $user = User::factory()->make(array_merge($userData, ['id' => 1]));

        $this->userRepository->shouldReceive('create')
            ->with($userData)
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->userService->createUser($userDTO);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('john@example.com', $result->email);
    }

    #[Test]
    public function it_can_update_a_user()
    {
        // Arrange
        $user = User::factory()->make([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];
        $userDTO = UserDTO::from($updateData);

        $this->userRepository->shouldReceive('findById')
            ->with(1)
            ->twice()
            ->andReturn($user);

        $this->userRepository->shouldReceive('update')
            ->with(1, $updateData)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->userService->updateUser(1, 'password123', $userDTO);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
    }

    #[Test]
    public function it_throws_exception_when_user_not_found_during_update()
    {
        // Arrange
        $this->userRepository->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $userDTO = UserDTO::from(['name' => 'Updated Name']);

        // Assert
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        // Act
        $this->userService->updateUser(999, 'password123', $userDTO);
    }

    #[Test]
    public function it_throws_exception_when_password_is_invalid()
    {
        // Arrange
        $user = User::factory()->make([
            'id' => 1,
            'password' => Hash::make('password123')
        ]);

        $this->userRepository->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $userDTO = UserDTO::from(['name' => 'Updated Name']);

        // Assert
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage('Invalid password');

        // Act
        $this->userService->updateUser(1, 'wrong_password', $userDTO);
    }

    #[Test]
    public function it_can_delete_a_user()
    {
        // Arrange
        $user = User::factory()->make(['id' => 1]);

        $this->userRepository->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($user);

        $this->userRepository->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->userService->deleteUser(1);

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function it_throws_exception_when_user_not_found_during_delete()
    {
        // Arrange
        $this->userRepository->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        // Assert
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        // Act
        $this->userService->deleteUser(999);
    }
}
