<?php

namespace App\Modules\Notifications\UseCases;

use App\Modules\Notifications\Infrastructure\Repositories\NotificationsRepository;

class NotificationsUseCase
{
    public function __construct(private NotificationsRepository $repo) {}

    public function list(array $filters) { return response()->json($this->repo->all($filters)); }
    public function summary() { return response()->json($this->repo->summary()); }
}
