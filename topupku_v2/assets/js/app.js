(function () {
    const body = document.body;
    // TopupKu is intentionally dark-only; ignore any old light-mode preference.
    body.classList.add("dark");
    try { localStorage.setItem("topupku-theme", "dark"); } catch (e) {}

    document.querySelectorAll("[data-confirm]").forEach(function (element) {
        element.addEventListener("click", function (event) {
            if (!confirm(element.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll("[data-toast]").forEach(function (toast) {
        setTimeout(function () {
            toast.classList.add("hide");
        }, 3500);
    });
})();
