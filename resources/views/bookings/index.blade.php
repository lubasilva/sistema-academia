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
    
    /* Indicadores de ocupação melhorados */
    .occupation-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        background: rgba(13, 110, 253, 0.1);
        border-radius: 6px;
        border: 1px solid rgba(13, 110, 253, 0.2);
        cursor: help;
        transition: all 0.2s ease;
        height: 100%;
    }
    
    .occupation-indicator:hover {
        background: rgba(13, 110, 253, 0.15);
        border-color: rgba(13, 110, 253, 0.3);
        transform: scale(1.02);
    }
    
    .occupation-text {
        font-size: 0.85rem;
        color: #0d6efd;
        font-weight: 600;
    }
    
    .occupation-high {
        background: rgba(220, 53, 69, 0.1);
        border-color: rgba(220, 53, 69, 0.2);
    }
    
    .occupation-high .occupation-text {
        color: #dc3545;
    }
    
    .occupation-full {
        background: rgba(108, 117, 125, 0.1);
        border-color: rgba(108, 117, 125, 0.2);
    }
    
    .occupation-full .occupation-text {
        color: #6c757d;
    }
    
    /* Tooltip customizado */
    .custom-tooltip {
        position: absolute;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        min-width: 200px;
        max-width: 300px;
        font-size: 0.85rem;
        display: none;
    }
    
    .tooltip-header {
        font-weight: 600;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
        color: #495057;
    }
    
    .tooltip-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .tooltip-list li {
        padding: 4px 0;
        color: #6c757d;
    }
    
    .tooltip-list li i {
        color: #0d6efd;
        margin-right: 6px;
    }
    
    /* Autocomplete de alunos */
    .student-suggestions {
        position: absolute;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        width: 100%;
        margin-top: 2px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: none;
    }
    
    .student-suggestions.show {
        display: block;
    }
    
    .student-suggestion-item {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }
    
    .student-suggestion-item:hover {
        background: #f8f9fa;
    }
    
    .student-suggestion-item:last-child {
        border-bottom: none;
    }
    
    .student-name {
        font-weight: 500;
        color: #212529;
        display: block;
    }
    
    .student-details {
        font-size: 0.85rem;
        color: #6c757d;
        display: block;
        margin-top: 2px;
    }
    
    .student-credits {
        color: #0d6efd;
        font-weight: 500;
    }
    
    .no-students-found {
        padding: 15px;
        text-align: center;
        color: #6c757d;
        font-style: italic;
    }
    
    .booking-event {
        padding: 2px 4px;
        font-size: 0.75rem;
    }
    
    /* Bookings do próprio usuário */
    .my-booking {
        border: 2px solid #198754 !important;
        background: #198754 !important;
        color: white !important;
        font-weight: 600;
        z-index: 10 !important;
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
                    <div class="mb-3">
                        <strong class="d-block mb-2">Nível de Ocupação:</strong>
                        <div class="mb-2">
                            <span class="badge bg-primary me-2">&nbsp;&nbsp;&nbsp;</span> Normal (até 69%)
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-warning me-2">&nbsp;&nbsp;&nbsp;</span> Alta (70-89%)
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary me-2">&nbsp;&nbsp;&nbsp;</span> Lotado (90% ou mais)
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle"></i> Passe o mouse sobre os horários para ver a lista de alunos agendados
                    </small>
                </div>
            </div>

            @if(auth()->user()->role === 'aluno' && auth()->user()->activePlan)
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
                        <label for="booking_time" class="form-label">Horário</label>
                        <select class="form-select" id="booking_time" name="time" required>
                            <option value="">Selecione uma data primeiro</option>
                        </select>
                        <small class="text-muted">Horários disponíveis conforme configuração da academia</small>
                    </div>
                    @if(auth()->user()->role === 'admin')
                    <div class="mb-3">
                        <label for="student_search" class="form-label">Aluno (somente com créditos disponíveis)</label>
                        <input type="text" 
                               class="form-control" 
                               id="student_search" 
                               placeholder="Clique para ver todos ou digite para buscar..."
                               autocomplete="off">
                        <input type="hidden" id="user_id" name="user_id" required>
                        <div id="student_suggestions" class="student-suggestions"></div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Clique no campo para ver todos os alunos com créditos ou digite para buscar
                        </small>
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
    
    // Criar tooltip customizado
    const tooltip = document.createElement('div');
    tooltip.className = 'custom-tooltip';
    document.body.appendChild(tooltip);
    
    // Função para mostrar tooltip
    function showTooltip(event, students, count, capacity) {
        if (!students || students.length === 0) return;
        
        let html = `<div class="tooltip-header">${count}/${capacity} vagas ocupadas</div>`;
        html += '<ul class="tooltip-list">';
        students.forEach(student => {
            html += `<li><i class="bi bi-person-fill"></i>${student}</li>`;
        });
        html += '</ul>';
        
        tooltip.innerHTML = html;
        tooltip.style.display = 'block';
        
        // Posicionar tooltip
        const rect = event.target.getBoundingClientRect();
        tooltip.style.left = (rect.left + window.scrollX) + 'px';
        tooltip.style.top = (rect.bottom + window.scrollY + 5) + 'px';
    }
    
    // Função para esconder tooltip
    function hideTooltip() {
        tooltip.style.display = 'none';
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
            // Buscar APENAS ocupação (sem bookings individuais)
            fetch(`/api/schedules/occupation?start=${info.startStr}&end=${info.endStr}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(occupation => {
                console.log('Ocupação carregada:', occupation.length, 'eventos');
                successCallback(occupation);
            })
            .catch(failureCallback);
        },
        eventContent: function(arg) {
            // Mostrar APENAS ocupação (todos os eventos são de ocupação agora)
            const count = arg.event.extendedProps.count;
            const capacity = arg.event.extendedProps.capacity;
            const percentage = (count / capacity) * 100;
            
            // Definir classe baseada na ocupação
            let occupationClass = '';
            if (percentage >= 90) {
                occupationClass = 'occupation-full';
            } else if (percentage >= 70) {
                occupationClass = 'occupation-high';
            }
            
            return {
                html: `<div class="occupation-indicator ${occupationClass}" 
                            data-students='${JSON.stringify(arg.event.extendedProps.students || [])}' 
                            data-count="${count}" 
                            data-capacity="${capacity}">
                    <span class="occupation-text">${count}/${capacity}</span>
                </div>`
            };
        },
        eventDidMount: function(info) {
            // Adicionar event listeners para tooltip em TODOS os eventos (todos são ocupação)
            const el = info.el.querySelector('.occupation-indicator');
            if (el) {
                el.addEventListener('mouseenter', function(e) {
                    const students = JSON.parse(this.getAttribute('data-students') || '[]');
                    const count = this.getAttribute('data-count');
                    const capacity = this.getAttribute('data-capacity');
                    showTooltip(e, students, count, capacity);
                });
                
                el.addEventListener('mouseleave', function() {
                    hideTooltip();
                });
            }
        },
        eventClick: function(info) {
            // Não fazer nada ao clicar (são apenas indicadores de ocupação)
            return false;
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
        const scheduleSelect = document.getElementById('booking_time');
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
            console.log('Horários disponíveis:', data);
            scheduleSelect.innerHTML = '<option value="">Selecione o horário</option>';
            
            if (data.length === 0) {
                scheduleSelect.innerHTML = '<option value="">Nenhum horário disponível neste dia</option>';
                return;
            }
            
            data.forEach(schedule => {
                const option = document.createElement('option');
                option.value = schedule.time;
                
                const occupied = schedule.capacity - schedule.available_slots;
                const statusText = schedule.available_slots === 0 ? 'LOTADO' : `${schedule.available_slots} vaga(s)`;
                
                option.textContent = `${schedule.time} - ${occupied}/${schedule.capacity} (${statusText})`;
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
    
    // Autocomplete de alunos (apenas para admin)
    const studentSearchInput = document.getElementById('student_search');
    const studentSuggestions = document.getElementById('student_suggestions');
    const userIdInput = document.getElementById('user_id');
    
    if (studentSearchInput && studentSuggestions && userIdInput) {
        let searchTimeout;
        let currentStudents = [];
        
        // Função para buscar alunos
        function fetchStudents(search = '') {
            fetch(`/api/students/with-credits?search=${encodeURIComponent(search)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(students => {
                currentStudents = students;
                displayStudentSuggestions(students);
            })
            .catch(error => {
                console.error('Erro ao buscar alunos:', error);
                studentSuggestions.innerHTML = '<div class="no-students-found">Erro ao buscar alunos</div>';
                studentSuggestions.classList.add('show');
            });
        }
        
        // Mostrar todos os alunos com créditos ao clicar no campo
        studentSearchInput.addEventListener('focus', function() {
            if (!userIdInput.value && this.value.trim().length === 0) {
                // Se não tem nada selecionado e campo vazio, mostrar todos
                fetchStudents('');
            } else if (currentStudents.length > 0) {
                // Se já tem dados em cache, mostrar
                displayStudentSuggestions(currentStudents);
            }
        });
        
        // Buscar alunos ao digitar
        studentSearchInput.addEventListener('input', function() {
            const search = this.value.trim();
            
            // Limpar timeout anterior
            clearTimeout(searchTimeout);
            
            // Limpar seleção
            userIdInput.value = '';
            
            if (search.length === 0) {
                // Se apagou tudo, mostrar todos novamente
                fetchStudents('');
                return;
            }
            
            if (search.length < 2) {
                return;
            }
            
            // Buscar após 300ms de inatividade
            searchTimeout = setTimeout(() => {
                fetchStudents(search);
            }, 300);
        });
        
        // Exibir sugestões
        function displayStudentSuggestions(students) {
            if (students.length === 0) {
                studentSuggestions.innerHTML = '<div class="no-students-found">Nenhum aluno encontrado com créditos disponíveis</div>';
                studentSuggestions.classList.add('show');
                return;
            }
            
            let html = '';
            students.forEach(student => {
                html += `
                    <div class="student-suggestion-item" data-id="${student.id}">
                        <span class="student-name">${student.name}</span>
                        <span class="student-details">
                            ${student.email} • 
                            <span class="student-credits">${student.credits_info}</span> • 
                            ${student.plan_name}
                        </span>
                    </div>
                `;
            });
            
            studentSuggestions.innerHTML = html;
            studentSuggestions.classList.add('show');
            
            // Adicionar event listeners nos itens
            studentSuggestions.querySelectorAll('.student-suggestion-item').forEach(item => {
                item.addEventListener('click', function() {
                    const studentId = this.dataset.id;
                    const student = currentStudents.find(s => s.id == studentId);
                    
                    if (student) {
                        studentSearchInput.value = student.name;
                        userIdInput.value = student.id;
                        studentSuggestions.classList.remove('show');
                    }
                });
            });
        }
        
        // Fechar sugestões ao clicar fora
        document.addEventListener('click', function(e) {
            if (!studentSearchInput.contains(e.target) && !studentSuggestions.contains(e.target)) {
                studentSuggestions.classList.remove('show');
            }
        });
        
        // Resetar ao abrir modal
        const bookingModal = document.getElementById('bookingModal');
        if (bookingModal) {
            bookingModal.addEventListener('show.bs.modal', function() {
                studentSearchInput.value = '';
                userIdInput.value = '';
                studentSuggestions.classList.remove('show');
            });
        }
    }
});
</script>
@endpush
@endsection
