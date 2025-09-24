<?php

namespace App\Modules\Forms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Forms\UseCases\ManageFormUseCase;
use Illuminate\Http\Request;

class FormsController extends Controller
{
    public function __construct(private ManageFormUseCase $useCase) {}

    public function index(Request $request)
    {
        return $this->useCase->list($request->query());
    }

    public function show(string $id)
    {
        return $this->useCase->get($id);
    }

    public function store(Request $request)
    {
        return $this->useCase->create($request->all());
    }

    public function update(Request $request, string $id)
    {
        return $this->useCase->update($id, $request->all());
    }

    public function destroy(string $id)
    {
        return $this->useCase->delete($id);
    }
}
