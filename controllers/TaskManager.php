<?php

namespace Controllers;

require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../enums/TaskStatus.php';

use Models\Task;
use Enums\TaskStatus;

class TaskManager
{
    private $tasks_path = __DIR__ . '/../tasks.json';

    //@desc show a list tasks - filter by status
    public function index(string|null $status): void
    {
        $filter = null;
        if ($status && in_array(
            $status,
            array_column(
                TaskStatus::cases(),
                'value'
            )
        )) $filter = $status;

        $tasks = $this->loadTasks(true);

        if ($filter) {
            $tasks = array_values(
                array_filter($tasks, function ($task) use ($filter) {
                    return $task['status'] === $filter;
                })
            );
        }

        print_r($tasks);
        exit;
    }

    //@desc creates a new tasks
    public function store(string $description)
    {
        // Create task
        $task = Task::createTask($description);

        // Load all tasks
        if (file_exists($this->tasks_path)) {
            $tasks = $this->loadTasks(true);
        } else {
            $tasks = [];
        }

        // Append the new task
        $tasks[] = (array) $task;

        // Save tasks
        $this->saveTasks($tasks);

        echo "Task with Id: {$task->id} created.";
        exit;
    }

    //@desc changes the description of a task
    public function update(int $id, string $description)
    {
        $tasks = $this->loadTasks(true);
        $updated = false;

        foreach ($tasks as &$t) {
            if ($t['id'] === $id) {
                $updated = true;
                $t = Task::updateTask($t, $description);
                break;
            }
        }

        if ($updated === true) {
            $this->saveTasks($tasks);
            echo "Task with ID: $id updated.";
            exit;
        } else {
            echo "Task with ID: $id not found.";
            exit;
        }
    }

    //@desc changes the status of a task
    public function mark(int $id, string $status)
    {
        if (!in_array(
            $status,
            array_column(TaskStatus::cases(), 'value')
        )) {
            echo "{status} must be one of [todo, done, in-progress]";
            exit;
        }

        $tasks = $this->loadTasks(true);
        $updated = false;

        foreach ($tasks as &$t) {
            if ($t['id'] === $id) {
                $updated = true;
                $t = Task::updateTask(task: $t, status: $status);
                break;
            }
        }

        if ($updated === true) {
            $this->saveTasks($tasks);
            echo "Task with ID: $id updated.";
            exit;
        } else {
            echo "Task with ID: $id not found.";
            exit;
        }
    }

    //@desc deletes a task if exist
    public function delete(int $id)
    {
        $tasks = $this->loadTasks(true);
        $filteredTasks = array_filter($tasks, fn($t) => $t['id'] !== $id);

        if (count($tasks) === count($filteredTasks)) {
            echo "Task with ID: $id not found.";
            exit;
        }

        $this->saveTasks(
            array_values($filteredTasks)
        );
        echo "Task with ID: $id deleted.";
        exit;
    }

    //@desc retrieve all tasks from tasks.json
    private function loadTasks(bool $assoc = false): array
    {
        if (file_exists($this->tasks_path)) {
            $tasksRawFile = file_get_contents($this->tasks_path);
        } else {
            echo "No Tasks exist! try adding one by running:", N;
            echo "tasks-cli.php add \"description\"";
            exit;
        }

        $tasks = json_decode($tasksRawFile, $assoc);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Failed to decode tasks.json: " . json_last_error_msg();
            exit;
        }

        return $tasks;
    }

    //@desc save tasks to tasks.json
    private function saveTasks(array $tasks): void
    {
        $tasks = json_encode($tasks, JSON_PRETTY_PRINT);

        if ($tasks === false) {
            echo "Failed to encode tasks to JSON: " . json_last_error_msg();
            exit;
        }

        $res = file_put_contents($this->tasks_path, $tasks);

        if ($res === false) {
            echo "Failed to write tasks to tasks.json.";
            exit;
        }
    }
}
