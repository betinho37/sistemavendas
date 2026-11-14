
@extends('layouts.app')

@section('title', 'Lista de Clientes')

@section('content')
<style>
    .clientes-page { color: #e8edf5; }
    .clientes-page .card { background: #1e1e1e; border: 1px solid #333; }
    .clientes-header { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
    .clientes-header h2 { margin:0; font-size:1.5rem; font-weight:700; }
    .clientes-busca { display:flex; gap:8px; margin-bottom:18px; }
    .clientes-busca input { min-width:0; }
    .clientes-desktop { overflow-x:auto; border-radius:10px; }
    .clientes-desktop table { min-width:850px; margin-bottom:0; }
    .clientes-desktop td, .clientes-desktop th { vertical-align:middle; }
    .foto-cliente { width:55px; height:55px; object-fit:cover; border-radius:8px; cursor:pointer; }
    .cliente-mobile { display:none; }
    .cliente-card { background:#252525; border:1px solid #393939; border-radius:12px; padding:15px; margin-bottom:13px; }
    .cliente-card-header { display:flex; align-items:center; gap:12px; margin-bottom:15px; min-width:0; }
    .cliente-card-header .foto-cliente, .cliente-card-header .sem-foto { width:58px; height:58px; flex-shrink:0; }
    .cliente-card-header .sem-foto { border-radius:8px; background:#353535; display:flex; align-items:center; justify-content:center; font-size:24px; }
    .cliente-card-nome { color:#fff; font-size:1rem; font-weight:700; overflow-wrap:anywhere; }
    .cliente-card-info { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:13px; }
    .cliente-card-info .item { min-width:0; }
    .cliente-card-info .label { display:block; font-size:.75rem; color:#999; margin-bottom:4px; }
    .cliente-card-info .valor { display:block; font-size:.9rem; color:#eee; overflow-wrap:anywhere; }
    .cliente-card-acoes { display:flex; gap:8px; margin-top:15px; padding-top:13px; border-top:1px solid #444; }
    .cliente-card-acoes > * { flex:1; min-width:0; }
    .cliente-card-acoes .btn { width:100%; min-height:42px; }
    .cliente-card-documentos { margin-top:13px; }
    .cliente-card-documentos .btn { min-height:40px; }
    .clientes-paginacao { display:flex; flex-direction:column; align-items:center; gap:14px; padding-top:20px; border-top:1px solid #383838; }
    .paginacao-info { color:#bdbdbd; font-size:13px; text-align:center; }
    .clientes-paginacao .pagination { display:flex; flex-wrap:wrap; justify-content:center; gap:5px; }
    .clientes-paginacao .page-item { margin:0; }
    .clientes-paginacao .page-link { background-color:#252525; border:1px solid #444; color:#e5e5e5; border-radius:6px; min-width:38px; min-height:38px; display:flex; align-items:center; justify-content:center; padding:8px 12px; text-decoration:none; }
    .clientes-paginacao .page-item.active .page-link { background-color:#0d6efd; border-color:#0d6efd; color:#fff; font-weight:600; }
    .clientes-paginacao .page-item.disabled .page-link { background-color:#1b1b1b; border-color:#333; color:#686868; pointer-events:none; }

    .clientes-loading { opacity:.5; pointer-events:none; }

    @media (max-width:767px) {
        .clientes-page { padding-left:2px; padding-right:2px; }
        .clientes-page .card { padding:14px !important; }
        .clientes-header { align-items:stretch; }
        .clientes-header h2 { font-size:1.3rem; }
        .clientes-header .btn { width:100%; min-height:44px; }
        .clientes-busca { flex-direction:column; }
        .clientes-desktop { display:none; }
        .cliente-mobile { display:block; }
        .clientes-paginacao .page-link { min-width:34px; min-height:34px; padding:6px 9px; font-size:13px; }
        .clientes-paginacao .pagination { gap:3px; }
    }

    @media (max-width:360px) {
        .cliente-card-acoes { flex-wrap:wrap; }
        .cliente-card-acoes > * { flex-basis:100%; }
    }
</style>

<div class="container clientes-page">
    <div class="card shadow-lg p-4 rounded">

        <div class="clientes-header">
            <h2 class="text-center text-light">Lista de Clientes</h2>
            <a href="{{ route('clientes.create') }}" class="btn btn-primary">➕ Novo Cliente</a>
        </div>

        <form class="clientes-busca" id="formBuscaClientes" method="GET" action="{{ route('clientes.index') }}">
            <input
                type="text"
                name="busca"
                id="buscaClientes"
                class="form-control bg-dark text-light border-secondary"
                placeholder="Pesquisar por nome, telefone, CPF ou e-mail..."
                value="{{ $busca ?? request('busca') }}"
                autocomplete="off"
            >

            <select name="ordem" id="ordemClientes" class="form-select bg-dark text-light border-secondary">
                <option value="recentes" {{ ($ordem ?? 'recentes') === 'recentes' ? 'selected' : '' }}>Mais recentes</option>
                <option value="nome_asc" {{ ($ordem ?? '') === 'nome_asc' ? 'selected' : '' }}>Nome: A–Z</option>
                <option value="nome_desc" {{ ($ordem ?? '') === 'nome_desc' ? 'selected' : '' }}>Nome: Z–A</option>
            </select>

            <a href="{{ route('clientes.index') }}" id="limparBuscaClientes" class="btn btn-outline-secondary">Limpar</a>
        </form>

        <div id="clientesResultados">

            {{-- TABELA DESKTOP --}}
            <div class="clientes-desktop">
                <table class="table table-dark table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>CPF</th>
                            <th>E-mail</th>
                            <th>Documentos</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                        <tr>
                            <td>
                                @if($cliente->foto)
                                    <img src="{{ asset('storage/'.$cliente->foto) }}"
                                         class="foto-cliente"
                                         data-nome="{{ $cliente->nome }}"
                                         data-foto="{{ asset('storage/'.$cliente->foto) }}">
                                @endif
                            </td>
                            <td>{{ $cliente->nome }}</td>
                            <td>{{ $cliente->fone }}</td>
                            <td>{{ $cliente->cpf }}</td>
                            <td>{{ $cliente->email }}</td>
                            <td>
                                @if($cliente->documentos->count())
                                    <button type="button" class="btn btn-outline-info btn-sm btn-documentos"
                                            data-cliente="{{ $cliente->id }}"
                                            data-nome="{{ $cliente->nome }}"
                                            data-documentos='@json($cliente->documentos)'>
                                        Ver ({{ $cliente->documentos->count() }})
                                    </button>
                                @else
                                    <span class="text-muted">Nenhum</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning btn-sm">✏️ Editar</a>
                                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja excluir este cliente? Todos os dados serão removidos!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️ Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-warning">Nenhum cliente encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- CARTÕES MOBILE --}}
            <div class="cliente-mobile">
                @forelse($clientes as $cliente)
                    <div class="cliente-card">
                        <div class="cliente-card-header">
                            @if($cliente->foto)
                                <img src="{{ asset('storage/'.$cliente->foto) }}"
                                     class="foto-cliente"
                                     data-nome="{{ $cliente->nome }}"
                                     data-foto="{{ asset('storage/'.$cliente->foto) }}">
                            @else
                                <div class="sem-foto">👤</div>
                            @endif

                            <div class="cliente-card-nome">{{ $cliente->nome }}</div>
                        </div>

                        <div class="cliente-card-info">
                            <div class="item">
                                <span class="label">Telefone</span>
                                <span class="valor">{{ $cliente->fone ?: 'Não informado' }}</span>
                            </div>
                            <div class="item">
                                <span class="label">CPF</span>
                                <span class="valor">{{ $cliente->cpf ?: 'Não informado' }}</span>
                            </div>
                            <div class="item" style="grid-column:1 / -1;">
                                <span class="label">E-mail</span>
                                <span class="valor">{{ $cliente->email ?: 'Não informado' }}</span>
                            </div>
                        </div>

                        <div class="cliente-card-documentos">
                            @if($cliente->documentos->count())
                                <button type="button" class="btn btn-outline-info btn-sm btn-documentos w-100"
                                        data-cliente="{{ $cliente->id }}"
                                        data-nome="{{ $cliente->nome }}"
                                        data-documentos='@json($cliente->documentos)'>
                                    📄 Ver documentos ({{ $cliente->documentos->count() }})
                                </button>
                            @else
                                <span class="text-muted small">Nenhum documento cadastrado</span>
                            @endif
                        </div>

                        <div class="cliente-card-acoes">
                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning btn-sm">✏️ Editar</a>
                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este cliente? Todos os dados serão removidos!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">🗑️ Excluir</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-warning py-3">Nenhum cliente encontrado.</div>
                @endforelse
            </div>

            {{-- PAGINAÇÃO --}}
            @if ($clientes->hasPages())
                <div class="clientes-paginacao mt-4">
                    <div class="paginacao-info">
                        Página {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}
                        <span class="mx-2">|</span>
                        {{ $clientes->total() }} clientes encontrados
                    </div>

                    <nav aria-label="Paginação de clientes">
                        <ul class="pagination pagination-dark justify-content-center mb-0">
                            @if ($clientes->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">‹ Anterior</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $clientes->previousPageUrl() }}">‹ Anterior</a></li>
                            @endif

                            @if ($clientes->currentPage() > 3)
                                <li class="page-item"><a class="page-link" href="{{ $clientes->url(1) }}">1</a></li>
                                @if ($clientes->currentPage() > 4)
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                @endif
                            @endif

                            @foreach ($clientes->getUrlRange(max(1, $clientes->currentPage()-2), min($clientes->lastPage(), $clientes->currentPage()+2)) as $pagina => $url)
                                <li class="page-item {{ $pagina == $clientes->currentPage() ? 'active' : '' }}">
                                    @if ($pagina == $clientes->currentPage())
                                        <span class="page-link">{{ $pagina }}</span>
                                    @else
                                        <a class="page-link" href="{{ $url }}">{{ $pagina }}</a>
                                    @endif
                                </li>
                            @endforeach

                            @if ($clientes->currentPage() < $clientes->lastPage()-2)
                                @if ($clientes->currentPage() < $clientes->lastPage()-3)
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                @endif
                                <li class="page-item"><a class="page-link" href="{{ $clientes->url($clientes->lastPage()) }}">{{ $clientes->lastPage() }}</a></li>
                            @endif

                            @if ($clientes->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $clientes->nextPageUrl() }}">Próximo ›</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">Próximo ›</span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- MODAL FOTO --}}
<div class="modal fade" id="fotoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 id="fotoModalTitle"></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fotoModalImg" style="max-width:100%;max-height:70vh;border-radius:10px">
            </div>
        </div>
    </div>
</div>

{{-- MODAL DOCUMENTOS --}}
<div class="modal fade" id="docsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 id="docsModalTitle"></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="docSearch" class="form-control bg-dark text-light mb-3" placeholder="Buscar documento...">
                <div id="docsContent"></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW --}}
<div class="modal fade" id="docPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 id="docPreviewTitle"></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="docPreviewContent"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const fotoModal = new bootstrap.Modal(document.getElementById('fotoModal'));
    const docsModal = new bootstrap.Modal(document.getElementById('docsModal'));
    const previewModal = new bootstrap.Modal(document.getElementById('docPreviewModal'));

    const form = document.getElementById('formBuscaClientes');
    const campoBusca = document.getElementById('buscaClientes');
    const campoOrdem = document.getElementById('ordemClientes');
    const resultados = document.getElementById('clientesResultados');
    const limpar = document.getElementById('limparBuscaClientes');

    let timerBusca = null;
    let controllerBusca = null;
    let documentos = [];

    async function carregarClientes(url, atualizarHistorico = true) {
        if (controllerBusca) controllerBusca.abort();
        controllerBusca = new AbortController();

        resultados.classList.add('clientes-loading');

        try {
            const resposta = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: controllerBusca.signal
            });

            if (!resposta.ok) throw new Error('Falha ao carregar clientes.');

            const html = await resposta.text();
            const pagina = new DOMParser().parseFromString(html, 'text/html');
            const novosResultados = pagina.querySelector('#clientesResultados');

            if (!novosResultados) throw new Error('Não foi possível atualizar a lista.');

            resultados.innerHTML = novosResultados.innerHTML;

            if (atualizarHistorico) {
                history.pushState({}, '', url);
            }

            resultados.classList.remove('clientes-loading');

        } catch (erro) {
            if (erro.name !== 'AbortError') {
                console.error(erro);
                resultados.classList.remove('clientes-loading');
            }
        }
    }

    function atualizarBusca() {
        const parametros = new URLSearchParams();
        const busca = campoBusca.value.trim();
        const ordem = campoOrdem.value;

        if (busca) parametros.set('busca', busca);
        if (ordem && ordem !== 'recentes') parametros.set('ordem', ordem);

        const url = form.action + (parametros.toString() ? '?' + parametros.toString() : '');
        carregarClientes(url);
    }

    campoBusca.addEventListener('input', () => {
        clearTimeout(timerBusca);
        timerBusca = setTimeout(atualizarBusca, 400);
    });

    campoOrdem.addEventListener('change', atualizarBusca);

    form.addEventListener('submit', event => {
        event.preventDefault();
        clearTimeout(timerBusca);
        atualizarBusca();
    });

    limpar.addEventListener('click', event => {
        event.preventDefault();
        campoBusca.value = '';
        campoOrdem.value = 'recentes';
        carregarClientes(limpar.href);
    });

    document.addEventListener('click', event => {
        const linkPagina = event.target.closest('#clientesResultados .clientes-paginacao a');

        if (linkPagina) {
            event.preventDefault();
            carregarClientes(linkPagina.href);
            return;
        }

        const foto = event.target.closest('.foto-cliente');

        if (foto) {
            document.getElementById('fotoModalTitle').innerText = foto.dataset.nome || '';
            document.getElementById('fotoModalImg').src = foto.dataset.foto;
            fotoModal.show();
            return;
        }

        const botaoDocs = event.target.closest('.btn-documentos');

        if (botaoDocs) {
            try {
                documentos = JSON.parse(botaoDocs.dataset.documentos || '[]');
                document.getElementById('docsModalTitle').innerText = `Documentos de ${botaoDocs.dataset.nome}`;
                document.getElementById('docSearch').value = '';
                renderDocs();
                docsModal.show();
            } catch (erro) {
                console.error(erro);
                alert('Não foi possível carregar os documentos.');
            }
        }
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);
        campoBusca.value = url.searchParams.get('busca') || '';
        campoOrdem.value = url.searchParams.get('ordem') || 'recentes';
        carregarClientes(window.location.href, false);
    });

    document.getElementById('docSearch').addEventListener('input', renderDocs);

    function renderDocs() {
        const busca = document.getElementById('docSearch').value.toLowerCase();
        const area = document.getElementById('docsContent');
        area.innerHTML = '';

        const grupos = { image: [], pdf: [], other: [] };

        documentos.filter(d => (d.nome || '').toLowerCase().includes(busca)).forEach(d => {
            const ext = (d.arquivo || '').split('.').pop().toLowerCase();

            if (['jpg', 'png', 'jpeg', 'gif', 'webp'].includes(ext)) grupos.image.push(d);
            else if (ext === 'pdf') grupos.pdf.push(d);
            else grupos.other.push(d);
        });

        criarGrupo('Imagens', grupos.image);
        criarGrupo('PDFs', grupos.pdf);
        criarGrupo('Outros', grupos.other);

        if (!area.innerHTML) area.innerHTML = '<p class="text-muted">Nenhum documento encontrado.</p>';
    }

    function criarGrupo(titulo, docs) {
        if (!docs.length) return;

        const area = document.getElementById('docsContent');
        const heading = document.createElement('h6');
        heading.className = 'text-info mt-3';
        heading.textContent = titulo;
        area.appendChild(heading);

        docs.forEach(doc => {
            const linha = document.createElement('div');
            linha.className = 'd-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom py-2';

            const nome = document.createElement('span');
            nome.className = 'text-break';
            nome.style.cursor = 'pointer';
            nome.textContent = '📄 ' + doc.nome;
            nome.addEventListener('click', () => abrirDoc(doc.nome, doc.arquivo));

            const acoes = document.createElement('div');
            const excluirBtn = document.createElement('button');
            excluirBtn.type = 'button';
            excluirBtn.className = 'btn btn-sm btn-outline-danger';
            excluirBtn.textContent = '🗑️';
            excluirBtn.addEventListener('click', () => excluir(doc.id));

            acoes.appendChild(excluirBtn);
            linha.appendChild(nome);
            linha.appendChild(acoes);
            area.appendChild(linha);
        });
    }

    function abrirDoc(nome, arquivo) {
        document.getElementById('docPreviewTitle').innerText = nome;
        const area = document.getElementById('docPreviewContent');
        area.innerHTML = '';

        const url = '/storage/' + arquivo;

        if (arquivo.toLowerCase().endsWith('.pdf')) {
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '75vh';
            iframe.style.border = '0';
            area.appendChild(iframe);
        } else {
            const img = document.createElement('img');
            img.src = url;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '75vh';
            area.appendChild(img);
        }

        previewModal.show();
    }

    async function excluir(id) {
        if (!confirm('Excluir documento?')) return;

        try {
            const res = await fetch(`/clientes/documentos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                alert(data.message ?? 'Erro ao excluir documento');
                return;
            }

            documentos = documentos.filter(d => d.id != id);
            docsModal.hide();
            window.location.reload();

        } catch (error) {
            console.error(error);
            alert('Erro de conexão ao excluir');
        }
    }
});
</script>
@endsection
