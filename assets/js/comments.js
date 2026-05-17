document.addEventListener("DOMContentLoaded", function () {
    let boardContainer = document.querySelector(".column-container");

    if (boardContainer) {
        boardContainer.addEventListener("click", function (event) {

            if (event.target.classList.contains("move-btn")) {
                return; 
            }
            let taskCard = event.target.closest(".task-card");

            if (taskCard) {
                let taskId = taskCard.getAttribute("task-id");

                if (taskId) {
                    window.location.href = "../views/taskDetail.php?task_id=" + taskId;
                }
            }
        });
    }
});

