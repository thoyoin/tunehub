import "/node_modules/bootstrap/dist/js/bootstrap.bundle.js"
import './alpine/home.js';
import './alpine/artistStudio.js';
import './alpine/player.js';
import Alpine from 'alpinejs'

import sort from '@alpinejs/sort'

Alpine.plugin(sort)

window.Alpine = Alpine;

Alpine.start()
document.addEventListener("DOMContentLoaded", () => {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].forEach(el => {
        new bootstrap.Popover(el);
    });
});
