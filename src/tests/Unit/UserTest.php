<?php

use App\User;

use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testClassConstructor()
    {
        $user = new User(18, 'John');

        $this->assertSame('John', $user->name);
        $this->assertSame(18, $user->age);
        $this->assertEmpty($user->favorite_movies);
    }
}