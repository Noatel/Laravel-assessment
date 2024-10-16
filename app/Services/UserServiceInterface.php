<?php

namespace App\Services;

use App\Models\User;

interface UserServiceInterface
{
    /**
     * Generate random hash key.
     *
     * @param  string $key
     * @return string
     */
    public function hash(string $key);
    public function list();
    public function store(array $attributes);

    public function find(int $id): ?User;
    public function findWithThrashed(int $id): ?User;

    public function findThrashedUsers(int $id): ?User;

    public function update(int $id, array $attributes): bool;

    public function destroy(int $id);
    public function restore(int $id);
    public function delete(int $id);

}