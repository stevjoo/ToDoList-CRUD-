/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["index.php",
    "add_task.php",
    "add_todo.php",
    "index.php",
    "profile.php",
    "edit_profile.php",
    "dashboard.php",
    "register.php",
    "delete_task.php",
    "delete_todo",
    "complete_task.php",
    "edit_task.php",
    "edit_todo.php",
    "forgot/change_password.php",
    "forgot/forgot_password.php",
    "forgot/confirmation.php",
    "src/todostyles.css"
  ],
  theme: {
    extend: {},
  },
  plugins: [require('daisyui')],
}

