<?php
session_start();
$_SESSION["user_id"] = 1; 

echo "<h3>Logged in successfully as Test User (ID: 1)!</h3>";
echo "<p><a href='../views/taskBoard.php?project_id=1'>Click here to go to Task Board</a></p>";