document.addEventListener("DOMContentLoaded", function () {
    const userFilter = document.getElementById("user-filter");
    const projectId = document.getElementById("current-project-id").value;
    const activityList = document.getElementById("activity-list");

    function loadActivities(userId) {
        activityList.innerHTML = "<p style='color: #94a3b8; font-style: italic;'>Loading feed...</p>";
        let xhr = new XMLHttpRequest();
        xhr.open("GET", `../controllers/activityController.php?project_id=${projectId}&user_id=${userId}`, true);
        
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                try {
                    let response = JSON.parse(this.responseText);
                    
                    if (response.ok) {
                        activityList.innerHTML = "";
                        
                        if (response.data.length === 0) {
                            activityList.innerHTML = "<p style='color: #94a3b8; text-align: center; padding: 30px 0;'>No activity recorded yet.</p>";
                            return;
                        }
                        response.data.forEach(log => {
                            let item = document.createElement("div");
                            item.className = "activity-item";
                            item.innerHTML = `
                                <div class="avatar">${log.initials}</div>
                                <div class="activity-content">
                                    <div class="activity-text">${log.action_text}</div>
                                    <div class="activity-time">${log.time_ago}</div>
                                </div>
                            `;
                            activityList.appendChild(item);
                        });
                    } 
                    else {
                        activityList.innerHTML = `<p style="color: #ef4444;">Error: ${response.error}</p>`;
                    }
                } 
                catch (e) {
                    activityList.innerHTML = `<p style="color: #ef4444;">Failed to read server response.</p>`;
                }
            }
        };
        xhr.send();
    }
    loadActivities("all");
    if (userFilter) {
        userFilter.addEventListener("change", function () {
            loadActivities(this.value);
        });
    }
});