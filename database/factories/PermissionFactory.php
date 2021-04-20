<?php

namespace Database\Factories\Pharaoh\Permission\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Pharaoh\Permission\Models\Group;
use Pharaoh\Permission\Models\Permission;

class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'group_id' => Group::factory(),
            'permission_key' => '1001',
        ];
    }
}
