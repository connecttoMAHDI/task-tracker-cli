<?php

namespace Models;

require_once __DIR__ . '/../enums/TaskStatus.php';

use Enums\TaskStatus;

class Task
{
    private static $_path = __DIR__ . '/..' . '/last_id.txt';
    private static $_id = 0;
    public $id;
    public $description;
    public $status;
    public $createdAt;
    public $updatedAt;

    public function __construct(
        string $description,
        string $status,
        string $createdAt,
        string $updatedAt
    ) {
        self::loadLastId();
        $this->id = ++self::$_id;
        $this->description = $description;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        self::saveLastId();
    }

    private static function loadLastId()
    {
        if (file_exists(self::$_path)) {
            self::$_id = (int) file_get_contents(self::$_path);
        }
    }

    private static function saveLastId()
    {
        file_put_contents(self::$_path, self::$_id);
    }

    public static function createTask(string $description): Task
    {
        $now = date('Y-m-d H:i:s');

        $task = new Task(
            $description,
            TaskStatus::TODO->value,
            $now,
            $now
        );

        return $task;
    }

    public static function updateTask(array $task, ?string $desc = null, ?string $status = null): array
    {
        $now = date('Y-m-d H:i:s');

        $task['description'] = $desc ?? $task['description'];
        $task['status'] = $status ?? $task['status'];
        $task['updatedAt'] = $now;

        return $task;
    }
}
