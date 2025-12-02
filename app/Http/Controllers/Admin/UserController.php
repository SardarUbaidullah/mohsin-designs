<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Tasks;
use App\Models\Projects;
use Illuminate\Http\Request;
use App\Providers\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{


      public function userTasks(User $user)
    {
        $tasks = Tasks::where('assigned_to', $user->id)
                    ->with(['project', 'milestone'])
                    ->latest()
                    ->get();

        return view('admin.users.tasks', compact('user', 'tasks'));
    }

      public function userProjects(User $user)
    {
        $projects = Projects::where('manager_id', $user->id)
                          ->withCount('tasks')
                          ->latest()
                          ->get();

        return view('admin.users.projects', compact('user', 'projects'));
    }
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // ==================== STATUS MANAGEMENT METHODS ====================

    /**
     * Toggle user status (Active/Inactive)
     */
    /**
 * Toggle user status (Active/Inactive)
 */
public function toggleStatus(User $user)
{
    // Prevent self-deactivation
    if ($user->id === auth()->id()) {
        return redirect()->back()->with('error', 'You cannot deactivate your own account.');
    }

    // Prevent deactivating the last super admin
    if ($user->role === 'super_admin' && $user->status === 'active') {
        $superAdminCount = User::where('role', 'super_admin')
            ->where('status', 'active')
            ->count();
        
        if ($superAdminCount <= 1) {
            return redirect()->back()->with('error', 'Cannot deactivate the last active super admin.');
        }
    }

    try {
        DB::transaction(function () use ($user) {
            $oldStatus = $user->status;
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            
            $updateData = [
                'status' => $newStatus,
                'deactivated_at' => $newStatus === 'inactive' ? now() : null
            ];

            $user->update($updateData);

            // Send notification
            $this->notifyUserStatusChanged($user, $oldStatus, $newStatus);
        });

        $statusText = $user->status === 'inactive' ? 'deactivated' : 'activated';
        return redirect()->back()->with('success', "User {$user->name} has been {$statusText} successfully.");

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update user status. Please try again.');
    }
}

    /**
     * Bulk update user status
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:active,inactive'
        ]);

        $currentUser = auth()->user();
        $userIds = $request->user_ids;
        $newStatus = $request->status;

        // Remove current user from the list to prevent self-deactivation
        $userIds = array_diff($userIds, [$currentUser->id]);

        // Check if trying to deactivate all super admins
        if ($newStatus === 'inactive') {
            $superAdminIds = User::whereIn('id', $userIds)
                ->where('role', 'super_admin')
                ->pluck('id')
                ->toArray();

            $activeSuperAdminCount = User::where('role', 'super_admin')
                ->where('status', 'active')
                ->whereNotIn('id', $superAdminIds)
                ->count();

            if ($activeSuperAdminCount === 0 && !empty($superAdminIds)) {
                return redirect()->back()
                    ->with('error', 'Cannot deactivate all super admin accounts. At least one super admin must remain active.');
            } 
        }

        try {
            DB::transaction(function () use ($userIds, $newStatus, $currentUser) {
                $updateData = ['status' => $newStatus];
                
                if ($newStatus === 'inactive') {
                    $updateData['deactivated_at'] = now();
                } else {
                    $updateData['deactivated_at'] = null;
                }

                $users = User::whereIn('id', $userIds)->get();
                
                foreach ($users as $user) {
                    $oldStatus = $user->status;
                    $user->update($updateData);
                    
                    // Send notification for each user
                    $this->notifyUserStatusChanged($user, $oldStatus, $newStatus);
                }

                // Log bulk activity
              
            });

            $statusText = $newStatus === 'active' ? 'activated' : 'deactivated';
            return redirect()->back()
                ->with('success', count($userIds) . " users have been {$statusText} successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update users status. Please try again.');
        }
    }

    /**
     * Notify user about status change
     */
    private function notifyUserStatusChanged($user, $oldStatus, $newStatus)
    {
        if ($oldStatus === $newStatus) {
            return;
        }

        $action = $newStatus === 'active' ? 'activated' : 'deactivated';
        
        $data = [
            'title' => $newStatus === 'active' ? 'Account Activated' : 'Account Deactivated',
            'message' => $newStatus === 'active' 
                ? "Your account has been activated. You can now access the system."
                : "Your account has been deactivated. You will no longer be able to access the system.",
            'action_url' => $newStatus === 'active' ? route('dashboard') : route('login'),
            'icon' => $newStatus === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle',
            'color' => $newStatus === 'active' ? 'green' : 'red',
            'user_name' => $user->name,
            'changed_by' => auth()->user()->name,
            'changed_at' => now()->format('M d, Y g:i A'),
        ];

        // Send notification to the user
        $this->notificationService->sendToUser($user->id, 'user_status_changed', $data);

        // Also notify super admins about important status changes
        if ($user->role === 'super_admin') {
            $superAdmins = User::where('role', 'super_admin')
                ->where('id', '!=', $user->id)
                ->where('id', '!=', auth()->id())
                ->get();

            foreach ($superAdmins as $superAdmin) {
                $this->notificationService->sendToUser($superAdmin->id, 'super_admin_status_changed', array_merge($data, [
                    'title' => "Super Admin Account {$action}",
                    'message' => "Super Admin account {$user->name} has been {$action} by " . auth()->user()->name,
                ]));
            }
        }
    }

    // ==================== EXISTING METHODS (UPDATED) ====================

    public function toggleProjectPermission(Request $request, User $user)
    {
        // Only allow for managers
        if ($user->role !== 'admin') {
            return redirect()->back()->with('error', 'Project creation permission can only be granted to managers.');
        }

        $newPermission = !$user->can_create_project;
        $user->update(['can_create_project' => $newPermission]);

        // Notify the manager about permission change
        $this->notificationService->sendToUser($user->id, 'project_permission_changed', [
            'title' => $newPermission ? 'Project Creation Permission Granted' : 'Project Creation Permission Revoked',
            'message' => $newPermission
                ? "You can now create projects in the system."
                : "Your project creation permission has been revoked.",
            'action_url' => $newPermission ? route('manager.projects.create') : route('manager.projects.index'),
            'icon' => $newPermission ? 'fas fa-check-circle' : 'fas fa-times-circle',
            'color' => $newPermission ? 'green' : 'red',
        ]);

        $message = $newPermission
            ? "Project creation permission granted to {$user->name}"
            : "Project creation permission revoked from {$user->name}";

        return redirect()->back()->with('success', $message);
    }

    // Show all users
    public function index(Request $request)
    {
        $query = User::with('client');

        // Department filter
        if ($request->has('department') && $request->department != 'all') {
            $query->where('department', $request->department);
        }

        // Role filter
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $users = $query->get();

        // Get unique departments from database (dynamic)
        $departments = User::whereNotNull('department')
                      ->where('department', '!=', '')
                      ->where('department', '!=', 'Not Assigned')
                      ->distinct()
                      ->orderBy('department')
                      ->pluck('department');

        // Get status statistics
        $statusStats = [
            'total' => User::count(),
            'active' => User::active()->count(),
            'inactive' => User::inactive()->count(),
        ];

        return view('admin.users.index', compact('users', 'departments', 'statusStats'));
    }

    // Show create user form
    public function create()
    {
        // Get existing departments for suggestions
        $departments = User::whereNotNull('department')
                      ->where('department', '!=', '')
                      ->where('department', '!=', 'Not Assigned')
                      ->distinct()
                      ->orderBy('department')
                      ->pluck('department');

        return view('admin.users.create', compact('departments'));
    }

    // Store new user created by Super Admin
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'status' => 'required|in:active,inactive', // Add status validation
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'department' => $request->department ?? 'Not Assigned',
            'status' => $request->status,
            'deactivated_at' => $request->status === 'inactive' ? now() : null,
        ];

        // If role is client, create a client record
        if ($request->role === 'client') {
            $client = Client::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'status' => 'active',
            ]);

            $userData['client_id'] = $client->id;
        }

        // Create user
        $user = User::create($userData);

        // Send welcome notification if user is active
        if ($user->status === 'active') {
            $this->notificationService->sendToUser($user->id, 'welcome', [
                'title' => 'Welcome to the System',
                'message' => "Your account has been created. You can now login and access the system.",
                'action_url' => route('login'),
                'icon' => 'fas fa-user-plus',
                'color' => 'blue',
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $client = null;

        // If user has a client role and client_id, fetch the client data
        if ($user->role === 'client' && $user->client_id) {
            $client = Client::find($user->client_id);
        }

        // Get existing departments for suggestions
        $departments = User::whereNotNull('department')
                      ->where('department', '!=', '')
                      ->where('department', '!=', 'Not Assigned')
                      ->distinct()
                      ->orderBy('department')
                      ->pluck('department');

        return view('admin.users.edit', compact('user', 'client', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,admin,manager,user,client',
            'department' => 'nullable|string|max:255',
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive', // Add status validation
        ]);

        $oldStatus = $user->status;

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department' => $request->department ?? 'Not Assigned',
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        // Update deactivated_at based on status
        if ($request->status === 'inactive' && $oldStatus === 'active') {
            $updateData['deactivated_at'] = now();
        } elseif ($request->status === 'active' && $oldStatus === 'inactive') {
            $updateData['deactivated_at'] = null;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Handle client assignment if role changes to client
        if ($request->role === 'client') {
            if ($user->client_id) {
                // Update existing client
                $client = Client::find($user->client_id);
                if ($client) {
                    $client->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'company' => $request->company,
                    ]);
                }
            } else {
                // Create new client
                $client = Client::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'company' => $request->company,
                    'status' => 'active',
                ]);
                $updateData['client_id'] = $client->id;
            }
        } elseif ($request->role !== 'client' && $user->client_id) {
            // Remove client association if role changes from client
            $updateData['client_id'] = null;
        }

        $user->update($updateData);

        // Send status change notification if status changed
        if ($oldStatus !== $request->status) {
            $this->notifyUserStatusChanged($user, $oldStatus, $request->status);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Optional: Delete associated client if this is a client user
        if ($user->isClient() && $user->client) {
            $user->client->delete();
        }

        $user->delete();
        return redirect()->back();
    }

    public function show(User $user)
    {
        $client = null;

        // If user has a client role and client_id, fetch the client data
        if ($user->role === 'client' && $user->client_id) {
            $client = \App\Models\Client::find($user->client_id);
        }

        // Get user activity log (if you have activity logging)
        

        return view('admin.users.show', compact('user', 'client'));
    }

    /**
     * Get user status statistics (for API)
     */
    public function getStatusStats()
    {
        $user = auth()->user();
        
        if ($user->role === 'super_admin') {
            $stats = [
                'total' => User::count(),
                'active' => User::active()->count(),
                'inactive' => User::inactive()->count(),
                'super_admin_active' => User::where('role', 'super_admin')->active()->count(),
                'admin_active' => User::where('role', 'admin')->active()->count(),
                'user_active' => User::where('role', 'user')->active()->count(),
                'client_active' => User::where('role', 'client')->active()->count(),
            ];
        } else {
            // For non-super admins, only show limited stats
            $stats = [
                'total' => User::where('id', '!=', $user->id)->count(),
                'active' => User::where('id', '!=', $user->id)->active()->count(),
                'inactive' => User::where('id', '!=', $user->id)->inactive()->count(),
            ];
        }

        return response()->json($stats);
    }
}