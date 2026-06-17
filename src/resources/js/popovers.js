import { Popover } from 'bootstrap';

const initPopovers = () => {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].forEach(popoverTriggerEl => new Popover(popoverTriggerEl));
};

document.addEventListener('DOMContentLoaded', initPopovers);
document.addEventListener('livewire:navigated', initPopovers);

export { initPopovers };
