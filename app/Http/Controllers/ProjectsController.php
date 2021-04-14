<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ProjectsController extends Controller
{
    public function show(Request $request)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $projects = Projects::where('creator_id', $loggedId)->orWhereHas('users', function (Builder $query) use ($loggedId) {
            $query->where('users.id', '=', $loggedId);
        })->get();
        return View::make('projects', [
            'projects' => $projects,
            'loggedUserId' => $loggedId
        ]);
    }

    public function assignees(Request $request, $id)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $users = User::all();
        return View::make('assign', [
            'users' => $users,
            'projectId' => $id
        ]);
    }

    public function assign(Request $request)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }
        $project = Projects::find($request->projectId);
        if ($project->creator_id != $loggedId) return View::make('login');

        $project->users()->attach($request->userId);
        return redirect('projects');
    }

    public function create(Request $request)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        Projects::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'done_tasks' => $request->done_tasks,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'creator_id' => $loggedId,
        ]);

        return View::make('create-project');
    }

    public function editForm(Request $request, $projectId)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $project = Projects::where('id', $projectId)->first();

        return View::make('edit-project', [
            'project' => $project,
            'loggedUserId' => $loggedId
        ]);
    }

    public function edit(Request $request, $projectId)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $project = Projects::where('id', $projectId)->first();
        if ($project->creator_id == $loggedId) {
            $project->title = $request->title;
            $project->description = $request->description;
            $project->price = $request->price;
            $project->done_tasks = $request->done_tasks;
            $project->starts_at = $request->starts_at;
            $project->ends_at = $request->ends_at;
        } else {
            $project->done_tasks = $request->done_tasks;
        }
        $project->save();

        return redirect('/projects');
    }
}