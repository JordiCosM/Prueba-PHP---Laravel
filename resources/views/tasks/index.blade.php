@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Gestor de tareas</h1>

        <form id="taskform" action="{{ route('tasks.store') }}" method="POST">
            @csrf
            <input type="text" id="taskName" name="name" placeholder="Nueva tarea..." required>
            @foreach ($categories as $category)
            <label>
                <input type="checkbox" name="categories[]" value="{{ $category->id }}"> {{ $category->name }}
            </label>
            @endforeach
        </form>
        <button type="button" id="send">Añadir</button>
        <div id="errorMessage">Los campos no pueden estar vacíos.</div>

        <table>
            <thead>
                <tr>
                    <th>Tarea</th>
                    <th>Categorías</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id=taskList>
                @include('tasks.partials.task_row', ['tasks' => $tasks])
            </tbody>
        </table>
    </div>

    <script>
        // Enviar el formulario por ajax
        $('#send').on('click', function(e) {
            var task = $('#taskName').val();
            var isChecked = $('input[name="categories[]"]:checked').length > 0;

            // Comprobar datos vacios
            if (task.trim() === '' || !isChecked) {
                $('#errorMessage').show();
            } else {
                $('#errorMessage').hide();

                var selectedCategories = [];
                $('input[name="categories[]"]:checked').each(function() {
                    selectedCategories.push($(this).val());
                });

                $.ajax({
                    url: $(this).attr("action"),
                    type: 'POST',
                    data: {
                        name: task,
                        categories: selectedCategories,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: (response) => {
                        // Rellenar la tabla
                        $('#taskList').html(response.html);

                        // Vaciar campos del formulario
                        $('#taskName').val('');
                        $('input[name="categories[]"]:checked').prop('checked', false);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al guardar la tarea:', error);
                    }
                });
            }
        });

        // Eliminar una tarea
        function deleteTask(id) {
            $.ajax({
                url: '/tasks/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: () => {
                    $('#task-' + id).remove();
                }
            });
        }
    </script>
@endsection
