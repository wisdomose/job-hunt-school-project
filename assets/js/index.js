window.addEventListener("load", () => {
  const menuToggle = document.querySelector("#menu-toggle");
  const closeMenu = document.querySelector("#close-menu");
  const menu = document.querySelector("#menu");
  menuToggle.addEventListener("click", () => {
    menu.classList.toggle("hidden");
    menu.classList.toggle("flex");
  });
  closeMenu.addEventListener("click", () => {
    menu.classList.add("hidden");
    menu.classList.remove("flex");
  });
});
