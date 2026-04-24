<?php
namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Program;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function index()
    {
        createLog('akses_menu', 'Menu');
        $programs = Program::all();

        return view('menus.index', compact('programs'));
    }

    public function datatable()
    {
        $data = Menu::with('program')->orderBy('program_id')->orderBy('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('program_name', fn($row) => $row->program->name ?? '-')
            ->addColumn('action', function ($row) {
                $edit   = '<a href="' . url("master/menus/{$row->id}/edit") . '" class="btn btn-sm btn-warning me-1" title="Edit"><i class="ri ri-edit-line"></i></a>';
                $delete = '<button class="btn btn-sm btn-danger btn-delete" title="Hapus" data-id="' . $row->id . '" data-url="' . url("master/menus/{$row->id}") . '"><i class="ri ri-delete-bin-line"></i></button>';
                return $edit . $delete;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $programs = Program::orderBy('name')->pluck('name', 'id');
        return view('menus.form', ['data' => null, 'programs' => $programs]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name'       => 'required|max:100',
        ]);

        createLog('create_menu', 'Menu', null, $request->all());

        Menu::create([
            'program_id' => $request->program_id,
            'name'       => $request->name,
        ]);

        return redirect('/master/menus')->with('message', 'Menu berhasil ditambahkan.')->with('mode', 'success');
    }

    public function edit($id)
    {
        $data     = Menu::findOrFail($id);
        $programs = Program::orderBy('name')->pluck('name', 'id');
        return view('menus.form', compact('data', 'programs'));
    }

    public function update(Request $request, $id)
    {
        $data     = Menu::findOrFail($id);
        $dataInit = $data->toJson();

        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name'       => 'required|max:100',
        ]);

        $data->program_id = $request->program_id;
        $data->name       = $request->name;
        $data->save();

        createLog('update_menu', 'Menu', $dataInit, $data->toJson());

        return redirect('/master/menus')->with('message', 'Menu berhasil diperbarui.')->with('mode', 'success');
    }

    public function destroy($id)
    {
        $data = Menu::findOrFail($id);
        createLog('delete_menu', 'Menu', $data->id, $data->toJson());
        $data->delete();
        return redirect('/master/menus')->with('message', 'Menu berhasil dihapus.')->with('mode', 'success');
    }
}
