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
});
