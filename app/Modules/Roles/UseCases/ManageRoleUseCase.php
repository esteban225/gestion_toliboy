<?php

namespace App\Modules\Roles\UseCases;

use App\Modules\Roles\Infrastructure\Repositories\RolesRepository;

class ManageRoleUseCase
{
    public function __construct(private RolesRepository $repo) {}

    public function list(array $filters) { return response()->json($this->repo->all($filters)); }
    public function create(array $data) { return response()->json(['id'=>$this->repo->create($data)],201); }
    public function update(string $id, array $data) { $this->repo->update($id,$data); return response()->json(['message'=>'ok']); }
}
