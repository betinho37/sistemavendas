
@extends('layouts.app')

@section('title', 'Portal do Cliente')

@section('content')
<div class="container">
    <div class="card bg-dark text-light border-secondary shadow p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h4 class="mb-0">Portal do Cliente</h4>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-light">
                Voltar
            </a>
        </div>

        <h5>{{ $cliente->nome }}</h5>
        <p class="text-secondary">{{ $cliente->fone }}</p>

        <hr class="border-secondary">

        @if($cliente->portal_ativo && $cliente->portal_token)
            <div class="alert alert-success">
                Portal ativo
            </div>

            <label class="form-label">Link permanente do cliente</label>
            <div class="input-group mb-3">
                <input
                    type="text"
                    id="portalLink"
                    class="form-control"
                    readonly
                    value="{{ url('/portal/'.$cliente->portal_token) }}"
                >
                <button class="btn btn-primary" type="button" onclick="copiarPortal()">
                    Copiar
                </button>
            </div>

            <form method="POST"
                  action="{{ route('clientes.portal.revogar', $cliente) }}"
                  onsubmit="return confirm('Deseja revogar o acesso deste cliente?')">
                @csrf
                <button class="btn btn-danger w-100">
                    Revogar acesso
                </button>
            </form>
        @else
            <div class="alert alert-warning">
                Este cliente ainda não possui portal ativo.
            </div>

            <form method="POST" action="{{ route('clientes.portal.gerar', $cliente) }}">
                @csrf
                <button class="btn btn-success w-100">
                    Gerar link permanente
                </button>
            </form>
        @endif
    </div>
</div>

<script>
function copiarPortal() {
    const campo = document.getElementById('portalLink');
    campo.select();
    campo.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(campo.value)
        .then(() => alert('Link copiado!'))
        .catch(() => alert('Não foi possível copiar o link.'));
}
</script>
@endsection
