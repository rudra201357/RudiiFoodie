document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("subscribe-form");
  const emailInput = document.getElementById("email");
  const message = document.getElementById("message");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // stop reload

    const email = emailInput.value.trim();

    if (email === "") {
      alert("Please enter an email.");
      return;
    }

    fetch("subscribe.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "email=" + encodeURIComponent(email),
    })
      .then((response) => response.text())
      .then((data) => {
        console.log("Response:", data);
        message.innerText = data;
        emailInput.value = ""; // clear input
      })
      .catch((error) => {
        console.error("Error:", error);
        message.innerText = "Something went wrong.";
      });
  });
});