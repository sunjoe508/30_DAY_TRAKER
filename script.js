const form = document.getElementById("progressForm");
const logList = document.getElementById("logEntries");

let logs = JSON.parse(localStorage.getItem("logs")) || [];

function renderLogs() {
  logList.innerHTML = "";
  logs.forEach((log, i) => {
    const li = document.createElement("li");
    li.innerHTML = `
      <strong>${log.date}</strong> - ${log.task}<br>
      <em>${log.reflection}</em>
    `;
    logList.appendChild(li);
  });
}

form.addEventListener("submit", (e) => {
  e.preventDefault();
  const date = document.getElementById("date").value;
  const task = document.getElementById("task").value;
  const reflection = document.getElementById("reflection").value;

  const newLog = { date, task, reflection };
  logs.push(newLog);
  localStorage.setItem("logs", JSON.stringify(logs));
  renderLogs();
  form.reset();
});

renderLogs();

