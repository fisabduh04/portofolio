<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CobaController extends Controller
{
    public function tableView()
    {
        return view('coba.coba');
    }

    

    public function index(Request $req)
    {
        $q = User::query();

        if ($req->has('search')) {
            $term = $req->search;
            $q->where(fn($w) =>
                $w->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
            );
        }

        if ($req->has('sort') && in_array($req->sort, ['name','email','created_at'])) {
            $order = $req->order === 'desc' ? 'desc' : 'asc';
            $q->orderBy($req->sort, $order);
        }

        $perPage = $req->per_page ?? 10;
        $u = $q->paginate($perPage);

        return response()->json([
            'data' => $u->items(),
            'current_page' => $u->currentPage(),
            'last_page' => $u->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email'));

        return response()->json($user);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'User deleted successfully']);
    }





    public function tampil3()
    {
        $users= User::all();
        return view('coba.coba3',compact('users'));
    }

    public function tampil2()
    {
        return view('coba.coba2');
    }
}
