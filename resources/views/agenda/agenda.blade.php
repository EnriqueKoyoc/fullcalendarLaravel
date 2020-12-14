@extends('layouts/plantilla')

@section('tittle', 'Calendar')

@section('container')
<!-- fullcalendar  -->
<script src="fullcalendar/core/main.min.css"></script>
<script src="fullcalendar/daygrid/main.min.css"></script>
<script src="fullcalendar/list/main.min.css"></script>
<script src="fullcalendar/timegrid/main.min.css"></script>
<!-- fullcalendar  -->
<script src="fullcalendar/core/main.js"></script>
<script src="fullcalendar/interaction/main.js"></script>
<script src="fullcalendar/daygrid/main.js"></script>
<script src="fullcalendar/list/main.js"></script>
<script src="fullcalendar/timegrid/main.js"></script>

<link href='lib/main.css' rel='stylesheet' />
<script src='lib/main.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        nowIndicator: true,
        dayMaxEvents: true,
        dayMaxEventRows: true, // allow "more" link when too many events
        views: {
            dayGridMonth: {
                buttonText: "Mes",
            },
            timeGridWeek: {
                buttonText: "Semana",
            },
            timeGridDay: {
                buttonText: "Día",
            },
            timeGrid: {
                dayMaxEventRows: 4 // adjust to 6 only for timeGridWeek/timeGridDay
            },
        },
        defaultView: "timeGridWeek",
        droppable: true, // this allows things to be dropped onto the calendar
        editable: false,
        eventLimit: false, // allow "more" link when too many events
        navLinks: true,
        selectable: true,

        events: "{{ url('/eventos/show') }}",

        eventClick: function(info) {
            $("#id_evento").val("");
            // recupera fecha inicial 
            let fechaI = moment(info.event.start).format('YYYY-MM-DD');
            let horaNueva1 = moment(info.event.start).format('H:mm');
            let fechaHora1 = fechaI + ' ' + horaNueva1;

            // recupera fecha final 
            let fechaF = moment(info.event.end).format('YYYY-MM-DD');
            let horaNueva2 = moment(info.event.end).format('H:mm');
            let fechaHora2 = fechaF + ' ' + horaNueva2;

            $("#id_evento").val(info.event.id);
            $("#title").val(info.event.title);
            $("#start").val(fechaHora1);
            $("#end").val(fechaHora2);
            $("#description").val(info.event.extendedProps.description);
            $("#color").val(info.event.backgroundColor);
            $("#user_id").val(info.event.extendedProps.user_id);

            // obtener los valores de id para comparar
            let id_user = $("#id_usuario").val();
            let user = info.event.extendedProps.user_id;

            if (id_user == user) {
                $('#btnActualizar').show();
                $('#btnEliminar').show();
            } else {
                $('#btnActualizar').hide();
                $('#btnEliminar').hide();
            }


            $("#titulo").html('Editar evento');
            $('#btnAgregar').hide();


            $("#addActividad").modal({
                backdrop: "static",
                show: true,
            });
        },
        dateClick: function(info, selectionInfo) {

            $("#id_evento").val("");
            // $("#title").val("");
            // $("#description").val("");
            // $("#color").val("");
            // $("#textColor").val("");
            // $("#start").val("");
            // $("#end").val("");
            // $("#user_id").val("");

            let fecha = moment(info.dateStr).format('YYYY-MM-DD')
            let hora = new Date();
            let horaNueva = moment(hora).format('H:mm');



            let FH = fecha + ' ' + horaNueva;

            $("#start").val(FH);
            $("#end").val(FH);
            $('#btnActualizar').hide();
            $('#btnEliminar').hide();
            $('#btnAgregar').show();
            $("#titulo").html('Agregar evento');


            $("#addActividad").modal({
                backdrop: "static",
                show: true,
            });
        },
    });

    calendar.setOption('locale', 'Es');
    calendar.render();

    // accion para agregar un evento 
    $('#btnAgregar').click(function() {
        objEvento = recolectarDatosGUI("POST");
        enviarInformacion('', objEvento);
    });

    // accion para eleminar un evento 
    $('#btnEliminar').click(function() {
        objEvento = recolectarDatosGUI("DELETE");
        enviarInformacion('/' + $("#id_evento").val(), objEvento);
    });

    // accion para modificar un evento 
    $('#btnActualizar').click(function() {
        objEvento = recolectarDatosGUI("PATCH");
        enviarInformacion('/' + $("#id_evento").val(), objEvento);
    });

    function recolectarDatosGUI(method) {

        nuevoEvento = {
            id: $("#id_evento").val(),
            title: $("#title").val(),
            description: $("#description").val(),
            color: $("#color").val(),
            textColor: $("#textColor").val(),
            start: $("#start").val(),
            end: $("#end").val(),
            user_id: $("#user_id").val(),
            '_token': $("meta[name='csrf-token']").attr("content"),
            '_method': method
        }
        return (nuevoEvento);

    }

    function enviarInformacion(accion, objEvento) {
        $.ajax({
            type: "POST",
            url: "{{url('/eventos')}}" + accion,
            data: objEvento,
            success: function(msg) {

                // console.log(msg)
                $("#addActividad").modal("hide");
                toastr.success('Evento agregado con éxito');
                calendar.refetchEvents();

            },
            error: function() {
                toastr.error('Error inesperado, intente de nuevo.')
            }
        });
    }

});
</script>
@if ($errors->any())
@foreach ($errors->all() as $error)
<div class="alert alert-danger"><strong>* {{$error}}</strong></div>

