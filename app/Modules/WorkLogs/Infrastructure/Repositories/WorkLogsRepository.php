<?php

namespace App\Modules\WorkLogs\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;

class WorkLogsRepository
{
    public function all(array $filters): array
    {
        $q = DB::table('work_logs');
        if (! empty($filters['user_id'])) {
            $q->where('user_id', $filters['user_id']);
        }
        if (! empty($filters['date'])) {
            $q->where('date', $filters['date']);
        }

        return $q->get()->toArray();
    }

    public function find(string $id)
    {
        return DB::table('work_logs')->where('id', $id)->first();
    }

    public function create(array $data): int
    {
        // expected keys: user_id, date, start_time, end_time, batch_id, task_description
        $payload = array_merge($data, ['created_at' => now(), 'updated_at' => now()]);

        return DB::table('work_logs')->insertGetId($payload);
    }
}
