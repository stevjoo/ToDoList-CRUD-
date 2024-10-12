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
    "src/todostyles.css"
  ],
  theme: {
    extend: {},
  },
  plugins: [require('daisyui')],
}

