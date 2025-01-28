# Task Tracker CLI

A sample solution for the [Task Tracker](https://roadmap.sh/projects/task-tracker) challenge from [roadmap.sh](https://roadmap.sh/). This is a Command-Line Interface (CLI) application to manage tasks with operations such as creating, updating, deleting, changing tasks' status, and viewing the list of tasks based on their status.

## Features

- **Create tasks** – Add a task with a description.
- **Update tasks** – Modify the description of an existing task.
- **Delete tasks** – Remove a task from the list.
- **Mark tasks** – Change the status of a task (todo, in-progress, done).
- **List tasks** – Filter tasks by their status (todo, in-progress, done).
- **Help command** – Display a list of available commands.

---

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Commands](#commands)
- [Contributing](#contributing)
- [License](#license)

---

## Installation

1. **Clone the repository:**

   Open your terminal and run the following command to clone the repository:

   ```bash
   git clone https://github.com/connecttoMAHDI/task-tracker-cli.git
   ```

2. **Navigate to the project directory:**

   After cloning, go to the project directory:

   ```bash
   cd task-tracker-cli
   ```

3. **Run the application:**

   The project is ready to run, simply use the following command to check available options:

   ```bash
   php task-cli.php --help
   ```

---

## Usage

To interact with the task tracker, run the `task-cli.php` script from the terminal and use the available commands.

### Available Commands

- **--help**: Display the help message and available commands.
- **add [description]**: Add a new task with a description.
- **update {id} [description]**: Update an existing task's description by providing the task ID and new description.
- **delete {id}**: Delete a task using its ID.
- **mark {id} [todo|in-progress|done]**: Mark a task with a new status.
- **list [status]**: List tasks, optionally filtered by their status (`todo`, `in-progress`, `done`).

---

## Commands Example

### Add a Task

```bash
php task-cli.php add "Do the laundry"
```

### Update a Task

```bash
php task-cli.php update 1 "Do the laundry and folding"
```

### Delete a Task

```bash
php task-cli.php delete 1
```

### Mark a Task

```bash
php task-cli.php mark 2 done
```

### List Tasks

```bash
php task-cli.php list todo
```
