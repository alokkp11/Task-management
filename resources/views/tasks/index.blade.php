<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Task Manager</title>
</head>
<body>
    <h1>Task Manager</h1>
    <input type="text" id="task-input" placeholder="Enter a new task">
    <button onclick="addTask()">Add Task</button>
    <button onclick="showAllTasks()">Show All Tasks</button>
    <ul id="task-list">
        @foreach ($tasks as $task)
            <li data-id="{{ $task->id }}">
                <input type="checkbox" onchange="toggleTask({{ $task->id }}, this.checked)" {{ $task->completed ? 'checked' : '' }}>
                {{ $task->title }}
                <button onclick="deleteTask({{ $task->id }})">Delete</button>
            </li>
        @endforeach
    </ul>

    <script>
        function addTask() {
            let title = document.getElementById('task-input').value;

            fetch('{{ route('tasks.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ title: title })
            })
            .then(response => response.json())
            .then(task => {
                let taskList = document.getElementById('task-list');
                let listItem = document.createElement('li');
                listItem.setAttribute('data-id', task.id);
                listItem.innerHTML = `
                    <input type="checkbox" onchange="toggleTask(${task.id}, this.checked)">
                    ${task.title}
                    <button onclick="deleteTask(${task.id})">Delete</button>
                `;
                taskList.appendChild(listItem);
                document.getElementById('task-input').value = '';
            });
        }

        function toggleTask(id, completed) {
            fetch(`/tasks/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ completed: completed })
            });
        }

        function deleteTask(id) {
            if (confirm('Are you sure you want to delete this task?')) {
                fetch(`/tasks/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(() => {
                    let taskList = document.getElementById('task-list');
                    let listItem = taskList.querySelector(`li[data-id='${id}']`);
                    taskList.removeChild(listItem);
                });
            }
        }

        function showAllTasks() {
            fetch('/tasks/all', {
                method: 'GET'
            })
            .then(response => response.json())
            .then(tasks => {
                let taskList = document.getElementById('task-list');
                taskList.innerHTML = '';
                tasks.forEach(task => {
                    let listItem = document.createElement('li');
                    listItem.setAttribute('data-id', task.id);
                    listItem.innerHTML = `
                        <input type="checkbox" onchange="toggleTask(${task.id}, this.checked)" ${task.completed ? 'checked' : ''}>
                        ${task.title}
                        <button onclick="deleteTask(${task.id})">Delete</button>
                    `;
                    taskList.appendChild(listItem);
                });
            });
        }
    </script>
</body>
</html>