<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerController extends Controller
{
    public function index(): View
    {
        $workers = Worker::with('user')->latest()->get();
        return view('workers.index', compact('workers'));
    }

    public function create(): View
    {
        $users = User::where('role', 'worker')->doesntHave('worker')->get();
        return view('workers.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'mobile'    => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'user_id'   => ['nullable', 'exists:users,id'],
        ]);

        Worker::create($data);

        return redirect()->route('workers.index')
            ->with('success', 'Worker added successfully.');
    }

    public function edit(Worker $worker): View
    {
        $users = User::where('role', 'worker')
            ->where(function ($q) use ($worker) {
                $q->doesntHave('worker')->orWhere('id', $worker->user_id);
            })->get();

        return view('workers.edit', compact('worker', 'users'));
    }

    public function update(Request $request, Worker $worker): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'mobile'    => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'user_id'   => ['nullable', 'exists:users,id'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $worker->update($data);

        return redirect()->route('workers.index')
            ->with('success', 'Worker updated.');
    }

    public function destroy(Worker $worker): RedirectResponse
    {
        $worker->delete();
        return redirect()->route('workers.index')->with('success', 'Worker removed.');
    }
}
