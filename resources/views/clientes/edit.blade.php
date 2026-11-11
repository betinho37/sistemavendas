
@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Verifique os seguintes erros:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex justify-content-center align-items-start py-4" style="min-height:100vh;">
    <div class="card shadow-lg p-4 rounded"
         style="background:#1e1e1e;border:1px solid #333;width:95%;max-width:1100px;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-light mb-0">Editar Cliente</h4>
            <button type="button" class="btn-close btn-close-white"
                    aria-label="Voltar" onclick="window.history.back()"></button>
        </div>

        {{-- FORMULÁRIO PRINCIPAL --}}
        <form id="form-cliente"
              action="{{ route('clientes.update', $cliente->id) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-8">

                    <div class="mb-3">
                        <label class="form-label text-light">Nome completo:</label>
                        <input type="text" name="nome"
                               class="form-control bg-dark text-light border-secondary"
                               value="{{ old('nome', $cliente->nome) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Data de nascimento:</label>
                            <input type="date" name="data_nascimento"
                                   class="form-control bg-dark text-light border-secondary"
                                   value="{{ old('data_nascimento', $cliente->data_nascimento ? \Illuminate\Support\Carbon::parse($cliente->data_nascimento)->format('Y-m-d') : '') }}"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Gênero:</label>
                            <select name="genero" class="form-select bg-dark text-light border-secondary">
                                <option value="">Selecione</option>
                                @foreach(['Masculino', 'Feminino', 'Outro'] as $genero)
                                    <option value="{{ $genero }}"
                                        {{ old('genero', $cliente->genero) === $genero ? 'selected' : '' }}>
                                        {{ $genero }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Endereço:</label>
                        <input type="text" name="endereco"
                               class="form-control bg-dark text-light border-secondary"
                               value="{{ old('endereco', $cliente->endereco) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Telefone:</label>
                            <input type="text" name="fone"
                                   class="form-control bg-dark text-light border-secondary phone-mask"
                                   value="{{ old('fone', $cliente->fone) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">CPF:</label>
                            <input type="text" name="cpf"
                                   class="form-control bg-dark text-light border-secondary cpf-mask"
                                   value="{{ old('cpf', $cliente->cpf) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">E-mail:</label>
                        <input type="email" name="email"
                               class="form-control bg-dark text-light border-secondary"
                               value="{{ old('email', $cliente->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Observações:</label>
                        <textarea name="observacoes"
                                  class="form-control bg-dark text-light border-secondary"
                                  rows="3">{{ old('observacoes', $cliente->observacoes) }}</textarea>
                    </div>
                </div>

                {{-- FOTO DO CLIENTE --}}
                <div class="col-md-4 text-center">
                    <label class="form-label text-light d-block">Foto do cliente</label>

                    <div class="mb-3">
                        <img id="foto-preview"
                             src="{{ $cliente->foto ? asset('storage/' . ltrim($cliente->foto, '/')) : '' }}"
                             alt="Foto do cliente"
                             class="rounded border border-secondary"
                             style="width:160px;height:160px;object-fit:cover;{{ $cliente->foto ? '' : 'display:none;' }}">

                        <div id="sem-foto" class="text-muted p-4"
                             style="{{ $cliente->foto ? 'display:none;' : '' }}">
                            <span style="font-size:45px;">👤</span>
                            <p class="mb-0">Nenhuma foto cadastrada</p>
                        </div>
                    </div>

                    <input type="file" name="foto" id="foto-input"
                           class="form-control bg-dark text-light border-secondary"
                           accept="image/jpeg,image/png,image/webp">

                    <small class="text-muted d-block mt-2">
                        JPG, PNG ou WEBP. Máximo de 10 MB.
                    </small>
                </div>
            </div>

            <div class="d-grid mt-3 mb-3">
                <button type="submit" class="btn btn-primary"
                        style="background:#00AEEF;border:none;">
                    Salvar alterações
                </button>
            </div>
        </form>

        <hr class="text-light my-4">

        {{-- DOCUMENTOS --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-light mb-0">Documentos do cliente</h5>
            <span class="badge bg-secondary">
                Total: <span id="doc-count">{{ $cliente->documentos->count() }}</span>
            </span>
        </div>

        <div id="upload-area" class="p-4 mb-4"
             style="background:rgba(255,255,255,.02);border:2px dashed #444;border-radius:10px;text-align:center;">
            <div style="font-size:35px;">📁</div>
            <p class="text-light mb-2">Arraste arquivos para esta área ou selecione no computador.</p>
            <p class="text-muted small">Você pode selecionar vários arquivos de uma vez.</p>

            <button type="button" id="btn-select-files" class="btn btn-outline-light btn-sm">
                Selecionar arquivos
            </button>

            <input type="file" id="multi-files" multiple
                   accept=".pdf,.jpg,.jpeg,.png,.gif,.webp" style="display:none;">

            <div id="previews" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 mt-3"></div>

            <div class="mt-3">
                <button type="button" id="btn-upload" class="btn btn-primary" disabled>
                    Enviar selecionados
                </button>
                <button type="button" id="btn-clear-files" class="btn btn-outline-secondary ms-2" disabled>
                    Limpar seleção
                </button>
            </div>

            <small class="form-text text-muted d-block mt-3">
                Permitidos: PDF, JPG, JPEG, PNG, GIF e WEBP. Máximo de 10 MB por arquivo.
            </small>

            <div id="upload-message" class="mt-3"></div>
        </div>

        {{-- DOCUMENTOS EXISTENTES --}}
        <div id="docs-grid" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
            @foreach($cliente->documentos as $doc)
                @php
                    $ext = strtolower(pathinfo($doc->arquivo, PATHINFO_EXTENSION));
                    $caminhoArquivo = ltrim($doc->arquivo, '/');

                    // Evita acrescentar "storage/" duas vezes.
                    if (str_starts_with($caminhoArquivo, 'storage/')) {
                        $urlDocumento = asset($caminhoArquivo);
                    } elseif (str_starts_with($caminhoArquivo, 'clientes/')) {
                        $urlDocumento = asset('storage/' . $caminhoArquivo);
                    } else {
                        $urlDocumento = asset('storage/' . $caminhoArquivo);
                    }
                @endphp

                <div class="col doc-card" id="doc-{{ $doc->id }}" data-id="{{ $doc->id }}">
                    <div class="card bg-dark text-light h-100" style="border:1px solid #333;">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $urlDocumento }}"
                                         alt="{{ $doc->nome }}"
                                         class="rounded"
                                         style="width:70px;height:70px;object-fit:cover;border:1px solid #444;"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="file-icon" style="display:none;">
                                        <strong>IMG</strong>
                                    </div>
                                @elseif($ext === 'pdf')
                                    <div class="file-icon"><strong class="text-danger">PDF</strong></div>
                                @else
                                    <div class="file-icon"><strong>ARQ</strong></div>
                                @endif

                                <div class="flex-grow-1 text-start overflow-hidden">
                                    <div class="fw-bold doc-name text-break" data-id="{{ $doc->id }}">
                                        {{ $doc->nome }}
                                    </div>
                                    <div class="text-muted small">{{ strtoupper($ext) }}</div>
                                    <div class="text-muted small">
                                        {{ $doc->created_at ? $doc->created_at->format('d/m/Y H:i') : '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto d-flex flex-wrap gap-2 justify-content-between">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light open-doc"
                                            data-url="{{ $urlDocumento }}"
                                            data-name="{{ $doc->nome }}"
                                            data-ext="{{ $ext }}">
                                        Abrir
                                    </button>
                                    <button type="button" class="btn btn-outline-light rename-doc"
                                            data-id="{{ $doc->id }}">
                                        Renomear
                                    </button>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger delete-doc"
                                        data-id="{{ $doc->id }}">
                                    Excluir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="docs-empty" class="text-center text-muted py-4"
             style="{{ $cliente->documentos->count() ? 'display:none;' : '' }}">
            Nenhum documento cadastrado para este cliente.
        </div>
    </div>
</div>

{{-- MODAL DE VISUALIZAÇÃO --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-break" id="previewTitle">Visualizar documento</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body text-center" id="previewContent"
                 style="min-height:60vh;overflow:auto;"></div>
            <div class="modal-footer border-secondary">
                <a href="#" id="previewOpenNew" target="_blank" rel="noopener"
                   class="btn btn-outline-light">Abrir em nova aba</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .file-icon {
        width:70px;height:70px;flex-shrink:0;display:flex;
        align-items:center;justify-content:center;border-radius:6px;
        background:#292929;border:1px solid #444;
    }
    .preview-card {
        min-width:0;background:#252525;border:1px solid #444;
        border-radius:8px;padding:10px;height:100%;
    }
    .preview-thumb {
        width:100%;height:115px;object-fit:contain;
        background:#171717;border-radius:5px;
    }
    .preview-filename {
        font-size:12px;overflow-wrap:anywhere;margin-top:8px;color:#f1f1f1;
    }
    #upload-area.dragging {
        border-color:#00AEEF !important;background:rgba(0,174,239,.08) !important;
    }
    .doc-card .card { transition:border-color .2s; }
    .doc-card .card:hover { border-color:#00AEEF !important; }
</style>

<script src="https://unpkg.com/imask"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const clienteId = @json($cliente->id);
    const baseUrl = @json(url('/'));
    const storageUrl = @json(asset('storage'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('multi-files');
    const btnSelect = document.getElementById('btn-select-files');
    const btnUpload = document.getElementById('btn-upload');
    const btnClear = document.getElementById('btn-clear-files');
    const previews = document.getElementById('previews');
    const docsGrid = document.getElementById('docs-grid');
    const docCount = document.getElementById('doc-count');
    const docsEmpty = document.getElementById('docs-empty');
    const uploadMessage = document.getElementById('upload-message');

    const previewModalElement = document.getElementById('previewModal');
    const previewTitle = document.getElementById('previewTitle');
    const previewContent = document.getElementById('previewContent');
    const previewOpenNew = document.getElementById('previewOpenNew');

    const modalPreview = typeof bootstrap !== 'undefined' && bootstrap.Modal
        ? new bootstrap.Modal(previewModalElement)
        : null;

    let arquivos = [];
    const MAX_SIZE = 10 * 1024 * 1024;
    const EXTENSOES_PERMITIDAS = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (caractere) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[caractere];
        });
    }

    function obterExtensao(nome) {
        const partes = String(nome || '').split('.');
        return partes.length > 1 ? partes.pop().toLowerCase() : '';
    }

    /*
     * Converte o caminho retornado pelo Laravel em URL pública.
     * Aceita:
     * clientes/documentos/arquivo.jpg
     * storage/clientes/documentos/arquivo.jpg
     * /storage/clientes/documentos/arquivo.jpg
     * URLs absolutas.
     */
    function normalizarUrlArquivo(caminho) {
        if (!caminho) return '';

        let valor = String(caminho).trim();

        if (/^https?:\/\//i.test(valor)) {
            return valor;
        }

        valor = valor.replace(/\\/g, '/').replace(/^\/+/, '');

        if (valor.startsWith('storage/')) {
            return baseUrl.replace(/\/$/, '') + '/' + valor;
        }

        // Remove prefixos conhecidos caso o backend os tenha incluído.
        valor = valor.replace(/^public\//, '');
        valor = valor.replace(/^app\/public\//, '');

        return storageUrl.replace(/\/$/, '') + '/' + valor;
    }

    function formatBytes(bytes) {
        if (!bytes) return '0 B';

        const unidades = ['B', 'KB', 'MB', 'GB'];
        const indice = Math.min(
            Math.floor(Math.log(bytes) / Math.log(1024)),
            unidades.length - 1
        );

        return (bytes / Math.pow(1024, indice)).toFixed(2) + ' ' + unidades[indice];
    }

    function mostrarMensagem(mensagem, tipo = 'danger') {
        uploadMessage.replaceChildren();

        const alerta = document.createElement('div');
        alerta.className = 'alert alert-' + tipo + ' mb-0';
        alerta.textContent = mensagem;
        uploadMessage.appendChild(alerta);
    }

    function atualizarEstadoBotoes() {
        btnUpload.disabled = arquivos.length === 0;
        btnClear.disabled = arquivos.length === 0;
        docsEmpty.style.display = docsGrid.querySelector('.doc-card') ? 'none' : 'block';
    }

    function adicionarArquivos(lista) {
        uploadMessage.replaceChildren();

        const erros = [];
        let adicionados = 0;

        Array.from(lista || []).forEach(function (arquivo) {
            const extensao = obterExtensao(arquivo.name);

            if (!EXTENSOES_PERMITIDAS.includes(extensao)) {
                erros.push(arquivo.name + ': formato não permitido.');
                return;
            }

            if (arquivo.size > MAX_SIZE) {
                erros.push(arquivo.name + ': ultrapassa 10 MB.');
                return;
            }

            const duplicado = arquivos.some(function (existente) {
                return existente.name === arquivo.name &&
                    existente.size === arquivo.size &&
                    existente.lastModified === arquivo.lastModified;
            });

            if (!duplicado) {
                arquivos.push(arquivo);
                adicionados++;
            }
        });

        atualizarPreviews();

        if (erros.length) {
            mostrarMensagem(erros.join('\n'), 'warning');
            uploadMessage.querySelector('.alert').style.whiteSpace = 'pre-line';
        } else if (adicionados) {
            mostrarMensagem(adicionados + ' arquivo(s) adicionado(s) à seleção.', 'success');
        }
    }

    btnSelect.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        adicionarArquivos(fileInput.files);
        fileInput.value = '';
    });

    uploadArea.addEventListener('dragover', function (event) {
        event.preventDefault();
        uploadArea.classList.add('dragging');
    });

    uploadArea.addEventListener('dragleave', function (event) {
        if (!uploadArea.contains(event.relatedTarget)) {
            uploadArea.classList.remove('dragging');
        }
    });

    uploadArea.addEventListener('drop', function (event) {
        event.preventDefault();
        uploadArea.classList.remove('dragging');
        adicionarArquivos(event.dataTransfer.files);
    });

    function atualizarPreviews() {
        previews.replaceChildren();

        arquivos.forEach(function (arquivo, indice) {
            const extensao = obterExtensao(arquivo.name);
            const coluna = document.createElement('div');
            coluna.className = 'col';

            const cartao = document.createElement('div');
            cartao.className = 'preview-card';

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extensao)) {
                const imagem = document.createElement('img');
                imagem.className = 'preview-thumb';
                imagem.alt = arquivo.name;
                imagem.src = URL.createObjectURL(arquivo);
                cartao.appendChild(imagem);
            } else {
                const icone = document.createElement('div');
                icone.className = 'preview-thumb d-flex align-items-center justify-content-center';

                const texto = document.createElement('strong');
                texto.className = extensao === 'pdf' ? 'text-danger' : 'text-light';
                texto.style.fontSize = '24px';
                texto.textContent = extensao.toUpperCase();

                icone.appendChild(texto);
                cartao.appendChild(icone);
            }

            const nome = document.createElement('div');
            nome.className = 'preview-filename';
            nome.textContent = arquivo.name;

            const tamanho = document.createElement('div');
            tamanho.className = 'text-muted small mt-1';
            tamanho.textContent = formatBytes(arquivo.size);

            const remover = document.createElement('button');
            remover.type = 'button';
            remover.className = 'btn btn-sm btn-outline-danger mt-2';
            remover.textContent = 'Remover';

            remover.addEventListener('click', function () {
                arquivos.splice(indice, 1);
                atualizarPreviews();
            });

            cartao.append(nome, tamanho, remover);
            coluna.appendChild(cartao);
            previews.appendChild(coluna);
        });

        atualizarEstadoBotoes();
    }

    btnClear.addEventListener('click', function () {
        arquivos = [];
        fileInput.value = '';
        previews.replaceChildren();
        uploadMessage.replaceChildren();
        atualizarEstadoBotoes();
    });

    async function lerResposta(resposta) {
        const tipo = resposta.headers.get('content-type') || '';

        if (tipo.includes('application/json')) {
            return await resposta.json();
        }

        const texto = await resposta.text();

        if (!resposta.ok) {
            throw new Error('Erro HTTP ' + resposta.status + '. Verifique a rota e o controller.');
        }

        throw new Error('O servidor não retornou JSON. Verifique a rota e o controller.');
    }

    // UPLOAD
    btnUpload.addEventListener('click', async function () {
        if (!arquivos.length) return;

        const formData = new FormData();
        arquivos.forEach(function (arquivo) {
            formData.append('documentos[]', arquivo);
        });

        btnUpload.disabled = true;
        btnSelect.disabled = true;
        btnClear.disabled = true;
        btnUpload.textContent = 'Enviando...';

        try {
            const resposta = await fetch(
                baseUrl.replace(/\/$/, '') + '/clientes/' + encodeURIComponent(clienteId) + '/documentos',
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                }
            );

            const dados = await lerResposta(resposta);

            if (!resposta.ok || !dados.success) {
                throw new Error(dados.message || dados.error || 'Não foi possível enviar os documentos.');
            }

            const documentosNovos = Array.isArray(dados.documentos) ? dados.documentos : [];
            let adicionados = 0;

            documentosNovos.forEach(function (documento) {
                if (adicionarCardDocumento(documento)) adicionados++;
            });

            arquivos = [];
            previews.replaceChildren();
            fileInput.value = '';

            docCount.textContent = docsGrid.querySelectorAll('.doc-card').length;
            atualizarEstadoBotoes();

            mostrarMensagem(
                dados.message || (adicionados
                    ? 'Documentos enviados com sucesso!'
                    : 'Upload concluído. Atualize a página para conferir os documentos.'),
                'success'
            );
        } catch (erro) {
            console.error('Erro no upload:', erro);
            mostrarMensagem(erro.message || 'Erro ao enviar documentos.');
        } finally {
            btnSelect.disabled = false;
            btnUpload.textContent = 'Enviar selecionados';
            atualizarEstadoBotoes();
        }
    });

    function adicionarCardDocumento(doc) {
        if (!doc || !doc.id || !(doc.arquivo || doc.url)) {
            console.error('Dados incompletos retornados pelo servidor:', doc);
            return false;
        }

        if (document.getElementById('doc-' + doc.id)) return false;

        const caminho = doc.url || doc.arquivo;
        const url = normalizarUrlArquivo(caminho);
        const extensao = obterExtensao(doc.nome_original || doc.arquivo || doc.nome);
        const nome = doc.nome || doc.nome_original || String(doc.arquivo).split('/').pop();

        const coluna = document.createElement('div');
        coluna.className = 'col doc-card';
        coluna.id = 'doc-' + doc.id;
        coluna.dataset.id = doc.id;

        const miniatura = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extensao)
            ? '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(nome) +
              '" class="rounded" style="width:70px;height:70px;object-fit:cover;border:1px solid #444;">'
            : '<div class="file-icon"><strong class="' +
              (extensao === 'pdf' ? 'text-danger' : '') + '">' +
              (extensao === 'pdf' ? 'PDF' : escapeHtml(extensao.toUpperCase() || 'ARQ')) +
              '</strong></div>';

        coluna.innerHTML = `
            <div class="card bg-dark text-light h-100" style="border:1px solid #333;">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        ${miniatura}
                        <div class="flex-grow-1 text-start overflow-hidden">
                            <div class="fw-bold doc-name text-break" data-id="${escapeHtml(doc.id)}">${escapeHtml(nome)}</div>
                            <div class="text-muted small">${escapeHtml(extensao.toUpperCase())}</div>
                            <div class="text-muted small">${escapeHtml(doc.created_at || '')}</div>
                        </div>
                    </div>
                    <div class="mt-auto d-flex flex-wrap gap-2 justify-content-between">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-light open-doc"
                                data-url="${escapeHtml(url)}"
                                data-name="${escapeHtml(nome)}"
                                data-ext="${escapeHtml(extensao)}">Abrir</button>
                            <button type="button" class="btn btn-outline-light rename-doc"
                                data-id="${escapeHtml(doc.id)}">Renomear</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger delete-doc"
                            data-id="${escapeHtml(doc.id)}">Excluir</button>
                    </div>
                </div>
            </div>`;

        docsGrid.prepend(coluna);
        docsEmpty.style.display = 'none';
        return true;
    }

    // ABRIR, EXCLUIR E RENOMEAR
    document.addEventListener('click', async function (event) {
        const botaoAbrir = event.target.closest('.open-doc');
        const botaoExcluir = event.target.closest('.delete-doc');
        const botaoRenomear = event.target.closest('.rename-doc');

        if (botaoAbrir) {
            const url = botaoAbrir.dataset.url;
            const nome = botaoAbrir.dataset.name || 'Documento';
            const extensao = (botaoAbrir.dataset.ext || '').toLowerCase();

            previewTitle.textContent = nome;
            previewContent.replaceChildren();
            previewOpenNew.href = url;

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extensao)) {
                const imagem = document.createElement('img');
                imagem.src = url;
                imagem.alt = nome;
                imagem.style.maxWidth = '100%';
                imagem.style.maxHeight = '70vh';
                imagem.style.objectFit = 'contain';
                imagem.className = 'rounded';

                imagem.onerror = function () {
                    previewContent.textContent =
                        'Não foi possível carregar a imagem. Verifique se o arquivo existe no armazenamento.';
                };

                previewContent.appendChild(imagem);
            } else if (extensao === 'pdf') {
                const iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.title = nome;
                iframe.style.width = '100%';
                iframe.style.height = '70vh';
                iframe.style.border = '0';
                previewContent.appendChild(iframe);
            } else {
                window.open(url, '_blank', 'noopener');
                return;
            }

            if (modalPreview) {
                modalPreview.show();
            } else {
                window.open(url, '_blank', 'noopener');
            }
            return;
        }

        if (botaoExcluir) {
            const id = botaoExcluir.dataset.id;

            if (!confirm('Deseja realmente excluir este documento?')) return;

            botaoExcluir.disabled = true;

            try {
                const resposta = await fetch(
                    baseUrl.replace(/\/$/, '') + '/clientes/documentos/' + encodeURIComponent(id),
                    {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    }
                );

                const dados = await lerResposta(resposta);

                if (!resposta.ok || !dados.success) {
                    throw new Error(dados.message || 'Não foi possível excluir o documento.');
                }

                document.getElementById('doc-' + id)?.remove();
                docCount.textContent = docsGrid.querySelectorAll('.doc-card').length;
                atualizarEstadoBotoes();
                mostrarMensagem('Documento excluído com sucesso!', 'success');
            } catch (erro) {
                console.error('Erro ao excluir documento:', erro);
                mostrarMensagem(erro.message || 'Erro ao excluir documento.');
            } finally {
                botaoExcluir.disabled = false;
            }
            return;
        }

        if (botaoRenomear) {
            const id = botaoRenomear.dataset.id;
            const nomeElemento = document.querySelector(
                '.doc-name[data-id="' + CSS.escape(String(id)) + '"]'
            );
            const nomeAtual = nomeElemento ? nomeElemento.textContent.trim() : '';
            const novoNome = prompt('Digite o novo nome do documento:', nomeAtual);

            if (novoNome === null) return;

            const nomeFinal = novoNome.trim();

            if (!nomeFinal) {
                alert('Digite um nome válido.');
                return;
            }

            const formData = new FormData();
            formData.append('nome', nomeFinal);
            botaoRenomear.disabled = true;

            try {
                const resposta = await fetch(
                    baseUrl.replace(/\/$/, '') + '/clientes/' +
                    encodeURIComponent(clienteId) + '/documentos/' +
                    encodeURIComponent(id) + '/rename',
                    {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    }
                );

                const dados = await lerResposta(resposta);

                if (!resposta.ok || !dados.success) {
                    throw new Error(dados.message || 'Não foi possível renomear o documento.');
                }

                if (nomeElemento) nomeElemento.textContent = nomeFinal;

                const card = document.getElementById('doc-' + id);
                const abrir = card?.querySelector('.open-doc');
                if (abrir) abrir.dataset.name = nomeFinal;

                mostrarMensagem('Documento renomeado com sucesso!', 'success');
            } catch (erro) {
                console.error('Erro ao renomear documento:', erro);
                mostrarMensagem(erro.message || 'Erro ao renomear documento.');
            } finally {
                botaoRenomear.disabled = false;
            }
        }
    });

    // PRÉ-VISUALIZAÇÃO DA FOTO DO CLIENTE
    const fotoInput = document.getElementById('foto-input');
    const fotoPreview = document.getElementById('foto-preview');
    const semFoto = document.getElementById('sem-foto');

    fotoInput.addEventListener('change', function () {
        const arquivo = this.files && this.files[0];
        if (!arquivo) return;

        if (!arquivo.type.startsWith('image/')) {
            alert('Selecione um arquivo de imagem.');
            this.value = '';
            return;
        }

        if (arquivo.size > MAX_SIZE) {
            alert('A foto deve ter no máximo 10 MB.');
            this.value = '';
            return;
        }

        const leitor = new FileReader();

        leitor.onload = function (evento) {
            fotoPreview.src = evento.target.result;
            fotoPreview.style.display = 'inline-block';
            semFoto.style.display = 'none';
        };

        leitor.readAsDataURL(arquivo);
    });

    // MÁSCARAS
    if (typeof IMask !== 'undefined') {
        document.querySelectorAll('.phone-mask').forEach(function (input) {
            IMask(input, {
                mask: [
                    { mask: '(00) 0000-0000' },
                    { mask: '(00) 00000-0000' }
                ]
            });
        });

        document.querySelectorAll('.cpf-mask').forEach(function (input) {
            IMask(input, { mask: '000.000.000-00' });
        });
    }

    atualizarEstadoBotoes();
});
</script>

@endsection
