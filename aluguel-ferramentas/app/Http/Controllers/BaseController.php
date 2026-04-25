<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SetorPermissionService;

abstract class BaseController extends Controller
{
    protected $setorPermission;
    protected $model;
    protected $viewPrefix;
    protected $routePrefix;
    
    public function __construct()
    {
        $this->setorPermission = app(SetorPermissionService::class);
        // REMOVA esta linha do construtor
        // $this->middleware('auth');
    }
    
    /**
     * Aplica filtro de setor na query
     */
    protected function applySetorFilter($query)
    {
        $user = Auth::user();
        
        // Se não tem usuário logado ou é admin, retorna tudo
        if (!$user || $user->role === 'admin') {
            return $query;
        }
        
        // Se a model tem setor_id, aplica o filtro
        if ($this->modelHasSetorId()) {
            return $query->where('setor_id', $user->setor_id);
        }
        
        return $query;
    }
    
    /**
     * Verifica se a model tem campo setor_id
     */
    protected function modelHasSetorId()
    {
        $model = new $this->model;
        return in_array('setor_id', $model->getFillable());
    }
    
    /**
     * Retorna dados com paginação e filtros
     */
    public function index(Request $request)
    {
        $query = $this->model::query();
        
        // Aplica filtro de setor automaticamente
        $query = $this->applySetorFilter($query);
        
        // Aplica filtros customizados (se existirem)
        $query = $this->applyFilters($query, $request);
        
        $perPage = $request->get('per_page', 10);
        $items = $query->paginate($perPage);
        
        $extraData = $this->getIndexData($request);
        
        return view("{$this->viewPrefix}.index", array_merge([
            'items' => $items,
        ], $extraData));
    }
    
    /**
     * Store com validação automática
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->getValidationRules());
        
        // Verifica permissão de setor
        if (!$this->setorPermission->canOperateInSetor($validated['setor_id'] ?? null)) {
            return redirect()->back()
                ->with('error', 'Você não tem permissão para este setor!')
                ->withInput();
        }
        
        $item = $this->model::create($validated);
        
        return redirect()->route("{$this->routePrefix}.index")
            ->with('success', $this->getSuccessMessage('created'));
    }
    
    /**
     * Update com verificação
     */
    public function update(Request $request, $id)
    {
        $item = $this->model::findOrFail($id);
        
        if (!$this->setorPermission->userHasAccessTo($item)) {
            return redirect()->back()
                ->with('error', 'Sem permissão para este item!');
        }
        
        $validated = $request->validate($this->getValidationRules());
        
        $item->update($validated);
        
        return redirect()->route("{$this->routePrefix}.index")
            ->with('success', $this->getSuccessMessage('updated'));
    }
    
    /**
     * Destroy com verificação
     */
    public function destroy($id)
    {
        $item = $this->model::findOrFail($id);
        
        if (!$this->setorPermission->userHasAccessTo($item)) {
            return redirect()->back()
                ->with('error', 'Sem permissão para excluir!');
        }
        
        $item->delete();
        
        return redirect()->route("{$this->routePrefix}.index")
            ->with('success', $this->getSuccessMessage('deleted'));
    }
    
    // Métodos que as classes filhas DEVEM implementar
    abstract protected function getValidationRules(): array;
    abstract protected function applyFilters($query, Request $request);
    
    // Métodos opcionais
    protected function getIndexData(Request $request)
    {
        return [];
    }
    
    protected function getSuccessMessage(string $action): string
    {
        $messages = [
            'created' => 'Cadastrado com sucesso!',
            'updated' => 'Atualizado com sucesso!',
            'deleted' => 'Removido com sucesso!',
        ];
        return $messages[$action] ?? 'Operação realizada!';
    }
}