import { Calendar } from '@fullcalendar/core';
import ptBrLocale from '@fullcalendar/core/locales/pt-br';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';

function parseSlots(element) {
    try {
        const slots = JSON.parse(element.dataset.horarios ?? '[]');

        return Array.isArray(slots) ? slots : [];
    } catch {
        return [];
    }
}

function formatSlotLabel(slot) {
    return `${slot.grupo} as ${slot.rotulo}`;
}

function addMinutesToTime(value, minutesToAdd) {
    const [hour, minute] = String(value).split(':').map(Number);
    const date = new Date(2000, 0, 1, hour || 0, minute || 0);

    date.setMinutes(date.getMinutes() + minutesToAdd);

    return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:00`;
}

function formatDateTimeLocal(date) {
    return [
        String(date.getFullYear()).padStart(4, '0'),
        String(date.getMonth() + 1).padStart(2, '0'),
        String(date.getDate()).padStart(2, '0'),
    ].join('-')
        + 'T'
        + [
            String(date.getHours()).padStart(2, '0'),
            String(date.getMinutes()).padStart(2, '0'),
        ].join(':');
}

function addMinutesToDateTime(value, minutesToAdd) {
    const [datePart, timePart = '00:00'] = String(value).split('T');
    const [year, month, day] = datePart.split('-').map(Number);
    const [hour, minute] = timePart.split(':').map(Number);
    const date = new Date(year, (month || 1) - 1, day || 1, hour || 0, minute || 0);

    date.setMinutes(date.getMinutes() + minutesToAdd);

    return formatDateTimeLocal(date);
}

function slotToEvent(slot) {
    const vagas = Number(slot.vagas ?? 0);
    const bloqueado = Boolean(slot.bloqueado);
    const lotado = Boolean(slot.lotado);

    return {
        end: addMinutesToDateTime(slot.valor, 30),
        id: slot.valor,
        start: slot.valor,
        title: bloqueado ? (slot.motivo || 'Indisponivel') : lotado ? 'Lotado' : `${vagas} ${vagas === 1 ? 'vaga' : 'vagas'}`,
        classNames: [
            'appointment-calendar-event',
            bloqueado ? 'is-unavailable' : lotado ? 'is-full' : 'is-available',
        ],
        extendedProps: {
            ...slot,
            bloqueado,
            lotado,
            vagas,
        },
    };
}

function isSlotUnavailable(slot) {
    return Boolean(slot?.lotado || slot?.bloqueado);
}

function updateSelection(element, hiddenInput, selectedSlot) {
    const selectedTexts = element.querySelectorAll('[data-agendamento-selecionado]');
    const selectedHelps = element.querySelectorAll('[data-agendamento-ajuda]');
    const selectedStatuses = element.querySelectorAll('[data-agendamento-status]');
    const selectionSummaries = element.querySelectorAll('[data-agendamento-resumo]');
    const submitButton = element.querySelector('[data-agendamento-submit]');

    if (selectedTexts.length === 0 || selectedHelps.length === 0 || ! submitButton) {
        return;
    }

    if (! selectedSlot) {
        hiddenInput.value = '';
        element.dataset.confirmText = element.dataset.confirmDefaultText || 'Revise o horario escolhido antes de confirmar.';
        selectedTexts.forEach((selectedText) => {
            selectedText.textContent = 'Nenhum horario selecionado';
        });
        selectedHelps.forEach((selectedHelp) => {
            selectedHelp.textContent = 'Clique em um horario disponivel no calendario.';
        });
        selectedStatuses.forEach((selectedStatus) => {
            selectedStatus.textContent = 'Selecione um horario';
        });
        selectionSummaries.forEach((summary) => {
            summary.classList.remove('has-selection');
        });
        submitButton.disabled = true;

        return;
    }

    hiddenInput.value = selectedSlot.valor;
    element.dataset.confirmText = `${formatSlotLabel(selectedSlot)}. Confirma este agendamento?`;
    selectedTexts.forEach((selectedText) => {
        selectedText.textContent = formatSlotLabel(selectedSlot);
    });
    selectedHelps.forEach((selectedHelp) => {
        selectedHelp.textContent = `${selectedSlot.vagas} ${selectedSlot.vagas === 1 ? 'vaga restante' : 'vagas restantes'} neste horario.`;
    });
    selectedStatuses.forEach((selectedStatus) => {
        selectedStatus.textContent = 'Ainda nao confirmado';
    });
    selectionSummaries.forEach((summary) => {
        summary.classList.add('has-selection');
    });
    submitButton.disabled = false;
}

function markSelectedEvent(calendarElement, selectedValue) {
    calendarElement.querySelectorAll('.appointment-calendar-event').forEach((eventElement) => {
        eventElement.classList.toggle('is-selected', eventElement.dataset.slotValue === selectedValue);
    });
}

function renderEventContent(arg) {
    const wrapper = document.createElement('div');
    const time = document.createElement('strong');
    const status = document.createElement('span');

    wrapper.className = 'appointment-calendar-event-content';
    time.textContent = arg.event.extendedProps.rotulo ?? arg.timeText;
    status.textContent = arg.event.title;

    wrapper.append(time, status);

    return { domNodes: [wrapper] };
}

function initAppointmentPicker(element) {
    const calendarElement = element.querySelector('[data-agendamento-calendario]');
    const hiddenInput = element.querySelector('[data-agendamento-valor]');
    const slots = parseSlots(element);

    if (! calendarElement || ! hiddenInput || slots.length === 0) {
        return;
    }

    const events = slots.map(slotToEvent);
    const firstAvailableSlot = slots.find((slot) => ! isSlotUnavailable(slot));
    const initialDate = String(hiddenInput.value || firstAvailableSlot?.valor || slots[0]?.valor).slice(0, 10);

    const calendar = new Calendar(calendarElement, {
        buttonText: {
            day: 'dia',
            month: 'mes',
            today: 'hoje',
            week: 'semana',
        },
        dayMaxEvents: 3,
        dayMaxEventRows: 3,
        eventMaxStack: 1,
        eventClick: (info) => {
            const slot = info.event.extendedProps;

            if (isSlotUnavailable(slot)) {
                return;
            }

            updateSelection(element, hiddenInput, slot);
            markSelectedEvent(calendarElement, hiddenInput.value);
        },
        eventContent: renderEventContent,
        eventDidMount: (info) => {
            info.el.dataset.slotValue = info.event.id;
            info.el.title = `${formatSlotLabel(info.event.extendedProps)} - ${info.event.title}`;
            info.el.classList.toggle('is-selected', info.event.id === hiddenInput.value);
        },
        events,
        firstDay: 0,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        height: 'auto',
        initialDate,
        initialView: 'timeGridWeek',
        locale: ptBrLocale,
        moreLinkClick: 'popover',
        nowIndicator: true,
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        slotDuration: '00:30:00',
        slotLabelInterval: '00:30:00',
        slotMaxTime: addMinutesToTime(element.dataset.horarioFim || '17:00', 30),
        slotMinTime: element.dataset.horarioInicio || '08:00:00',
    });

    calendar.render();

    if (hiddenInput.value) {
        const selectedSlot = slots.find((slot) => slot.valor === hiddenInput.value);
        updateSelection(element, hiddenInput, isSlotUnavailable(selectedSlot) ? null : selectedSlot);
        markSelectedEvent(calendarElement, hiddenInput.value);
    } else {
        updateSelection(element, hiddenInput, null);
    }
}

export function initAgendamentoPicker() {
    document.querySelectorAll('[data-agendamento-picker]').forEach(initAppointmentPicker);
}
