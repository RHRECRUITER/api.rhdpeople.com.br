<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\Admin\ModuleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Module\StoreModuleRequest;
use App\Http\Requests\Admin\Module\UpdateModuleRequest;
use App\Models\Module;
use App\Services\Admin\ModuleManagementService;
use Illuminate\Support\Facades\Response;

class ModuleController extends Controller
{
    public function __construct(
        private readonly ModuleManagementService $moduleService
    ) {}

    public function index()
    {
        $modules = Module::orderBy('label', 'asc')->get();

        return Response::api(['modules' => $modules], 'Módulos recuperados.');
    }

    public function show(string $id)
    {
        $module = Module::findOrFail($id);

        return Response::api(['module' => $module], 'Detalhes do módulo.');
    }

    public function store(StoreModuleRequest $request)
    {
        $dto = ModuleDTO::fromRequest($request);
        $module = $this->moduleService->createModule($dto);

        return Response::api(['module' => $module], 'Módulo criado.', 201);
    }

    public function update(UpdateModuleRequest $request, string $id)
    {
        $dto = ModuleDTO::fromRequest($request);
        $module = $this->moduleService->updateModule($id, $dto);

        return Response::api(['module' => $module], 'Módulo atualizado.');
    }

    public function toggleStatus(string $id)
    {
        $module = Module::findOrFail($id);
        $module->update(['is_active' => ! $module->is_active]);

        $status = $module->is_active ? 'ativado' : 'inativado';

        return Response::api(['module' => $module], "Módulo {$status}.");
    }
}
