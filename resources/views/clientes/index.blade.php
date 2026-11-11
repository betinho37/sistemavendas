@extends('layouts.app')

@section('title', 'Lista de Clientes')

@section('content')
<div class="container">
    <div class="card shadow-lg p-4 rounded" style="background:#1e1e1e;border:1px solid #333;">
        <h2 class="text-center text-light mb-4">Lista de Clientes</h2>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary mb-3">Novo Cliente</a>
        <br>
        <form class="mb-3 d-flex">
            <input type="text" class="form-control me-2 bg-dark text-light border-0"
                   placeholder="Pesquisar...">
            <button class="btn btn-primary">Pesquisar</button>
        </form>

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
                @foreach($clientes as $cliente)
                <tr>
                    <td>
                        @if($cliente->foto)
                            <img src="{{ asset('storage/'.$cliente->foto) }}"
                                 class="foto-cliente"
                                 data-nome="{{ $cliente->nome }}"
                                 data-foto="{{ asset('storage/'.$cliente->foto) }}"
                                 style="width:55px;height:55px;object-fit:cover;border-radius:8px;cursor:pointer">
                        @endif
                    </td>

                    <td>{{ $cliente->nome }}</td>
                    <td>{{ $cliente->fone }}</td>
                    <td>{{ $cliente->cpf }}</td>
                    <td>{{ $cliente->email }}</td>

                    <td>
                        @if($cliente->documentos->count())
                            <button class="btn btn-outline-info btn-sm btn-documentos"
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
                    <a href="{{ route('clientes.edit', $cliente) }}"
                    class="btn btn-warning btn-sm">
                        ✏️ Editar
                    </a>

                    <form action="{{ route('clientes.destroy', $cliente) }}"
                        method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir este cliente? Todos os dados serão removidos!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            🗑️ Excluir
                        </button>
                    </form>
                </td>

                </tr>
                @endforeach
            </tbody>
        </table>


{{-- PAGINAÇÃO DOS CLIENTES --}}
@if ($clientes->hasPages())
    <div class="clientes-paginacao mt-4">
        <div class="paginacao-info">
            Página {{ $clientes->currentPage() }}
            de {{ $clientes->lastPage() }}
            <span class="mx-2">|</span>
            {{ $clientes->total() }} clientes cadastrados
        </div>

        <nav aria-label="Paginação de clientes">
            <ul class="pagination pagination-dark justify-content-center mb-0">

                {{-- ANTERIOR --}}
                @if ($clientes->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">‹ Anterior</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link"
                           href="{{ $clientes->previousPageUrl() }}"
                           rel="prev">
                            ‹ Anterior
                        </a>
                    </li>
                @endif

                {{-- PRIMEIRA PÁGINA --}}
                @if ($clientes->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link"
                           href="{{ $clientes->url(1) }}">1</a>
                    </li>

                    @if ($clientes->currentPage() > 4)
                        <li class="page-item disabled">
                            <span class="page-link">…</span>
                        </li>
                    @endif
                @endif

                {{-- PÁGINAS PRÓXIMAS DA ATUAL --}}
                @foreach (
                    $clientes->getUrlRange(
                        max(1, $clientes->currentPage() - 2),
                        min($clientes->lastPage(), $clientes->currentPage() + 2)
                    ) as $pagina => $url
                )
                    <li class="page-item {{ $pagina == $clientes->currentPage() ? 'active' : '' }}">
                        @if ($pagina == $clientes->currentPage())
                            <span class="page-link" aria-current="page">
                                {{ $pagina }}
                            </span>
                        @else
                            <a class="page-link" href="{{ $url }}">
                                {{ $pagina }}
                            </a>
                        @endif
                    </li>
                @endforeach

                {{-- ÚLTIMA PÁGINA --}}
                @if ($clientes->currentPage() < $clientes->lastPage() - 2)
                    @if ($clientes->currentPage() < $clientes->lastPage() - 3)
                        <li class="page-item disabled">
                            <span class="page-link">…</span>
                        </li>
                    @endif

                    <li class="page-item">
                        <a class="page-link"
                           href="{{ $clientes->url($clientes->lastPage()) }}">
                            {{ $clientes->lastPage() }}
                        </a>
                    </li>
                @endif

                {{-- PRÓXIMO --}}
                @if ($clientes->hasMorePages())
                    <li class="page-item">
                        <a class="page-link"
                           href="{{ $clientes->nextPageUrl() }}"
                           rel="next">
                            Próximo ›
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Próximo ›</span>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
@endif

<style>
    .clientes-paginacao {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        padding-top: 20px;
        border-top: 1px solid #383838;
    }

    .paginacao-info {
        color: #bdbdbd;
        font-size: 13px;
        text-align: center;
    }

    .clientes-paginacao .pagination {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }

    .clientes-paginacao .page-item {
        margin: 0;
    }

    .clientes-paginacao .page-link {
        background-color: #252525;
        border: 1px solid #444;
        color: #e5e5e5;
        border-radius: 6px;
        min-width: 38px;
        min-height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        text-decoration: none;
        transition: background-color .2s, border-color .2s;
    }

    .clientes-paginacao .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        font-weight: 600;
    }

    .clientes-paginacao .page-item:not(.disabled):not(.active) .page-link:hover {
        background-color: #383838;
        border-color: #0d6efd;
        color: #fff;
    }

    .clientes-paginacao .page-item.disabled .page-link {
        background-color: #1b1b1b;
        border-color: #333;
        color: #686868;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 576px) {
        .clientes-paginacao .page-link {
            min-width: 34px;
            min-height: 34px;
            padding: 6px 9px;
            font-size: 13px;
        }

        .clientes-paginacao .pagination {
            gap: 3px;
        }
    }
</style>
    </div>
</div>

{{-- MODAL FOTO --}}
<div class="modal fade" id="fotoModal">
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
<div class="modal fade" id="docsModal">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 id="docsModalTitle"></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="text" id="docSearch"
                       class="form-control bg-dark text-light mb-3"
                       placeholder="Buscar documento...">

                <div id="docsContent"></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW --}}
