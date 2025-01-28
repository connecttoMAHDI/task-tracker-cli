<?php

require_once __DIR__ . '/controllers/TaskManager.php';
require_once __DIR__ . '/enums/Commands.php';
require_once __DIR__ . '/constants.php';

use Controllers\TaskManager;
use Enums\Commands;

$command = $argv[1] ?? null;
$args = array_slice($argv, 2);
$taskManager = new TaskManager();

function help()
{
    echo N;
    echo "Here is the list of commands:", N;
    echo "- add [description]", N;
    echo "- update {id} [description]", N;
    echo "- delete {id}", N;
    echo N;
    echo "Manage task's status:", N;
    echo "- mark in-progress {id}", N;
    echo "- mark done {id}", N;
    echo "- mark todo {id}", N;
    echo N;
    echo "Retrieve all tasks with filter:", N;
    echo "- list", N;
    echo "- list done", N;
    echo "- list todo", N;
    echo "- list in-progress", N;
    return;
}

switch ($command) {
    case Commands::ADD:
        $description = $args[0] ?? null;
        if ($description) {
            $taskManager->store($description);
        } else {
            echo "Please provide a [description] for the task.", N;
            echo "Usage: task-cli.php add \"description\"", N;
        }
        break;
    case Commands::UPDATE:
        $id = $args[0] ?? null;
        $description = $args[1] ?? null;
        if ($id && $description) {
            $taskManager->update($id, $description);
        } else {
            echo "Please specify the {id} and the new [description] for the task.", N;
            echo "Usage: task-cli.php update {id} \"description\"", N;
        }
        break;
    case Commands::DELETE:
        $id = $args[0] ?? null;
        if ($id) {
            $taskManager->delete($id);
        } else {
            echo "Please specify the {id} of the task to delete.", N;
            echo "Usage: task-cli.php delete {id}", N;
        }
        break;
    case Commands::MARK:
        $id = $args[0] ?? null;
        $status = $args[1] ?? null;
        if ($status) {
            $taskManager->mark($id, $status);
        } else {
            echo "Please specify the {id} and the {status} for the task.", N;
            echo "Usage: task-cli.php mark {id} [todo|done|in-progress]", N;
        }
        break;
    case Commands::LIST:
        $status = $args[0] ?? null;
        $taskManager->index($status);
        break;
    case '--help':
        help();
        break;
    default:
        echo "Unknown command. Type '--help' for options.", N;
        echo "Usage: tasks-cli.php {command} [arguments]";
}
