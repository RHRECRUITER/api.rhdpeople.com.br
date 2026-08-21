<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\Admin\AddOnDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Addon\StoreAddonRequest;
use App\Http\Requests\Admin\Addon\UpdateAddonRequest;
use App\Models\AddOn;
use App\Services\Admin\AddonManagementService;
use Illuminate\Support\Facades\Response;

class AddonController extends Controller
{
    public function __construct(
        private readonly AddonManagementService $addonService
    ) {
    }

    public function index()
    {
        $addons = AddOn::orderBy('label', 'asc')->get();

        return Response::api(['add_ons' => $addons], 'Add-ons recuperados com sucesso.');
    }

    public function show(string $id)
    {
        $addon = AddOn::findOrFail($id);

        return Response::api(['add_on' => $addon], 'Detalhes do add-on.');
    }

    public function store(StoreAddonRequest $request)
    {
        // Certifique-se de ter renomeado a classe para AddOnDTO com "A" maiúsculo
        $dto   = AddOnDTO::fromRequest($request);
        $addon = $this->addonService->createAddon($dto);

        return Response::api(['add_on' => $addon], 'Add-on criado com sucesso.', 201);
    }

    public function update(UpdateAddonRequest $request, string $id)
    {
        $dto   = AddOnDTO::fromRequest($request);
        // Lembre-se de corrigir o nome do método lá no Service para updateAddOn!
        $addon = $this->addonService->updateAddOn($id, $dto);

        return Response::api(['add_on' => $addon], 'Add-on atualizado com sucesso.');
    }

    public function toggleStatus(string $id)
    {
        $addon = AddOn::findOrFail($id);
        $addon->update(['is_active' => ! $addon->is_active]);

        $status = $addon->is_active ? 'ativado' : 'inativado';

        return Response::api(['add_on' => $addon], "Add-on {$status}.");
    }
}
