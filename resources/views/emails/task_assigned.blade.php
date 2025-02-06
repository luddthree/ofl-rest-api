<!DOCTYPE html>
<html>
<head>
    <title>New Task Assigned</title>
</head>
<body>
    <h2>Hello, you have a new task assigned!</h2>
    <p>Task: {{ $task['title'] }}</p>
    <p>Deadline: {{ $task['deadline'] }}</p>
    <p>Please complete it on time.</p>
</body>
</html>
