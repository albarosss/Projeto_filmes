document.addEventListener("DOMContentLoaded", function () {
    const stars = document.querySelectorAll(".star");
    const hiddenRating = document.getElementById("avaliacao");

    stars.forEach((star) => {
        star.addEventListener("click", () => {
            const rating = star.getAttribute("data-rating");
            hiddenRating.value = rating;

            stars.forEach((s) => {
                const sRating = s.getAttribute("data-rating");
                s.classList.remove("active");
                if (sRating <= rating) {
                    s.classList.add("active");
                }
            });
        });
    });
});
