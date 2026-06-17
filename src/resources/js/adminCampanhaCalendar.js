import { Calendar } from '@fullcalendar/core';
import ptBrLocale from '@fullcalendar/core/locales/pt-br';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';

function parseSlots(element) {
    try {
        const slots = JSON.parse(element.dataset.horarios ?? '[]');
        return Array.isArray(slots) ? slots : [];
    } catch {
        return [];
    }
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
    const vagasOcupadas = Number(slot.vagas_ocupadas ?? 0);
    const vagasTotais = Number(slot.total_vagas ?? 0);
    const vagasLivres = Number(slot.vagas_livres ?? 0);
    const status = slot.status; // 'verde', 'amarelo', 'vermelho'

    let className = 'is-available';
    if (status === 'vermelho') className = 'is-full';
    else if (status === 'amarelo') className = 'is-warning';

    return {
        end: addMinutesToDateTime(slot.valor, 30),
        id: slot.valor,
        start: slot.valor,
        title: status === 'vermelho' ? 'Lotado' : `${vagasOcupadas}/${vagasTotais} ocupadas`,
        classNames: [
            'appointment-calendar-event',
            className,
        ],
        extendedProps: {
            ...slot,
            vagasOcupadas,
            vagasTotais,
            vagasLivres,
            status
        },
    };
}

function renderEventContent(arg) {
    const wrapper = document.createElement('div');
    const time = document.createElement('strong');
    const statusText = document.createElement('span');

    wrapper.className = 'appointment-calendar-event-content';
    time.textContent = arg.event.extendedProps.rotulo ?? arg.timeText;
    statusText.textContent = arg.event.title;

    wrapper.append(time, statusText);

    return { domNodes: [wrapper] };
}

function initAdminCalendar(element) {
    const calendarElement = element.querySelector('[data-admin-calendario]');
    const slots = parseSlots(element);

    if (! calendarElement || slots.length === 0) {
        return;
    }

    const events = slots.map(slotToEvent);
    const initialDate = String(slots[0]?.valor).slice(0, 10);

    const calendar = new Calendar(calendarElement, {
        buttonText: {
            day: 'dia',
            month: 'mes',
            today: 'hoje',
            week: 'semana',
        },
        dayMaxEvents: true,
        eventContent: renderEventContent,
        eventDidMount: (info) => {
            const status = info.event.extendedProps.status;
            if (status === 'vermelho') {
                info.el.style.backgroundColor = 'var(--bs-danger)';
                info.el.style.borderColor = 'var(--bs-danger)';
                info.el.style.color = 'white';
            } else if (status === 'amarelo') {
                info.el.style.backgroundColor = 'var(--bs-warning)';
                info.el.style.borderColor = 'var(--bs-warning)';
                info.el.style.color = 'black';
            } else {
                info.el.style.backgroundColor = 'rgba(25, 135, 84, 0.1)';
                info.el.style.borderColor = 'var(--bs-success)';
                info.el.style.color = 'var(--bs-success)';
            }
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
        nowIndicator: true,
        plugins: [dayGridPlugin, timeGridPlugin],
        slotDuration: '00:30:00',
        slotLabelInterval: '00:30:00',
        slotMaxTime: element.dataset.horarioFim || '17:00:00',
        slotMinTime: element.dataset.horarioInicio || '08:00:00',
        allDaySlot: false,
    });

    calendar.render();
}

export function initAdminCampanhaCalendar() {
    document.querySelectorAll('[data-admin-calendar-wrapper]').forEach(initAdminCalendar);
}
