<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Projects;
use App\Models\Tasks;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teamMembers = User::whereHas('assignedTasks', function ($q) {
                $q->whereHas('project', function($query) {
                    $query->where('manager_id', auth()->id());
                });
            })
            ->withCount([
                'assignedTasks as pending_tasks_count' => function($query) {
                    $query->whereHas('project', function($q) {
                        $q->where('manager_id', auth()->id());
                    })->where('status', '!=', 'done');
                },
                'assignedTasks as total_tasks_count' => function($query) {
                    $query->whereHas('project', function($q) {
                        $q->where('manager_id', auth()->id());
                    });
                },
                'assignedTasks as completed_tasks_count' => function($query) {
                    $query->whereHas('project', function($q) {
                        $q->where('manager_id', auth()->id());
                    })->where('status', 'done');
                }
            ])
            ->get();

        return view('manager.team.index', compact('teamMembers'));
    }

    public function show($id)
    {
        $teamMember = User::with([
                'assignedTasks' => function($query) {
                    $query->whereHas('project', function($q) {
                        $q->where('manager_id', auth()->id());
                    })->with('project');
                }
            ])
            ->where('id', $id)
            ->whereHas('assignedTasks.project', function ($query) {
                $query->where('manager_id', auth()->id());
            })
            ->firstOrFail();

        return view('manager.team.show', compact('teamMember'));
    }


    public function projectTeam($projectId)
{
    $project = Projects::where('id', $projectId)
        ->where('manager_id', auth()->id())
        ->firstOrFail();

    $teamMembers = User::whereHas('assignedTasks', function ($q) use ($projectId) {
            $q->where('project_id', $projectId);
        })
        ->withCount([
            'assignedTasks as project_tasks_count' => function($query) use ($projectId) {
                $query->where('project_id', $projectId);
            },
            'assignedTasks as completed_tasks_count' => function($query) use ($projectId) {
                $query->where('project_id', $projectId)->where('status', 'done');
            }
        ])
        ->get();

    return view('manager.team.project-team', compact('project', 'teamMembers'));
}
}
