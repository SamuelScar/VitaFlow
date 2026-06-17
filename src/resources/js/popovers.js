import { Popover, Tooltip } from 'bootstrap';

const initPopovers = () => {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].forEach(popoverTriggerEl => new Popover(popoverTriggerEl));

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].forEach(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
};

document.addEventListener('DOMContentLoaded', initPopovers);
document.addEventListener('livewire:navigated', initPopovers);

// Re-initialize after Livewire DOM updates
document.addEventListener('livewire:initialized', () => {
    Livewire.hook('morph.updated', ({ el, component }) => {
        initPopovers();
    });
});

export { initPopovers };
