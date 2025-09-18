<?php

namespace App\Modules\Roles\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Roles\UseCases\ManageRoleUseCase;

class RolesController extends Controller
{
    public function __construct(private ManageRoleUseCase $useCase) {}

    public function index(Request $request) { return $this->useCase->list($request->query()); }
    public function store(Request $request) { return $this->useCase->create($request->all()); }
    public function update(Request $request, string $id) { return $this->useCase->update($id, $request->all()); }
}
