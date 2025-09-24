<?php

namespace App\Modules\WorkLogs\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\WorkLogs\UseCases\ManageWorkLogUseCase;
use Illuminate\Http\Request;

class WorkLogsController extends Controller
{
    public function __construct(private ManageWorkLogUseCase $useCase) {}

    public function index(Request $request)
    {
        return $this->useCase->list($request->query());
    }

    public function store(Request $request)
    {
        return $this->useCase->create($request->all());
    }

    public function show(string $id)
    {
        return $this->useCase->get($id);
    }
}
