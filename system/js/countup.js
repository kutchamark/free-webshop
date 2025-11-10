const counters = document.querySelectorAll("[data-count-to]");

counters.forEach((counter) => {
    const target = Number(counter.getAttribute("data-count-to")) || 0;
    const duration = 1200;
    const startTime = performance.now();

    const updateCounter = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const value = Math.floor(progress * target);
        counter.textContent = value.toLocaleString();

        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    };

    requestAnimationFrame(updateCounter);
});
