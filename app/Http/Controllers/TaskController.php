<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Fetch all tasks for the authenticated user
    public function index() 
    { 
        $tasks = auth()->user()->tasks; 
        return response()->json($tasks); 
    } 

    // Store a new task for the authenticated user
    public function store(Request $request) 
    { 
        // Validate the task input
        $request->validate([
            'task' => 'required|string|max:255',
        ]);

        // Create the task for the authenticated user
        $task = auth()->user()->tasks()->create([
            'task' => $request->task,
            'completed' => false,  // Set a default value for 'completed' if needed
        ]);

        return response()->json($task, 201);  // Return the created task with a 201 status code
    }

    // Update the completion status of a task
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'task' => 'required|string|max:255', // Ensure task name is required and a string
            'completed' => 'required|boolean',   // Ensure completed is required and boolean
        ]);
    
        // Find and update the task for the authenticated user
        $task = auth()->user()->tasks()->findOrFail($id);
    
        // Update task data
        $task->update([
            'task' => $request->task,          // Update task name
            'completed' => $request->completed, // Update completion status
        ]);
    
        // Return the updated task as a response
        return response()->json($task);
    }
    

    // Delete a task for the authenticated user
    public function destroy($id) 
    { 
        // Find the task and delete it
        $task = auth()->user()->tasks()->findOrFail($id); 
        $task->delete(); 

        return response()->json(null, 204);  // Return a 204 status code (No Content)
    } 
}