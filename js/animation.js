document.addEventListener('DOMContentLoaded', function () {
    const counters = document.querySelectorAll('.counter');

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 1000; // 2 seconds
        const step = Math.floor(target / (duration / 10));
        let current = 0;

        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = current;
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };

        updateCounter();
    });
});