@endforeach
@endif

</script>
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">Agenda</h1>
    </div>
    <!-- /.col -->
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
            <li class="breadcrumb-item active">Calendar</li>
        </ol>
    </div>
    <!-- /.col -->
</div>

<input type="hidden" name="id_usuario" id="id_usuario" value="{{ Auth::user()->id }}">

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="sticky-top mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Draggable Events</h4>
                        </div>
                        <div class="card-body">
                            <!-- the events -->
                            <div id="external-events">
                                <div class="external-event bg-success">Lunch</div>
                                <div class="external-event bg-warning">Go home</div>
                                <div class="external-event bg-info">Do homework</div>
                                <div class="external-event bg-primary">Work on UI design</div>
                                <div class="external-event bg-danger">Sleep tight</div>
                                <div class="checkbox">
                                    <label for="drop-remove">
                                        <input type="checkbox" id="drop-remove">
                                        remove after drop
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create Event</h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                                <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
                                <ul class="fc-color-picker" id="color-chooser">
                                    <li><a class="text-primary" href="#"><i class="fas fa-square"></i></a></li>
                                    <li><a class="text-warning" href="#"><i class="fas fa-square"></i></a></li>
                                    <li><a class="text-success" href="#"><i class="fas fa-square"></i></a></li>
                                    <li><a class="text-danger" href="#"><i class="fas fa-square"></i></a></li>
                                    <li><a class="text-muted" href="#"><i class="fas fa-square"></i></a></li>
                                </ul>
                            </div>
                            <!-- /btn-group -->
                            <div class="input-group">
                                <input id="new-event" type="text" class="form-control" placeholder="Event Title">

                                <div class="input-group-append">
                                    <button id="add-new-event" type="button" class="btn btn-primary">Add</button>
                                </div>
                                <!-- /btn-group -->
                            </div>
                            <!-- /input-group -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card card-primary">
                    <div class="card-body p-0">
                        <!-- THE CALENDAR -->
                        <div id="calendar"></div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<div class="modal fade" id="addActividad">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <input type="hidden" id="id_evento" name="id_evento">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Título *</label>
                            <input type="text" name="title" id="title" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Descripción *</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Color del evento *</label>
                            <div class="input-group my-colorpicker2">
                                <input name="color" id="color" type="text" class="form-control">

                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-square"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <input type="hidden" name="color" id="color" value="green"> -->
                <input type="hidden" name="textColor" id="textColor" value="#FFF">

                <div class="row">
                    <div class="col md-6">
                        <div class="form-group">
                            <label>Fecha y hora inicio *</label>
                            <input id="start" name="start" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col md-6">
                        <div class="form-group">
                            <label>Fecha y hora final *</label>
                            <input id="end" name="end" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger" id="btnEliminar">Eliminar</button>
                <button type="submit" class="btn btn-primary" id="btnActualizar">Actualizar</button>
                <button type="submit" id="btnAgregar" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<script>
@if(Session::has('message'))
var type = "{{Session::get('alert-type','info')}}"
switch (type) {
    case 'nuevo':
        alert('Evento agregado con éxito');
        break;
    case 'actualizar':
        alert('Evento actualizado con éxito');
        break;
}
@endif
</script>

@endsection