const urlParams = new URLSearchParams(window.location.search);
document.getElementById("token").value = urlParams.get("token");
