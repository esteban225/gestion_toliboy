<?php

namespace App\Modules\WorkLogs\UseCases;

use App\Modules\WorkLogs\Infrastructure\Repositories\WorkLogsRepository;
use Illuminate\Http\JsonResponse;

class ManageWorkLogUseCase
{
    public function __construct(private WorkLogsRepository $repo) {}

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
}