<div class="modal fade" id="docPreviewModal">
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

    const fotoModal = new bootstrap.Modal('#fotoModal');
    const docsModal = new bootstrap.Modal('#docsModal');
    const previewModal = new bootstrap.Modal('#docPreviewModal');

    let documentos = [];
    let clienteId = null;

    document.querySelectorAll('.foto-cliente').forEach(img => {
        img.onclick = () => {
            document.getElementById('fotoModalTitle').innerText = img.dataset.nome;
            document.getElementById('fotoModalImg').src = img.dataset.foto;
            fotoModal.show();
        };
    });

    document.querySelectorAll('.btn-documentos').forEach(btn => {
        btn.onclick = () => {
            documentos = JSON.parse(btn.dataset.documentos);
            clienteId = btn.dataset.cliente;
            document.getElementById('docsModalTitle').innerText =
                `Documentos de ${btn.dataset.nome}`;
            renderDocs();
            docsModal.show();
        };
    });

    document.getElementById('docSearch').oninput = renderDocs;

    function renderDocs() {
        const busca = docSearch.value.toLowerCase();
        const area = document.getElementById('docsContent');
        area.innerHTML = '';

        const grupos = { image:[], pdf:[], other:[] };

        documentos.filter(d => d.nome.toLowerCase().includes(busca))
            .forEach(d => {
                const ext = d.arquivo.split('.').pop().toLowerCase();
                if(['jpg','png','jpeg','gif','webp'].includes(ext)) grupos.image.push(d);
                else if(ext === 'pdf') grupos.pdf.push(d);
                else grupos.other.push(d);
            });

        criarGrupo('Imagens', grupos.image);
        criarGrupo('PDFs', grupos.pdf);
        criarGrupo('Outros', grupos.other);
    }

    function criarGrupo(titulo, docs) {
        if(!docs.length) return;

        const area = document.getElementById('docsContent');
        area.innerHTML += `<h6 class="text-info mt-3">${titulo}</h6>`;

        docs.forEach(doc => {
            area.innerHTML += `
            <div class="d-flex justify-content-between border-bottom py-2">
                <span style="cursor:pointer"
                      onclick="abrirDoc('${doc.nome}','${doc.arquivo}')">
                    📄 ${doc.nome}
                </span>
                <div>

                    <button onclick="excluir(${doc.id})"
                            class="btn btn-sm btn-outline-danger">🗑️</button>
                </div>
            </div>`;
        });
    }

    window.abrirDoc = (nome, arquivo) => {
        document.getElementById('docPreviewTitle').innerText = nome;
        document.getElementById('docPreviewContent').innerHTML =
            arquivo.endsWith('.pdf')
            ? `<iframe src="/storage/${arquivo}" style="width:100%;height:75vh"></iframe>`
            : `<img src="/storage/${arquivo}" style="max-width:100%">`;
        previewModal.show();
    };

    window.renomear = id => {
        const nome = prompt('Novo nome:');
        if(!nome) return;

        fetch(`/clientes/${clienteId}/documentos/${id}/rename`,{
            method:'POST',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
            body:JSON.stringify({nome})
        }).then(() => {
            documentos.find(d=>d.id==id).nome = nome;
            renderDocs();
        });
    };

   window.excluir = async (id) => {
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

        // remove do array local
        documentos = documentos.filter(d => d.id != id);

        // 🔥 FECHA MODAL
        bootstrap.Modal.getInstance(
            document.getElementById('docsModal')
        ).hide();

        // 🔄 ATUALIZA A PÁGINA
        window.location.reload();

    } catch (e) {
        console.error(e);
        alert('Erro de conexão ao excluir');
    }
};


});
</script>
@endsection
