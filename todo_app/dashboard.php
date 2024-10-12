<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM todos WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>

<body class="bg-sky-100 min-h-screen">
    <div class="flex overflow-x-hidden">
        <div class="fixed top-0 left-0 w-44 lg:w-64 min-h-full hidden md:flex flex-col sm:hidden 
            bg-slate-600 text-slate-50">
            <div class="flex-grow overflow-hidden">
                <h1 class="p-8 text-2xl font-bold">TO-DO LIST</h1>
                <div class="grid grid-rows-4">
                    <form class="p-1 rounded-md flex" action="dashboard.php" method="GET">
                        <input class="max-w-32 lg:max-w-52 text-black input" type="text" name="search"
                            placeholder="Find Task..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button class="m-1 md:inline" type="submit">
                            <img class="invert max-w-6" src="img/find-svgrepo-com.svg" alt="find svg" />
                        </button>
                    </form>
                    <button class="inline p-2 text-left hover:bg-slate-700" onclick="openModal('profile_modal')">
                        <img class="inline-block invert max-w-6" src="img/profile-user-svgrepo-com.svg"
                            alt="profile svg" />
                        <span class="p-4 font-semibold">Profile</span>
                    </button>
                    <button class="inline p-2 text-left hover:bg-slate-700" onclick="openModal('add_todo_modal')">
                        <img class="inline-block invert max-w-6" src="img/new-svgrepo-com.svg" alt="new todo svg" />
                        <span class="p-4 font-semibold">New List</span>
                    </button>
                </div>
            </div>
            <a class="mt-auto p-4 font-bold bg-slate-600 hover:bg-slate-700" href="logout.php">
                <img class="inline-block ml-2 invert max-w-6" src="img/exit-svgrepo-com.svg" alt="logout svg" />
                <span class="p-4 font-semibold">Logout</span>
            </a>
        </div>

        <div class="min-w-full min-h-full m-auto block mb-16 md:ml-40 lg:ml-64 md:mb-0">
            <div class="px-2 bg-gradient-to-r from-blue-300 to-purple-200 text-slate-50">
                <h1 class="drop-shadow-xl text-center text-4xl font-bold p-2 md:text-left">Dashboard</h1>
            </div>

            <div class="w-full h-full md:w-4/5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 bg-sky-100">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="my-4 m-auto w-4/5 max-w-96 max-h-80 bg-slate-50 shadow-md shadow-slate-400 rounded-3xl overflow-hidden">
                        <h4 class="font-bold bg-gradient-to-tr from-blue-200 to-sky-200 p-4 text-xl text-slate-600">
                            <?php echo htmlspecialchars($row['title']); ?>
                            <a href="javascript:void(0)" onclick="openEditTodoModal(<?php echo $row['id']; ?>)" 
                               class="text-blue-500 hover:underline ml-2">Edit</a>
                        </h4>

                        <?php
                        $taskStmt = $conn->prepare("SELECT * FROM tasks WHERE todo_id = ?");
                        $taskStmt->bind_param("i", $row['id']);
                        $taskStmt->execute();
                        $tasks = $taskStmt->get_result();
                        ?>

                        <div class="max-h-40 overflow-y-scroll">
                            <ul class="max-h-64 overflow-x-scroll p-0 m-0">
                                <?php while ($task = $tasks->fetch_assoc()): ?>
                                    <li class="flex items-center <?php echo $task['completed'] ? 'line-through text-opacity-50' : ''; ?> p-1 border-b">
                                        <input class="checkbox checkbox-lg md:checkbox-md checkbox-primary" 
                                               type="checkbox" 
                                               <?php echo $task['completed'] ? 'checked' : ''; ?> 
                                               onchange="location.href='complete_task.php?task_id=<?php echo $task['id']; ?>&status=<?php echo $task['completed'] ? '0' : '1'; ?>'"/>
                                        <p class="ml-2"><?php echo htmlspecialchars($task['task']); ?></p>
                                        <a href="javascript:void(0)" 
                                           onclick="openEditTaskModal(<?php echo $task['id']; ?>)" 
                                           class="text-blue-500 hover:underline ml-auto">Edit</a>
                                        <a href="javascript:void(0)" 
                                           onclick="confirmDeleteTask(<?php echo $task['id']; ?>)" 
                                           class="text-red-500 hover:underline ml-2">Delete</a>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>

                        <div class="border-t-2">
                            <a href="add_task.php?todo_id=<?php echo $row['id']; ?>" 
                               class="m-2 float-left rounded-full hover:bg-purple-300">
                                <img class="m-4 max-w-8 max-h-8 inline-block" src="img/add-circle-svgrepo-com.svg" alt="add task svg" />
                            </a>
                            <a href="javascript:void(0)" 
                               onclick="confirmDeleteTodo(<?php echo $row['id']; ?>)" 
                               class="float-right m-2 rounded-full hover:bg-red-300">
                                <img class="m-4 max-w-8 max-h-8 inline-block" src="img/delete-svgrepo-com.svg" alt="delete list svg" />
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Edit Todo Modal -->
    <dialog id="edit_todo_modal" class="modal">
        <div class="bg-slate-50 modal-box">
            <iframe id="edit_todo_iframe" class="w-full h-96" frameborder="0"></iframe>
            <form method="dialog">
                <button class="btn btn-block" onclick="refreshPage()">Close</button>
            </form>
        </div>
    </dialog>

    <!-- Edit Task Modal -->
    <dialog id="edit_task_modal" class="modal">
        <div class="bg-slate-50 modal-box">
            <iframe id="edit_task_iframe" class="w-full h-96" frameborder="0"></iframe>
            <form method="dialog">
                <button class="btn btn-block" onclick="refreshPage()">Close</button>
            </form>
        </div>
    </dialog>

    <script>
        function openEditTodoModal(todoId) {
            document.getElementById('edit_todo_iframe').src = 'edit_todo.php?id=' + todoId;
            document.getElementById('edit_todo_modal').showModal();
        }

        function openEditTaskModal(taskId) {
            document.getElementById('edit_task_iframe').src = 'edit_task.php?id=' + taskId;
            document.getElementById('edit_task_modal').showModal();
        }

        function confirmDeleteTask(taskId) {
            if (confirm("Are you sure you want to delete this task?")) {
                location.href = 'delete_task.php?task_id=' + taskId;
            }
        }

        function confirmDeleteTodo(todoId) {
            if (confirm("Are you sure you want to delete this todo list?")) {
                location.href = 'delete_todo.php?id=' + todoId;
            }
        }

        function refreshPage() {
            location.reload();
        }
    </script>
</body>

</html>
