<?php

namespace Okipa\LaravelBootstrapTableList\Test\helpers;

use Faker\Factory;
use Hash;
use Okipa\LaravelBootstrapTableList\Tests\Models\User;

trait Users
{
    public $faker;
    public $clearPassword;
    public $data;

    public function instanciateFaker()
    {
        $this->faker = Factory::create();
    }

    public function createMultipleUsers(int $count)
    {
        for ($ii = 0; $ii < $count; $ii++) {
            $this->createUniqueUser();
        }

        return app(User::class)->all();
    }

    public function createUniqueUser()
    {
        $databaseUser = app(User::class)->create($this->generateFakeUserData());

        return app(User::class)->find($databaseUser->id);
    }

    public function generateFakeUserData()
    {
        $this->clearPassword = $this->faker->password;

        return [
            'name'     => $this->faker->name,
            'email'    => $this->faker->email,
            'password' => Hash::make($this->clearPassword),
        ];
    }
}
