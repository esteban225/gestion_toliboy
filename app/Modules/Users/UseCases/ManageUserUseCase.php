<?php

namespace App\Modules\Users\UseCases;

use App\Modules\Users\Infrastructure\Repositories\UsersRepository;
use Illuminate\Http\JsonResponse;

class ManageUserUseCase
{
    public function __construct(private UsersRepository $repo) {}

    public function list(array $filters): JsonResponse { return response()->json($this->repo->all($filters)); }
    public function get(string $id): JsonResponse { return response()->json($this->repo->find($id)); }
    public function create(array $data): JsonResponse { return response()->json(['id'=>$this->repo->create($data)], 201); }
    public function update(string $id, array $data): JsonResponse { $this->repo->update($id,$data); return response()->json(['message'=>'updated']); }
    public function delete(string $id): JsonResponse { $this->repo->delete($id); return response()->json(['message'=>'deleted']); }
}
