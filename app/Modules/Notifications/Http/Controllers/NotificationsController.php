<?php

namespace App\Modules\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Notifications\UseCases\NotificationsUseCase;

class NotificationsController extends Controller
{
    public function __construct(private NotificationsUseCase $useCase) {}

    public function index(Request $request) { return $this->useCase->list($request->query()); }
    public function summary() { return $this->useCase->summary(); }
}
