<?php
namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProgramController extends Controller
{
    public function index()
    {
        createLog('akses_menu', 'Program');
        return view('programs.index');
    }

    public function datatable()
    {
        $data = Program::orderBy('id', 'desc')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return formatDate($row->created_at, 'd M Y H:i');
            })
            ->editColumn('photo_url', function ($row) {
                if ($row->photo_url) {
                    return '<button type="button" class="btn btn-sm btn-info btn-preview" data-photo="' . $row->photo_url . '" title="Preview Foto">
                                <i class="ri ri-image-line"></i> Preview
                            </button>';
                }
                return '<span class="badge bg-secondary">Tidak ada foto</span>';
            })
            ->addColumn('action', function ($row) {
                $edit   = '<a href="' . url("master/programs/{$row->id}/edit") . '" class="btn btn-sm btn-warning me-1" title="Edit"><i class="ri ri-edit-line"></i></a>';
                $delete = '<button class="btn btn-sm btn-danger btn-delete" title="Hapus" data-id="' . $row->id . '" data-url="' . url("master/programs/{$row->id}") . '"><i class="ri ri-delete-bin-line"></i></button>';
                return $edit . $delete;
            })
            ->rawColumns(['photo_url', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('programs.form', ['data' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|unique:programs,name|max:100',
            'url'   => 'required|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('programs', 'public');
        }

        $data = Program::create([
            'name'       => $request->name,
            'url'        => $request->url,
            'photo_url'  => $photoPath,
            'created_at' => now(),
        ]);

        createLog('create_program', 'program', null, $data->toJson());

        return redirect('/master/programs')->with('message', 'Program berhasil ditambahkan.')->with('mode', 'success');
    }

    public function edit($id)
    {
        $data = Program::findOrFail($id);
        return view('programs.form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = Program::findOrFail($id);

        $dataInit = $data->toJson();

        $request->validate([
            'name'  => 'required|unique:programs,name,' . $id . ',id|max:100',
            'url'   => 'required|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($data->photo_url && Storage::disk('public')->exists($data->photo_url)) {
                Storage::disk('public')->delete($data->photo_url);
            }
            $photoPath       = $request->file('photo')->store('programs', 'public');
            $data->photo_url = $photoPath;
        }

        $data->name = $request->name;
        $data->url  = $request->url;
        $data->save();
        createLog('update_program', 'program', $dataInit, $data->toJson());
        return redirect('/master/programs')->with('message', 'Program berhasil diperbarui.')->with('mode', 'success');
    }

    public function destroy($id)
    {
        $data = Program::findOrFail($id);

        if ($data->photo_url && Storage::disk('public')->exists($data->photo_url)) {
            Storage::disk('public')->delete($data->photo_url);
        }

        $dataInit = $data->toJson();
        createLog('delete_program', 'program', $dataInit, null);
        $data->delete();

        return redirect('/master/programs')->with('message', 'Program berhasil dihapus.')->with('mode', 'success');
    }
}
