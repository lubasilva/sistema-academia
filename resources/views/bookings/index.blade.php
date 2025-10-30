@extends('layouts.base')

@section('title', 'Agenda de Aulas')

@push('styles')
<style>
    #calendar {
        font-size: 0.9rem;
    }
    
    .fc-event {
        cursor: pointer;
    }
    
    .fc-timegrid-slot {
        height: 3em;
    }
    
    /* Indicadores de ocupação */
    .occupation-indicator {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2px;
        background: rgba(108, 117, 125, 0.1);
        border-radius: 4px;
        font-size: 0.7rem;
    }
    
    .occupation-dots {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
        justify-content: center;
        margin-bottom: 2px;
    }
    
    .occupation-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #6c757d;
        display: inline-block;
    }
    
    .occupation-count {
        font-size: 0.65rem;
        font-weight: bold;
        color: #495057;
    }
    
    .occupation-text {
        font-size: 0.65rem;
        color: #6c757d;
        font-weight: 600;
    }
    
    .booking-event {
        padding: 2px 4px;
        font-size: 0.75rem;
    }
    
    /* Background events de ocupação */
    .fc-event.fc-bg-event {
        opacity: 1 !important;
    }
    
    @media (max-width: 768px) {
        #calendar {
            font-size: 0.75rem;
        }
        
        .fc-toolbar {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
        }
        
        .occupation-dot {
            width: 6px;
            height: 6px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Agenda de Aulas</h2>
            <p class="text-muted">Visualize e gerencie suas reservas</p>
        </div>
        @can('create', App\Models\Booking::class)
        <div class="col-auto">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookingModal">
                <i class="bi bi-plus-circle"></i> Nova Reserva
            </button>
        </div>
        @endcan
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-semibold mb-0">Legenda</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="badge bg-success me-2">&nbsp;&nbsp;&nbsp;</span> Reservado
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-info me-2">&nbsp;&nbsp;&nbsp;</span> Realizado
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-danger me-2">&nbsp;&nbsp;&nbsp;</span> Cancelado
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-secondary me-2">&nbsp;&nbsp;&nbsp;</span> Ausente
                    </div>
                </div>
            </div>

            @if(auth()->user()->role === 'student' && auth()->user()->activePlan)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-semibold mb-0">Meu Plano</h6>
                </div>
                <div class="card-body">
                    <h5 class="text-primary fw-bold">{{ auth()->user()->activePlan->plan->name }}</h5>
                    <p class="mb-2">
                        <strong>Créditos:</strong> {{ auth()->user()->activePlan->credits_remaining }}
                    </p>
                    <p class="mb-0 small text-muted">
                        Válido até: {{ \Carbon\Carbon::parse(auth()->user()->activePlan->ends_at)->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Nova Reserva -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Nova Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="booking_date" class="form-label">Data</label>
                        <input type="date" class="form-control" id="booking_date" name="date" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="schedule_id" class="form-label">Horário</label>
                        <select class="form-select" id="schedule_id" name="schedule_id" required>
                            <option value="">Selecione uma data primeiro</option>
                        </select>
                    </div>
                    @if(auth()->user()->role === 'admin')
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Aluno</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Selecione o aluno</option>
                            @foreach(\App\Models\User::where('role', 'student')->get() as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Reserva</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) {
        console.error('Elemento #calendar não encontrado!');
        return;
    }
    
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar não foi carregado!');
        return;
    }
    
    // Função para abrir modal
    function openBookingModal() {
        const modalEl = document.getElementById('bookingModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            console.error('Bootstrap Modal não disponível');
        }
    }
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        height: 'auto',
        selectable: true,
        selectMirror: true,
        nowIndicator: true,
        eventDisplay: 'block',
        displayEventTime: false,
        events: function(info, successCallback, failureCallback) {
            // Buscar bookings do usuário
            fetch('/api/bookings', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(bookings => {
                // Buscar ocupação de todos os horários
                fetch(`/api/schedules/occupation?start=${info.startStr}&end=${info.endStr}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(occupation => {
                    // Combinar bookings com eventos de ocupação
                    const allEvents = [...bookings, ...occupation];
                    successCallback(allEvents);
                })
                .catch(() => {
                    successCallback(bookings);
                });
            })
            .catch(failureCallback);
        },
        eventContent: function(arg) {
            // Se for evento de ocupação (background)
            if (arg.event.extendedProps.isOccupation) {
                const count = arg.event.extendedProps.count;
                const capacity = arg.event.extendedProps.capacity;
                
                // Criar indicadores visuais (bolinhas)
                let dots = '';
                for (let i = 0; i < Math.min(count, 10); i++) {
                    dots += '<span class="occupation-dot"></span>';
                }
                
                if (count > 10) {
                    dots += `<span class="occupation-count">+${count - 10}</span>`;
                }
                
                return {
                    html: `<div class="occupation-indicator">
                        <div class="occupation-dots">${dots}</div>
                        <small class="occupation-text">${count}/${capacity}</small>
                    </div>`
                };
            }
            
            // Eventos normais (bookings do usuário)
            return {
                html: `<div class="booking-event">
                    <strong>${arg.event.title}</strong>
                </div>`
            };
        },
        eventClick: function(info) {
            // Não permitir clicar em eventos de ocupação
            if (info.event.extendedProps.isOccupation) {
                return;
            }
            
            if (confirm('Deseja cancelar esta reserva?')) {
                // TODO: implementar cancelamento
                alert('Reserva: ' + info.event.title);
            }
        },
        select: function(info) {
            // Quando clicar e arrastar em um horário vazio
            const dateStr = info.startStr.split('T')[0];
            const timeStr = info.start.toTimeString().substring(0, 5);
            
            // Preencher data no modal
            document.getElementById('booking_date').value = dateStr;
            
            // Carregar horários e pré-selecionar o horário clicado
            loadSchedules(dateStr, timeStr);
            
            // Abrir modal
            openBookingModal();
            
            calendar.unselect();
        },
        dateClick: function(info) {
            // Quando clicar em uma data específica
            const dateStr = info.dateStr.split('T')[0];
            const timeStr = info.date.toTimeString().substring(0, 5);
            
            document.getElementById('booking_date').value = dateStr;
            loadSchedules(dateStr, timeStr);
            openBookingModal();
        }
    });
    
    calendar.render();
    console.log('FullCalendar renderizado com sucesso!');

    // Função para carregar horários disponíveis
    function loadSchedules(date, preSelectTime = null) {
        const scheduleSelect = document.getElementById('schedule_id');
        scheduleSelect.innerHTML = '<option value="">Carregando...</option>';
        
        fetch(`/api/schedules/available?date=${date}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao carregar horários');
            }
            return response.json();
        })
        .then(data => {
            console.log('Horários carregados:', data);
            scheduleSelect.innerHTML = '<option value="">Selecione o horário</option>';
            
            if (data.length === 0) {
                scheduleSelect.innerHTML = '<option value="">Nenhum horário disponível</option>';
                return;
            }
            
            data.forEach(schedule => {
                const option = document.createElement('option');
                option.value = schedule.id;
                option.textContent = `${schedule.time} (${schedule.available_slots} vagas disponíveis)`;
                option.disabled = schedule.available_slots === 0;
                
                // Pré-selecionar o horário clicado
                if (preSelectTime && schedule.time === preSelectTime) {
                    option.selected = true;
                }
                
                scheduleSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erro:', error);
            scheduleSelect.innerHTML = '<option value="">Erro ao carregar horários</option>';
        });
    }

    // Carregar horários ao mudar data manualmente
    const bookingDateInput = document.getElementById('booking_date');
    if (bookingDateInput) {
        bookingDateInput.addEventListener('change', function() {
            const date = this.value;
            if (date) {
                loadSchedules(date);
            }
        });
    }
    
    // Recarregar eventos após criar reserva (submit do form)
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function() {
            setTimeout(() => {
                calendar.refetchEvents();
            }, 1000);
        });
    }
});
</script>
@endpush
@endsection
