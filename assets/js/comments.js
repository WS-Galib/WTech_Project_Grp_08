document.addEventListener("DOMContentLoaded", function () {
    let boardContainer = document.querySelector(".column-container");

    if (boardContainer) {
        boardContainer.addEventListener("click", function (event) {
                        if (event.target.classList.contains("move-btn")) {
                return; // Let Student 3's code handle the column movement
            }

