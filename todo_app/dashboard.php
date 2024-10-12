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
        <div class="fixed top-0 left-0 w-44 lg:w-64 min-h-full hidden
            md:flex flex-col
            sm:hidden 
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
            <div class="px-2 bg-gradient-to-r from-blue-300 to-purple-200  text-slate-50">
                <h1 class="drop-shadow-xl text-center text-4xl font-bold p-2 md:text-left">Dashboard</h1>
            </div>
            <div class="flex justify-center md:justify-normal md:ml-4 bg-opacity-0">
                <div class="p-6  join join-horizontal">
                    <span class="font-semibold btn join-item text-slate-600">Filter:</span>
                    <a class="btn join-item bg-slate-50 text-slate-600" href="dashboard.php?status=all">All</a>
                    <a class="btn join-item bg-slate-50 text-slate-600" href="dashboard.php?status=completed">Done</a>
                    <a class="btn join-item bg-slate-50 text-slate-600"
                        href="dashboard.php?status=incomplete">Incomplete</a>
                </div>
            </div>
            <?php
            if ($searchQuery) {
                $searchStmt = $conn->prepare("SELECT * FROM tasks WHERE task LIKE ? AND todo_id IN (SELECT id FROM todos WHERE user_id = ?)");
                $likeQuery = "%" . $searchQuery . "%";
                $searchStmt->bind_param("si", $likeQuery, $userId);
                $searchStmt->execute();
                $tasks = $searchStmt->get_result();

                echo '<div class="bg-purple-200 p-6 mt-4 rounded-lg shadow-md w-1/2 m-auto md:m-0">';
                echo "<h2 class='text-lg font-bold mb-4'>You Searched for: <span class='text-blue-600'>" . htmlspecialchars($searchQuery) . "</span></h2>";

                while ($task = $tasks->fetch_assoc()) {
                    echo '<div class="p-8 mb-4 bg-slate-50 rounded-lg shadow transition-all duration-200">';
                    echo '<p class="text-lg font-medium">' . htmlspecialchars($task['task']) . '</p>';

                    echo '<div class="mt-2 border-t pt-2">';

                    if ($task['completed']) {
                        echo '<span class="inline-block bg-green-100 text-green-800 text-sm px-2 py-1 rounded-full">Completed</span>';
                    } else {
                        echo '<span class="inline-block bg-red-100 text-red-800 text-sm px-2 py-1 rounded-full">Incomplete</span>';
                    }
                    echo ' <a class="btn btn-ghost"' . "href='complete_task.php?task_id=" . $task['id'] . "&status=" . ($task['completed'] ? "0" : "1") . "'>";
                    echo $task['completed'] ? "Mark as Incomplete" : "Mark as Done";
                    echo "</a></p>";
                    echo '</div></div>';
                }

                echo '</div>';
            } else {
                echo '<div class="w-full h-full md:w-4/5
                grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 bg-sky-100">';
                while ($row = $result->fetch_assoc()) {
                    $todoId = $row['id'];
                    echo '<div class="my-4 m-auto w-4/5 max-w-96 max-h-80
                        bg-slate-50 shadow-md shadow-slate-400 rounded-3xl overflow-hidden">
                        <h4 class="font-bold bg-gradient-to-tr from-blue-200 to-sky-200 p-4 text-xl text-slate-600">' . htmlspecialchars($row['title']) . "</h4>";

                    $filterQuery = "SELECT * FROM tasks WHERE todo_id = ?";
                    if ($statusFilter == 'completed') {
                        $filterQuery .= " AND completed = 1";
                    } elseif ($statusFilter == 'incomplete') {
                        $filterQuery .= " AND completed = 0";
                    }

                    $taskStmt = $conn->prepare($filterQuery);
                    $taskStmt->bind_param("i", $todoId);
                    $taskStmt->execute();
                    $tasks = $taskStmt->get_result();

                    echo '<div class="max-h-40 overflow-y-scroll">';
                    echo '<ul class"max-h-64 overflow-x-scroll p-0 m-0">';
                    while ($task = $tasks->fetch_assoc()) {

                        // cross out if complete
                        echo $task['completed'] ? '<li class="flex items-center line-through text-slate-600 text-opacity-50 p-1 break-words w-full border-b"> ' : '<li class="flex items-center p-1 break-words w-full border-b"> ';

                        // Eli : checkboxnya kalo diclick reset/reload pagenya.
                        // kalau udah scroll kebawah bakal back to top
                        // idk how to fix this 
                        echo '<input class="checkbox checkbox-lg md:checkbox-md checkbox-primary" type="checkbox" ' . ($task['completed'] ? 'checked' : '') .
                            ' onchange="location.href=\'complete_task.php?task_id=' . $task['id'] . '&status=' . ($task['completed'] ? '0' : '1') . '\'"/>';
                        echo '<p class="ml-2 py-1">' . htmlspecialchars($task['task']) . '</p>';

                        // echo " <a href='complete_task.php?task_id=" . $task['id'] . "&status=" . ($task['completed'] ? "0" : "1") . "'>";
                        // echo $task['completed'] ? "Tandai Belum Selesai" : "Tandai Selesai";
                        // echo "</a></p>";
                        echo '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                    echo '<div class="border-t-2">';

                    echo "<a href='add_task.php?todo_id=" . $todoId . "' class='m-2 float-left rounded-full hover:bg-purple-300'>
                            <img class='m-4 max-w-8 max-h-8 inline-block' src='img/add-circle-svgrepo-com.svg' alt='add task svg' />
                        </a>".
                        "<a href='delete_todo.php?id=" . $todoId . "' class='float-right m-2 rounded-full hover:bg-red-300'>" .
                        "<img class='m-4 max-w-8 max-h-8 inline-block' src='img/delete-svgrepo-com.svg' alt='delete list svg' />" .
                        "</a></div></div>";
                    // echo "<a href='add_task.php?todo_id=" . $todoId . "'>Add Task</a> | <a href='delete_todo.php?id=" . $todoId . "'>Delete</a></div></div>";
                }
            }
            echo '</div>';
            ?>
        </div>
    </div>
    <div class="grid grid-cols-4 w-full p-2  fixed bottom-0 h-16 bg-slate-600 text-slate-50 md:hidden">
        <div class="dropdown dropdown-top">
            <div tabindex="0" role="button" class="block text-slate-50 m-0">
                <img class="invert max-w-10 max-h-10" src="img/search-file-svgrepo-com.svg" alt="search svg" />
            </div>
            <form tabindex="0" class="dropdown-content mx-auto mb-4 w-fit p-4 rounded-md 
                flex items-center bg-slate-600 bg-opacity-80" action="dashboard.php" method="GET">
                <input class="inline-block w-80 rounded-md p-4 text-black md:w-fit" type="text" name="search"
                    placeholder="Find Task..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="inline-block p-1 m-1 font-semibold rounded-md hover:bg-slate-700" type="submit">
                    <img class="invert max-w-10 max-h-10" src="img/find-svgrepo-com.svg" alt="find svg" />
                </button>
            </form>
        </div>
        <button onclick="openModal('profile_modal')">
            <img class="invert max-w-10 max-h-10" src="img/profile-user-svgrepo-com.svg" alt="profile svg" />
        </button>
        <button onclick="openModal('add_todo_modal')">
            <img class="invert max-w-10 max-h-10" src="img/new-svgrepo-com.svg" alt="new todo svg" />
        </button>
        <a href="logout.php">
            <img class="ml-2 invert max-w-10 max-h-10" src="img/exit-svgrepo-com.svg" alt="logout svg" />
        </a>
    </div>

    <dialog id="add_todo_modal" class="modal">
        <div class="bg-slate-50 modal-box">
            <div class="modal-action">
                <iframe src="add_todo.php" class="w-full h-96" frameborder="0"></iframe>
            </div>
            <form method="dialog">
                <button class="btn btn-block" onclick="refreshPage()">Close Window</button>
            </form>
        </div>
    </dialog>

    <dialog id="profile_modal" class="modal">
        <div class="bg-slate-50 modal-box">
            <div class="modal-action">
                <iframe src="profile.php" class="w-full h-96" frameborder="0"></iframe>
            </div>
            <form method="dialog">
                <button class="btn btn-block" onclick="refreshPage()">Close Window</button>
            </form>
        </div>
    </dialog>
    <script>
        function currentTodo(todoID) {

        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.showModal();
            } else {
                console.error(`Modal with ID "${modalId}" not found.`);
            }
        }
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.close();
            } else {
                console.error(`Modal with ID "${modalId}" not found.`);
            }
        }

        // for modal close window button to refresh page
        function refreshPage() {
            location.reload(); 
        }
    </script>
</body>

</html>