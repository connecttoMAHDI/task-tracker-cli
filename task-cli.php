<?php

require_once __DIR__ . '/controllers/TaskManager.php';
require_once __DIR__ . '/enums/Commands.php';
require_once __DIR__ . '/constants.php';

use Controllers\TaskManager;
use Enums\Commands;

$command = $argv[1];
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
    case Commands::$add:
        $description = $args[0] ?? null;
        if ($description) {
            $taskManager->store($description);
        } else {
            echo "{description} is required and must be string.", N;
            echo "Usage: task-cli.php add \"description\"";
        }
        break;
    case Commands::$update:
        $id = $args[0] ?? null;
        $description = $args[1] ?? null;
        if ($id && $description) {
            $taskManager->update($id, $description);
        } else {
            echo "{id} & {description} are required.", N;
            echo "Usage: task-cli.php update {id} \"description\"";
        }
        break;
    case Commands::$delete:
        $id = $args[0] ?? null;
        if ($id) {
            $taskManager->delete($id);
        } else {
            echo "{id} is required.", N;
            echo "Usage: task-cli.php delete {id}";
        }
        break;
    case Commands::$mark:
        $id = $args[0] ?? null;
        $status = $args[1] ?? null;
        if ($status) {
            $taskManager->mark($id, $status);
        } else {
            echo "{id} & {status} are required.", N;
            echo "Usage: task-cli.php mark {id} [todo|done|in-progress]";
        }
        break;
    case Commands::$list:
        $status = $args[0] ?? null;
        $taskManager->index($status);
        break;
    case '--help':
        help();
        break;
    default:
        echo "Unknown command. Type '--help' for options.", N;
        echo "Usage: tasks-cli.php [command] [arguments]";
}
