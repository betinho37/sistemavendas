
@extends('layouts.app')

@section('title', 'Usuários do Sistema')

@section('content')

<style>
    .usuarios-page {
        color: #e5e7eb;
        padding: 20px 0 35px;
    }

    .usuarios-card,
    .usuarios-panel {
        background: #151b25;
        border: 1px solid #2b3544;
        border-radius: 13px;
    }

    .usuarios-card {
        padding: 18px;
        height: 100%;
    }

    .usuarios-card small {
        color: #9ca3af;
        display: block;
        margin-bottom: 8px;
    }

    .usuarios-card strong {
        font-size: 25px;
        color: #fff;
    }

    .usuarios-panel {
        overflow: hidden;
        margin-bottom: 20px;
    }

    .usuarios-panel-header {
        padding: 17px;
        border-bottom: 1px solid #2b3544;
    }

    .usuarios-page .form-control,
    .usuarios-page .form-select {
        background-color: #0e141d;
        border-color: #354153;
        color: #fff;
    }

    .usuarios-page .form-control::placeholder {
        color: #8792a2;
    }

    .usuarios-page .form-control:focus,
    .usuarios-page .form-select:focus {
        border-color: #3985e8;
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .15);
    }

    .usuarios-page label {
        color: #cbd5e1;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .usuarios-page .table {
        --bs-table-bg: transparent;
        --bs-table-color: #e5e7eb;
        --bs-table-border-color: #2b3544;
        margin: 0;
    }

    .usuarios-page .table thead th {
        background: #1d2633;
        color: #aeb9c8;
        font-size: 12px;
        white-space: nowrap;
        padding: 13px;
    }

    .usuarios-page .table td {
        padding: 14px 13px;
        vertical-align: middle;
    }

    .usuario-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #263b55;
        color: #8ec5ff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .usuario-mobile {
        background: #1b2430;
        border: 1px solid #303d4e;
        border-radius: 11px;
        padding: 15px;
        margin-bottom: 12px;
    }

    .usuario-mobile .linha {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #303d4e;
        font-size: 13px;
    }

    .usuario-mobile .linha:last-child {
        border-bottom: 0;
    }

    .usuario-mobile .linha span:first-child {
        color: #9ca3af;
    }

    .usuarios-page .pagination {
        margin: 0;
        flex-wrap: wrap;
    }

    .usuarios-page .page-link {
        background: #1b2430;
        border-color: #354153;
        color: #dbeafe;
    }

    .usuarios-page .active > .page-link {
        background: #2563eb;
        border-color: #2563eb;
    }

    .usuarios-page .btn {
        white-space: nowrap;
    }

    .usuarios-page .form-check-label {
        color: #e5e7eb;
        font-size: 14px;
    }

    .usuarios-page .form-check-input {
        background-color: #0e141d;
        border-color: #64748b;
    }

    .usuarios-page .form-check-input:checked {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    @media (max-width: 767px) {
        .usuarios-page {
            padding-top: 12px;
        }

        .usuarios-card {
            padding: 14px;
        }

        .usuarios-card strong {
            font-size: 21px;
        }

        .usuario-acoes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .usuario-acoes form {
            margin: 0;
        }

        .usuario-acoes .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid usuarios-page">

    {{-- CABEÇALHO --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="text-info small fw-bold">ADMINISTRAÇÃO MASTER</span>
            <h3 class="mb-1 text-white">Usuários do sistema</h3>
            <p class="text-secondary mb-0">
                Crie, edite, altere permissões e gerencie as contas.
            </p>
        </div>

        <span class="badge bg-primary px-3 py-2">
            <i class="fas fa-shield-alt me-1"></i>
            Área administrativa
        </span>
    </div>

    {{-- MENSAGENS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Não foi possível salvar os dados:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- INDICADORES --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-4">
            <div class="usuarios-card">
                <small><i class="fas fa-users me-2"></i>Total de usuários</small>
                <strong>{{ $totalUsuarios }}</strong>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="usuarios-card">
                <small><i class="fas fa-user-shield me-2"></i>Administradores</small>
                <strong>{{ $totalAdmins }}</strong>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="usuarios-card">
                <small><i class="fas fa-user me-2"></i>Usuários comuns</small>
                <strong>{{ $totalComuns }}</strong>
            </div>
        </div>

    </div>

    {{-- FORMULÁRIO DE CADASTRO / EDIÇÃO --}}
    <div class="usuarios-panel" id="formularioUsuario">

        <div class="usuarios-panel-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="text-white mb-1">
                    @if($editando)
                        <i class="fas fa-user-pen me-2 text-info"></i>
                        Editar usuário #{{ $editando->id }}
                    @else
                        <i class="fas fa-user-plus me-2 text-info"></i>
                        Cadastrar novo usuário
                    @endif
                </h5>

                <small class="text-secondary">
                    Preencha os dados da conta e defina o nível de acesso.
                </small>
            </div>

            @if($editando)
                <a href="{{ route('admin.usuarios.index') }}"
                   class="btn btn-outline-light btn-sm">
                    <i class="fas fa-times me-1"></i>
                    Cancelar edição
                </a>
            @endif
        </div>

        <div class="p-3 p-md-4">

            <form method="POST"
                  action="{{ $editando
                    ? route('admin.usuarios.update', $editando->id)
                    : route('admin.usuarios.store') }}">

                @csrf

                @if($editando)
                    @method('PUT')
                @endif

                <div class="row g-3">

                    <div class="col-12 col-md-6">
                        <label for="name">Nome completo</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $editando->name ?? '') }}"
                            placeholder="Nome do usuário"
                            maxlength="255"
                            required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="email">E-mail de acesso</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $editando->email ?? '') }}"
                            placeholder="usuario@email.com"
                            maxlength="255"
                            required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="password">
                            Senha
                            @if($editando)
                                <small class="text-secondary">(deixe em branco para manter a atual)</small>
                            @endif
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="{{ $editando ? 'Nova senha (opcional)' : 'Mínimo de 8 caracteres' }}"
                            minlength="8"
                            {{ $editando ? '' : 'required' }}
                            autocomplete="new-password">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="password_confirmation">Confirmar senha</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Repita a senha"
                            minlength="8"
                            autocomplete="new-password">
                    </div>

                    <div class="col-12">
                        <input type="hidden" name="is_admin" value="0">

                        <div class="form-check mt-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="is_admin"
                                name="is_admin"
                                value="1"
                                {{ old('is_admin', $editando->is_admin ?? false) ? 'checked' : '' }}>

                            <label class="form-check-label" for="is_admin">
                                <i class="fas fa-user-shield text-warning me-1"></i>
                                Conceder acesso de administrador
                            </label>
                        </div>

                        <small class="text-secondary d-block mt-1">
                            Administradores podem acessar as áreas administrativas do sistema.
                        </small>
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 mt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>
                            {{ $editando ? 'Salvar alterações' : 'Cadastrar usuário' }}
                        </button>

                        @if($editando)
                            <a href="{{ route('admin.usuarios.index') }}"
                               class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- LISTAGEM --}}
    <div class="usuarios-panel">

        <div class="usuarios-panel-header">
            <h5 class="text-white mb-3">
                <i class="fas fa-list me-2 text-info"></i>
                Contas cadastradas
            </h5>

            <form method="GET"
                  action="{{ route('admin.usuarios.index') }}"
                  id="filtroUsuarios">

                <div class="row g-2">

                    <div class="col-12 col-md-8">
                        <input
                            type="search"
                            name="busca"
                            class="form-control"
                            placeholder="Pesquisar por nome ou e-mail..."
                            value="{{ request('busca') }}"
                            autocomplete="off">
                    </div>

                    <div class="col-12 col-md-4">
                        <select name="tipo" class="form-select">
                            <option value="">Todos os tipos</option>
                            <option value="admin" {{ request('tipo') === 'admin' ? 'selected' : '' }}>
                                Administradores
                            </option>
                            <option value="usuario" {{ request('tipo') === 'usuario' ? 'selected' : '' }}>
                                Usuários comuns
                            </option>
                        </select>
                    </div>

                </div>
            </form>
        </div>

        <div class="p-3">

            @forelse($usuarios as $usuario)

                {{-- CARTÃO MOBILE --}}
                <div class="usuario-mobile d-md-none">

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="usuario-avatar">
                            {{ strtoupper(substr($usuario->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0">
                            <strong class="d-block text-white text-break">
                                {{ $usuario->name }}
                            </strong>

                            <small class="text-secondary text-break">
                                {{ $usuario->email }}
                            </small>
                        </div>
                    </div>

                    <div class="linha">
                        <span>ID</span>
                        <strong>#{{ $usuario->id }}</strong>
                    </div>

                    <div class="linha">
                        <span>Tipo de acesso</span>

                        @if($usuario->is_admin)
                            <span class="badge bg-warning text-dark">Administrador</span>
                        @else
                            <span class="badge bg-info text-dark">Usuário comum</span>
                        @endif
                    </div>

                    <div class="linha">
                        <span>Clientes cadastrados</span>
                        <strong>{{ $usuario->clientes_count }}</strong>
                    </div>

                    <div class="linha">
                        <span>Cadastro</span>
                        <strong>{{ $usuario->created_at?->format('d/m/Y') ?? '—' }}</strong>
                    </div>

                    <div class="usuario-acoes mt-3">

                        <a href="{{ route('admin.usuarios.index', array_merge(request()->query(), ['editar' => $usuario->id])) }}#formularioUsuario"
                           class="btn btn-sm btn-outline-info">
                            <i class="fas fa-pen me-1"></i>
                            Editar
                        </a>

                        @if(auth()->id() !== $usuario->id)
                            <form method="POST"
                                  action="{{ route('admin.usuarios.destroy', $usuario->id) }}"
                                  onsubmit="return confirm('Tem certeza que deseja excluir o usuário {{ addslashes($usuario->name) }}? Esta ação não poderá ser desfeita.');">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash me-1"></i>
                                    Excluir
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-sm btn-secondary" disabled>
                                Sua conta
                            </button>
                        @endif

                    </div>
                </div>

            @empty
                <div class="text-center py-5">
                    <i class="fas fa-user-slash fa-2x text-secondary mb-3"></i>
                    <p class="text-secondary mb-0">Nenhum usuário encontrado.</p>
                </div>
            @endforelse

            {{-- TABELA DESKTOP --}}
            @if($usuarios->count())
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>ID</th>
                                <th>Tipo de acesso</th>
                                <th>Clientes</th>
                                <th>Cadastro</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="usuario-avatar">
                                                {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                            </div>

                                            <div>
                                                <strong class="d-block text-white">
                                                    {{ $usuario->name }}
                                                </strong>
                                                <small class="text-secondary">
                                                    {{ $usuario->email }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>#{{ $usuario->id }}</td>

                                    <td>
                                        @if($usuario->is_admin)
                                            <span class="badge bg-warning text-dark">
                                                Administrador
                                            </span>
                                        @else
                                            <span class="badge bg-info text-dark">
                                                Usuário comum
                                            </span>
                                        @endif
                                    </td>

                                    <td>{{ $usuario->clientes_count }}</td>

                                    <td>
                                        {{ $usuario->created_at?->format('d/m/Y H:i') ?? '—' }}
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-end gap-2">

                                            <a href="{{ route('admin.usuarios.index', array_merge(request()->query(), ['editar' => $usuario->id])) }}#formularioUsuario"
                                               class="btn btn-sm btn-outline-info"
                                               title="Editar usuário">
                                                <i class="fas fa-pen"></i>
                                            </a>

                                            @if(auth()->id() !== $usuario->id)
                                                <form method="POST"
                                                      action="{{ route('admin.usuarios.destroy', $usuario->id) }}"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir o usuário {{ addslashes($usuario->name) }}? Esta ação não poderá ser desfeita.');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Excluir usuário">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button"
                                                        class="btn btn-sm btn-secondary"
                                                        disabled
                                                        title="Sua conta master">
                                                    <i class="fas fa-shield-alt"></i>
                                                </button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            @endif

        </div>

        @if($usuarios->hasPages())
            <div class="usuarios-panel-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <small class="text-secondary">
                    Mostrando {{ $usuarios->firstItem() }} a
                    {{ $usuarios->lastItem() }} de
                    {{ $usuarios->total() }} usuários
                </small>

                {{ $usuarios->links() }}
            </div>
        @endif

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filtroUsuarios');

    if (!form) return;

    const busca = form.querySelector('[name="busca"]');
    const tipo = form.querySelector('[name="tipo"]');

    let temporizador;

    tipo.addEventListener('change', function () {
        form.submit();
    });

    busca.addEventListener('input', function () {
        clearTimeout(temporizador);

        temporizador = setTimeout(function () {
            form.submit();
        }, 500);
    });
});
</script>

@endsection
