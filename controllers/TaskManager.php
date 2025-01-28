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

    /**
     * Displays a list of tasks, optionally filtered by status.
     *
     * @param string|null $status The status to filter tasks by (e.g., 'todo', 'done', 'in-progress').
     *                            If null, all tasks are shown.
     * @return void
     */
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

        $this->formatOutput($tasks);
    }

    /**
     * Creates a new task with the provided description.
     *
     * @param string $description The description of the task to create.
     * @return void
     */
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

    /**
     * Updates the description of an existing task by ID.
     *
     * @param int $id The ID of the task to update.
     * @param string $description The new description for the task.
     * @return void
     */
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

    /**
     * Updates the status of an existing task by ID.
     *
     * @param int $id The ID of the task to update.
     * @param string $status The new status of the task (e.g., 'todo', 'done', 'in-progress').
     * @return void
     */
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

    /**
     * Deletes a task by ID if it exists.
     *
     * @param int $id The ID of the task to delete.
     * @return void
     */
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

    /**
     * Loads tasks from the tasks.json file.
     *
     * @param bool $assoc Whether to return tasks as an associative array (default is false).
     * @return array The list of tasks loaded from the file.
     */
    private function loadTasks(bool $assoc = false): array
    {
        if (file_exists($this->tasks_path)) {
            $tasksRawFile = file_get_contents($this->tasks_path);
        } else {
            echo "No Tasks exist! try adding one by typing:", N;
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

    /**
     * Saves tasks to the tasks.json file.
     *
     * @param array $tasks The list of tasks to save to the file.
     * @return void
     */
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

    /**
     * Formats and prints the tasks as a table.
     *
     * @param array $tasks The list of tasks to format and display.
     * @return void
     */
    private function formatOutput(array $tasks)
    {
        if (empty($tasks)) {
            echo "No tasks to display.\n";
            exit;
        }

        // Determine the maximum width for each column
        $maxWidths = [
            'id' => strlen('ID'),
            'description' => strlen('Description'),
            'status' => strlen('Status'),
            'createdAt' => strlen('Created At'),
            'updatedAt' => strlen('Updated At'),
        ];

        foreach ($tasks as $task) {
            $maxWidths['id'] = max($maxWidths['id'], strlen((string)$task['id']));
            $maxWidths['description'] = max($maxWidths['description'], strlen($task['description']));
            $maxWidths['status'] = max($maxWidths['status'], strlen($task['status']));
            $maxWidths['createdAt'] = max($maxWidths['createdAt'], strlen($task['createdAt']));
            $maxWidths['updatedAt'] = max($maxWidths['updatedAt'], strlen($task['updatedAt']));
        }

        // Create the format string
        $format = "| %-" . $maxWidths['id'] . "s | %-" . $maxWidths['description'] . "s | %-" . $maxWidths['status'] . "s | %-" . $maxWidths['createdAt'] . "s | %-" . $maxWidths['updatedAt'] . "s |\n";
        $separator = '+' . str_repeat('-', $maxWidths['id'] + 2) . '+' . str_repeat('-', $maxWidths['description'] + 2) . '+' . str_repeat('-', $maxWidths['status'] + 2) . '+' . str_repeat('-', $maxWidths['createdAt'] + 2) . '+' . str_repeat('-', $maxWidths['updatedAt'] + 2) . "+\n";

        // Print the table
        echo $separator;
        printf($format, 'ID', 'Description', 'Status', 'Created At', 'Updated At');
        echo $separator;
        foreach ($tasks as $task) {
            printf($format, $task['id'], $task['description'], $task['status'], $task['createdAt'], $task['updatedAt']);
        }
        echo $separator;
    }
}
