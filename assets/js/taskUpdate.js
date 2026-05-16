function UpdateTaskDatabase(card, taskId, newStatus) {
    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let data = JSON.parse(this.responseText);

            if (data.ok === true) {
                let targetColumn = document.getElementById(data.new_status);
                targetColumn.appendChild(card);

                let oldButtons = card.querySelectorAll('.move-btn');
                oldButtons.forEach(btn => btn.remove());

                if (data.new_status === 'todo') {
                    card.innerHTML += "<button class='move-btn' data-direction='right'>&rarr;</button>";
                }
                else if (data.new_status === 'in-progress') {
                    card.innerHTML += "<button class='move-btn' data-direction='left'>&larr;</button> <button class='move-btn' data-direction='right'>&rarr;</button>";
                }
                else if (data.new_status === 'done') {
                    card.innerHTML += "<button class='move-btn' data-direction='left'>&larr;</button>";
                }

                
                card.classList.remove('overdue'); 
                if (data.new_status !== 'done') {
                    let dueDateString = card.getAttribute('due-date');
                    if (dueDateString) {
                        let today = new Date();
                        today.setHours(0, 0, 0, 0);
                        let dueDate = new Date(dueDateString);
                        
                        if (dueDate < today) {
                            card.classList.add('overdue');
                        }
                    }
                }
            }
        }
    };

    xhttp.open("POST", "../controllers/taskUpdateController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("task_id=" + taskId + "&status=" + newStatus);
}

document.addEventListener("DOMContentLoaded", function () {
    let today = new Date();
    today.setHours(0, 0, 0, 0);

    let cards = document.getElementsByClassName("task-card");

    for (let i = 0; i < cards.length; i++) {
        let card = cards[i];
        let columnId = card.parentElement.id;
        let dueDateString = card.getAttribute("due-date");

        if (columnId === "done" || !dueDateString) {
            continue;
        }

        let dueDate = new Date(dueDateString);
        if (dueDate < today) {
            card.classList.add("overdue");
        }
    }

    let board = document.querySelector(".column-container");
    board.addEventListener("click", function (event) {
        if (event.target.classList.contains("move-btn")) {
            let button = event.target;
            let card = button.closest(".task-card");
            let taskId = card.getAttribute("task-id");
            let direction = button.getAttribute("data-direction");
            let currentColumn = card.parentElement.id;

            let newStatus = "";
            if (direction === "right") {
                if (currentColumn === "todo") {
                    newStatus = "in-progress";
                } else if (currentColumn === "in-progress") {
                    newStatus = "done";
                }
            } else if (direction === "left") {
                if (currentColumn === "done") {
                    newStatus = "in-progress";
                } else if (currentColumn === "in-progress") {
                    newStatus = "todo";
                }
            }

            if (newStatus !== "") {
                UpdateTaskDatabase(card, taskId, newStatus);
            }
        }
    });
});