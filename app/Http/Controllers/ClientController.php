<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class ClientController extends Controller
{
    public function index(): InertiaResponse
    {
        $clients = Client::query()->latest()->paginate(20);

        return Inertia::render('clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->validated());

        return to_route('clients.index')->with('status', 'Client created successfully.');
    }

    public function show(Client $client): InertiaResponse
    {
        return Inertia::render('clients/Show', [
            'client' => $client->only('id', 'name', 'email', 'phone'),
        ]);
    }

    public function edit(Client $client): InertiaResponse
    {
        return Inertia::render('clients/Edit', [
            'client' => $client->only('id', 'name', 'email', 'phone'),
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return to_route('clients.index')->with('status', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return to_route('clients.index')->with('status', 'Client deleted successfully.');
    }
}
