<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Users\UseCases\ManageUserUseCase;

class UsersController extends Controller
{
    public function __construct(private ManageUserUseCase $useCase) {}

    public function index(Request $request) { return $this->useCase->list($request->query()); }
    public function show(string $id) { return $this->useCase->get($id); }
    public function store(Request $request) { return $this->useCase->create($request->all()); }
    public function update(Request $request, string $id) { return $this->useCase->update($id, $request->all()); }
    public function destroy(string $id) { return $this->useCase->delete($id); }
}
