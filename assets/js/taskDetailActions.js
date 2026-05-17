document.addEventListener("DOMContentLoaded", function () {
    const commentForm = document.getElementById("ajax-comment-form");

    if (commentForm) {
        commentForm.addEventListener("submit", function (e) {
            e.preventDefault(); 

            let taskId = document.getElementById("form-task-id").value;
            let projectId = document.getElementById("form-project-id").value;
            let bodyInput = document.getElementById("comment-body-input");
            let bodyText = bodyInput.value.trim();
            let errorOutput = document.getElementById("form-error-output");

            if (bodyText === "") return;
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../controllers/commentCreateController.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    try {
                        let response = JSON.parse(this.responseText);

                        if (response.ok === true) {
                            bodyInput.value = "";
                            errorOutput.style.display = "none";

                            let noCommentsMsg = document.getElementById("no-comments-msg");
                            if (noCommentsMsg) noCommentsMsg.remove();

                            let newComment = document.createElement("div");
                            newComment.className = "comment-box";
                            newComment.id = "comment-" + response.comment.id;
                            
                            newComment.innerHTML = `
                                <div class='comment-meta'><b>${response.comment.author_name}</b> &bull; ${response.comment.created_at}</div>
                                <div class='comment-body'>${response.comment.body}</div>
                                <a class='delete-link' onclick='deleteComment(${response.comment.id})'>Delete</a>
                            `;
                            document.getElementById("comments-thread").appendChild(newComment);
                        } 
                        else {
                            errorOutput.innerText = response.error || "An error occurred.";
                            errorOutput.style.display = "block";
                        }
                    } 
                    catch (err) {
                        errorOutput.innerText = "Server response error.";
                        errorOutput.style.display = "block";
                    }
                }
            };
            xhr.send("task_id=" + encodeURIComponent(taskId) + 
                     "&project_id=" + encodeURIComponent(projectId) + 
                     "&body=" + encodeURIComponent(bodyText));
        });
    }
});
