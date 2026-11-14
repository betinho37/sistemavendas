
@extends('layouts.app')

@section('title', 'Auditoria do Sistema')

@section('content')

<style>
.auditoria-page {
    color: #e8edf5;
}

.auditoria-page .card-auditoria {
    background: #1e1e1e;
    border: 1px solid #383838;
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 15px;
}

.auditoria-page input,
.auditoria-page select {
    background: #292929;
    color: #fff;
    border: 1px solid #444;
    border-radius: 6px;
    padding: 10px;
    width: 100%;
}

.auditoria-page label {
    display: block;
    margin-bottom: 5px;
}

.auditoria-page .btn-auditoria {
    background: #087fca;
    color: white;
    border: 0;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
}

.auditoria-page .btn-limpar {
    color: #ddd;
    margin-left: 10px;
}

.auditoria-page table {
    width: 100%;
    border-collapse: collapse;
}

.auditoria-page th,
.auditoria-page td {
    padding: 12px;
    border-bottom: 1px solid #383838;
    text-align: left;
    vertical-align: top;
}

.auditoria-page th {
    color: #8fcfff;
}

.auditoria-page pre {
    background: #111;
    color: #ddd;
    padding: 10px;
    border-radius: 6px;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    font-size: 12px;
}

.auditoria-page .mobile-item {
    border: 1px solid #383838;
    padding: 13px;
    border-radius: 8px;
    margin-bottom: 10px;
    background: #222;
}

.auditoria-page .mobile-item p {
    margin: 5px 0;
}

@media (max-width: 768px) {
    .auditoria-desktop {
        display: none;
    }

    .auditoria-page .filtros {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }
}

@media (min-width: 769px) {
    .auditoria-mobile {
        display: none;
    }

    .auditoria-page .filtros {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
}
</style>

<div class="auditoria-page">

    <h2>Auditoria do Sistema</h2>

    <p style="color:#aaa">
        Histórico de atividades realizadas pelos usuários.
    </p>

    <div class="card-auditoria">

        <form method="GET" action="{{ route('auditoria.index') }}">

            <div class="filtros">

                <div>
                    <label>Pesquisar</label>
                    <input type="text" name="busca"
                        value="{{ request('busca') }}"
                        placeholder="Usuário, descrição ou ID">
                </div>

                <div>
                    <label>Usuário</label>
                    <select name="usuario">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}"
                                @selected(request('usuario') == $usuario->id)>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Operação</label>
                    <select name="acao">
                        <option value="">Todas</option>
                        <option value="criacao" @selected(request('acao') == 'criacao')>Criação</option>
                        <option value="alteracao" @selected(request('acao') == 'alteracao')>Alteração</option>
                        <option value="exclusao" @selected(request('acao') == 'exclusao')>Exclusão</option>
                    </select>
                </div>

                <div>
                    <label>Módulo</label>
                    <select name="modulo">
                        <option value="">Todos</option>
                        <option value="contratos" @selected(request('modulo') == 'contratos')>Contratos</option>
                        <option value="pagamentos" @selected(request('modulo') == 'pagamentos')>Pagamentos</option>
                    </select>
                </div>

                <div>
                    <label>Data inicial</label>
                    <input type="date" name="inicio" value="{{ request('inicio') }}">
                </div>

                <div>
                    <label>Data final</label>
                    <input type="date" name="fim" value="{{ request('fim') }}">
                </div>

            </div>

            <div style="margin-top:15px">
                <button class="btn-auditoria" type="submit">
                    Filtrar
                </button>

                <a href="{{ route('auditoria.index') }}" class="btn-limpar">
                    Limpar filtros
                </a>
            </div>

        </form>
    </div>

    <div class="card-auditoria">
        <strong>Registros encontrados: {{ $auditorias->total() }}</strong>
    </div>

    <div class="auditoria-desktop card-auditoria">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Usuário</th>
                    <th>Módulo</th>
                    <th>Operação</th>
                    <th>Registro</th>
                    <th>Detalhes</th>
                </tr>
            </thead>

            <tbody>
                @forelse($auditorias as $item)
                    <tr>
                        <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $item->usuario_nome ?? 'Não identificado' }}</td>
                        <td>{{ ucfirst($item->modulo) }}</td>
                        <td>{{ ucfirst($item->acao) }}</td>
                        <td>#{{ $item->registro_id }}</td>
                        <td>
                            <details>
                                <summary style="cursor:pointer;color:#65bfff">
                                    Ver detalhes
                                </summary>

                                <p>{{ $item->descricao }}</p>

                                @if($item->dados_anteriores)
                                    <strong>Dados anteriores</strong>
                                    <pre>{{ json_encode($item->dados_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif

                                @if($item->dados_novos)
                                    <strong>Dados novos</strong>
                                    <pre>{{ json_encode($item->dados_novos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif

                                <small>IP: {{ $item->ip ?? 'Não registrado' }}</small>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Nenhuma atividade registrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="auditoria-mobile">
        @forelse($auditorias as $item)
            <div class="mobile-item">

                <p><strong>Data:</strong>
                    {{ $item->created_at->format('d/m/Y H:i:s') }}
                </p>

                <p><strong>Usuário:</strong>
                    {{ $item->usuario_nome ?? 'Não identificado' }}
                </p>

                <p><strong>Módulo:</strong>
                    {{ ucfirst($item->modulo) }}
                </p>

                <p><strong>Operação:</strong>
                    {{ ucfirst($item->acao) }}
                </p>

                <p><strong>Registro:</strong> #{{ $item->registro_id }}</p>

                <details>
                    <summary style="color:#65bfff;cursor:pointer">
                        Ver detalhes
                    </summary>

                    <p>{{ $item->descricao }}</p>

                    @if($item->dados_anteriores)
                        <strong>Dados anteriores</strong>
                        <pre>{{ json_encode($item->dados_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif

                    @if($item->dados_novos)
                        <strong>Dados novos</strong>
                        <pre>{{ json_encode($item->dados_novos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @endif

                    <small>IP: {{ $item->ip ?? 'Não registrado' }}</small>
                </details>

            </div>
        @empty
            <p>Nenhuma atividade registrada.</p>
        @endforelse
    </div>

    <div style="margin-top:20px">
        {{ $auditorias->links() }}
    </div>

</div>

@endsection
