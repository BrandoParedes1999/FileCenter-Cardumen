<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\RegistroActividad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    private function autorizar(): void
    {
        if (!in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE'])) {
            abort(403);
        }
    }

    public function index(): View
    {
        $this->autorizar();
        $empresas = Empresa::withCount(['usuarios', 'carpetas'])
            ->orderByDesc('es_corporativo')->orderBy('nombre')->get();
        return view('empresas.index', compact('empresas'));
    }

    public function show(Empresa $empresa): View
    {
        $this->autorizar();
        $empresa->loadCount(['usuarios', 'carpetas']);
        $empresa->load(['usuarios' => fn($q) => $q->orderBy('paterno')->take(8)]);
        return view('empresas.show', compact('empresa'));
    }

    public function create(): View
    {
        $this->autorizar();
        return view('empresas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->autorizar();
        $validated = $request->validate([
            'nombre'           => ['required','string','max:245'],
            'siglas'           => ['required','string','max:80','unique:empresas,siglas'],
            'logo'             => ['nullable','string','max:245'],
            'es_corporativo'   => ['boolean'],
            'color_primario'   => ['nullable','string','regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secundario' => ['nullable','string','regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_terciario'  => ['nullable','string','regex:/^#[0-9A-Fa-f]{6}$/'],
            'activo'           => ['boolean'],
        ], ['siglas.unique' => 'Estas siglas ya están en uso.']);

        $empresa = Empresa::create([
            'nombre'           => $validated['nombre'],
            'siglas'           => strtoupper($validated['siglas']),
            'logo'             => $validated['logo'] ?? 'logo_default.png',
            'es_corporativo'   => $validated['es_corporativo'] ?? false,
            'color_primario'   => $validated['color_primario'] ?? null,
            'color_secundario' => $validated['color_secundario'] ?? null,
            'color_terciario'  => $validated['color_terciario'] ?? null,
            'activo'           => $validated['activo'] ?? true,
        ]);

        RegistroActividad::registrar('editar','usuario',Auth::id(),"Creó empresa: {$empresa->nombre}");
        return redirect()->route('empresas.show',$empresa)->with('success',"Empresa \"{$empresa->nombre}\" creada.");
    }

    public function edit(Empresa $empresa): View
    {
        $this->autorizar();
        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        $this->autorizar();
        $validated = $request->validate([
            'nombre'           => ['required','string','max:245'],
            'siglas'           => ['required','string','max:80',Rule::unique('empresas','siglas')->ignore($empresa->id)],
            'logo'             => ['nullable','string','max:245'],
            'es_corporativo'   => ['boolean'],
            'color_primario'   => ['nullable','string','regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secundario' => ['nullable','string','regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_terciario'  => ['nullable','string','regex:/^#[0-9A-Fa-f]{6}$/'],
            'activo'           => ['boolean'],
        ]);

        $empresa->update([
            'nombre'           => $validated['nombre'],
            'siglas'           => strtoupper($validated['siglas']),
            'logo'             => $validated['logo'] ?: $empresa->logo,
            'es_corporativo'   => $validated['es_corporativo'] ?? false,
            'color_primario'   => $validated['color_primario'] ?? null,
            'color_secundario' => $validated['color_secundario'] ?? null,
            'color_terciario'  => $validated['color_terciario'] ?? null,
            'activo'           => $validated['activo'] ?? true,
        ]);

        RegistroActividad::registrar('editar','usuario',Auth::id(),"Editó empresa: {$empresa->nombre}");
        return redirect()->route('empresas.show',$empresa)->with('success','Empresa actualizada.');
    }

    public function toggleActivo(Empresa $empresa): RedirectResponse
    {
        $this->autorizar();
        if ($empresa->es_corporativo) return back()->withErrors(['error'=>'No se puede desactivar la empresa corporativa.']);
        $empresa->update(['activo' => !$empresa->activo]);
        $estado = $empresa->activo ? 'activada' : 'desactivada';
        RegistroActividad::registrar('editar','usuario',Auth::id(),"Empresa {$estado}: {$empresa->nombre}");
        return back()->with('success',"Empresa {$estado} correctamente.");
    }

    public function destroy(Empresa $empresa): RedirectResponse
    {
        $this->autorizar();
        if ($empresa->es_corporativo) return back()->withErrors(['error'=>'No puedes eliminar la empresa corporativa.']);
        if ($empresa->usuarios()->count() > 0) return back()->withErrors(['error'=>'Tiene usuarios asignados.']);
        if ($empresa->carpetas()->count() > 0) return back()->withErrors(['error'=>'Tiene carpetas asignadas.']);
        $nombre = $empresa->nombre;
        $empresa->delete();
        RegistroActividad::registrar('eliminar','usuario',Auth::id(),"Eliminó empresa: {$nombre}");
        return redirect()->route('empresas.index')->with('success',"Empresa \"{$nombre}\" eliminada.");
    }
}