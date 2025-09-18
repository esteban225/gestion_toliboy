<?php


namespace App\Modules\Notifications\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;

class NotificationsRepository
{
    public function all(array $filters = []): array
    {
        $q = DB::table('notifications');
        return $q->orderBy('created_at','desc')->limit(100)->get()->toArray();
    }

    public function summary(): array
    {
        return [
            'unread' => DB::table('notifications')->where('read_at', null)->count(),
            'total' => DB::table('notifications')->count(),
        ];
    }
}
