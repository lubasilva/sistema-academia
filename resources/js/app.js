import './bootstrap';
import * as bootstrap from 'bootstrap';
import '@popperjs/core';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import ptBrLocale from '@fullcalendar/core/locales/pt-br';
import Chart from 'chart.js/auto';

// Expor Bootstrap globalmente
window.bootstrap = bootstrap;

window.Calendar = Calendar;
window.dayGridPlugin = dayGridPlugin;
window.timeGridPlugin = timeGridPlugin;
window.interactionPlugin = interactionPlugin;
window.ptBrLocale = ptBrLocale;
window.Chart = Chart;

