<?php

namespace App\Modules\Forms\UseCases;

use App\Modules\Forms\Infrastructure\Repositories\FormsRepository;
use Illuminate\Http\JsonResponse;

class ManageFormUseCase
{
    public function __construct(private FormsRepository $repo) {}

    public function list(array $filters): JsonResponse
    {
        $rows = $this->repo->all($filters);

        return response()->json(['count' => count($rows), 'rows' => $rows]);
    }

    public function get(string $id): JsonResponse
    {
        return response()->json($this->repo->find($id));
    }

    public function create(array $data): JsonResponse
    {
        $id = $this->repo->create($data);

        return response()->json(['id' => $id], 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        $this->repo->update($id, $data);

        return response()->json(['message' => 'updated']);
    }

    public function delete(string $id): JsonResponse
    {
        $this->repo->delete($id);

        return response()->json(['message' => 'deleted']);
    }
}
