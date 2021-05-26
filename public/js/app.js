const handleAccordions = () => {
    const accordions = document.querySelectorAll('.accordion');

    accordions.forEach((accordion) => {
        accordion.addEventListener('click', () => {
            const el = event.target;
            const panel = document.querySelector('[data-panel="' + el.dataset.target + '"]');
            panel.classList.toggle('open');
            el.classList.toggle('active');
        });
    });
}

handleAccordions();

const handleRequests = () => {
    const requests = document.querySelectorAll('.request-header');

    requests.forEach((request) => {
        request.addEventListener('click', () => {
            const el = event.target;
            el.nextElementSibling.classList.toggle('hide');
        });
    });
}

handleRequests();