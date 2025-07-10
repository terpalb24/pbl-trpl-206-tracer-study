import "./bootstrap";

// Hamburgers NPM
const menuButton = document.getElementById("menu-button");

menuButton.addEventListener("click", () => {
    if (menuButton.classList.contains("is-active")) {
        menuButton.classList.remove("is-active");
    } else {
        menuButton.classList.add("is-active");
    }
});
