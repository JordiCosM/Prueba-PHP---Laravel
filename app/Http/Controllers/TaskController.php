<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Página de inicio
    public function index()
    {
        // Recoger los datos de la BD
        $tasks = Task::with('categories')->get();
        $categories = Category::all();

        return view('tasks.index', compact('tasks', 'categories'));
    }

    // Insertar los datos en DB
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'array'
        ]);

        $task = Task::create(['name' => $request->name]);

        if ($request->has('categories')) {
            $task->categories()->attach($request->categories);
        }

        $task->load('categories');
        $tasks = Task::with('categories')->get();
        $categories = Category::all();

        return response()->json([
            'html' => view('tasks.partials.task_row', compact('tasks', 'categories'))->render()
        ]);
    }

    // Eliminar una tarea
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->categories()->detach();
        $task->delete();

        return response()->json(['success' => true]);
    }
}
