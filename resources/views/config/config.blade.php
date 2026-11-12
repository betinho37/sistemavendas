
@extends('layouts.app')

@section('title', 'Configurações do Sistema')

@section('content')

<style>
    .config-wrapper {
        color: #e5e7eb;
    }

    .config-header {
        background: linear-gradient(135deg, #111827, #172438);
        border: 1px solid #26364b;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 22px;
    }

    .config-card {
        background: #111827;
        border: 1px solid #293548;
        border-radius: 14px;
        padding: 22px;
        margin-top: 18px;
    }

    .config-card h5 {
        color: #38bdf8;
        margin-bottom: 20px;
    }

    .config-wrapper .form-control {
        background: #0b1220;
        border: 1px solid #344155;
        color: #fff;
    }

    .config-wrapper .form-control:focus {
        background: #0b1220;
        color: #fff;
        border-color: #00b2ff;
        box-shadow: 0 0 0 3px rgba(0,178,255,.12);
    }

    .config-wrapper .form-label {
        color: #cbd5e1;
    }

    .config-wrapper .nav-tabs {
        border-bottom: 1px solid #344155;
        gap: 5px;
    }

    .config-wrapper .nav-link {
        color: #9ca3af;
        border: 0;
        border-radius: 8px;
    }

    .config-wrapper .nav-link.active {
        background: #12314b;
        color: #38bdf8;
    }

    .config-wrapper .form-check-input {
        background-color: #374151;
        border-color: #64748b;
    }

    .config-wrapper .form-check-input:checked {
        background-color: #0284c7;
        border-color: #0284c7;
    }

    .config-hint {
        color: #94a3b8;
        font-size: 13px;
    }

    .config-link {
        background: #0b1220;
        border: 1px solid #334155;
        border-radius: 9px;
        padding: 12px;
        color: #38bdf8;
        word-break: break-all;
    }

    .config-save {
        position: sticky;
        bottom: 12px;
        z-index: 10;
        background: rgba(17,24,39,.96);
        border: 1px solid #334155;
        padding: 14px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }
</style>

<div class="config-wrapper">

    <div class="config-header">
        <h3 class="mb-2">
            <i class="fas fa-sliders-h text-info me-2"></i>
            Configurações
        </h3>

        <p class="text-secondary mb-0">
            Gerencie a empresa, regras financeiras, notificações e segurança do m1r4ge7.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Verifique os campos:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('configuracoes.salvar') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <ul class="nav nav-tabs p-2" id="configTabs" role="tablist">

            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab"
                        data-bs-target="#empresa" type="button">
                    <i class="fas fa-building me-1"></i> Empresa
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                        data-bs-target="#financeiro" type="button">
                    <i class="fas fa-coins me-1"></i> Financeiro
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                        data-bs-target="#notificacoes" type="button">
                    <i class="fas fa-bell me-1"></i> Notificações
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                        data-bs-target="#clientes" type="button">
                    <i class="fas fa-users me-1"></i> Clientes
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"
                        data-bs-target="#acesso" type="button">
                    <i class="fas fa-user-shield me-1"></i> Usuários e acesso
                </button>
            </li>

        </ul>

        <div class="tab-content">

            {{-- EMPRESA --}}
            <div class="tab-pane fade show active" id="empresa">

                <div class="config-card">
                    <h5><i class="fas fa-building me-2"></i>Dados da empresa</h5>

                    <div class="mb-3">
                        <label class="form-label">Nome da empresa</label>
                        <input type="text" name="nome" class="form-control"
                               value="{{ old('nome', $config['nome'] ?? '') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logotipo</label>
                        <input type="file" name="logo" class="form-control"
                               accept="image/*">

                        @if (!empty($config['logo_path']))
                            <img src="{{ asset('storage/'.$config['logo_path']) }}"
                                 class="img-thumbnail mt-3"
                                 style="max-height:100px"
                                 alt="Logo atual">
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone / WhatsApp</label>
                        <input type="text" name="telefone" class="form-control"
                               value="{{ old('telefone', $config['telefone'] ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-mail da empresa</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $config['email'] ?? '') }}">
                    </div>
                </div>
            </div>

            {{-- FINANCEIRO --}}
            <div class="tab-pane fade" id="financeiro">

                <div class="config-card">
                    <h5><i class="fas fa-calculator me-2"></i>Regras financeiras</h5>

                    <div class="mb-3">
                        <label class="form-label">Juros padrão (%)</label>
                        <input type="number" step="0.01" min="0"
                               name="juros_padrao" class="form-control"
                               value="{{ old('juros_padrao', $config['juros_padrao'] ?? 0) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Intervalo entre parcelas (dias)</label>
                        <input type="number" min="1" name="intervalo"
                               class="form-control"
                               value="{{ old('intervalo', $config['intervalo'] ?? 30) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chave Pix</label>
                        <input type="text" name="pix" class="form-control"
                               value="{{ old('pix', $config['pix'] ?? '') }}">
                    </div>

                    <p class="config-hint">
                        Esses valores ficam armazenados como padrões de configuração.
                        A aplicação das regras nos contratos depende dos respectivos controllers.
                    </p>
                </div>
            </div>

            {{-- NOTIFICAÇÕES --}}
            <div class="tab-pane fade" id="notificacoes">

                <div class="config-card">
                    <h5><i class="fas fa-bell me-2"></i>Preferências de notificação</h5>

                    <div class="form-check form-switch mb-4">
                        <input type="checkbox" name="notificacao_email"
                               id="notificacao_email" value="1"
                               class="form-check-input"
                               {{ old('notificacao_email', $config['notificacao_email'] ?? false) ? 'checked' : '' }}>

                        <label class="form-check-label" for="notificacao_email">
                            Habilitar notificações por e-mail
                        </label>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input type="checkbox" name="notificacao_whatsapp"
                               id="notificacao_whatsapp" value="1"
                               class="form-check-input"
                               {{ old('notificacao_whatsapp', $config['notificacao_whatsapp'] ?? false) ? 'checked' : '' }}>

                        <label class="form-check-label" for="notificacao_whatsapp">
                            Habilitar notificações por WhatsApp
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dias antes do vencimento</label>
                        <input type="number" min="0" max="365"
                               name="dias_aviso" class="form-control"
                               value="{{ old('dias_aviso', $config['dias_aviso'] ?? 3) }}">
                    </div>
                </div>
            </div>

            {{-- CLIENTES --}}
            <div class="tab-pane fade" id="clientes">

                <div class="config-card">
                    <h5><i class="fas fa-users me-2"></i>Gerenciamento de clientes</h5>

                    <p class="config-hint">
                        Acesse os cadastros e documentos dos clientes.
                    </p>

                    <a href="{{ route('clientes.index') }}" class="btn btn-info">
                        <i class="fas fa-address-book me-2"></i>
                        Gerenciar clientes
                    </a>
                </div>
            </div>

            {{-- USUÁRIOS E ACESSO --}}
            <div class="tab-pane fade" id="acesso">

                <div class="config-card">
                    <h5><i class="fas fa-user-shield me-2"></i>Controle de cadastro</h5>

                    <div class="form-check form-switch mb-3">
                        <input type="checkbox"
                               name="cadastro_liberado"
                               id="cadastro_liberado"
                               value="1"
                               class="form-check-input"
                               {{ old('cadastro_liberado', $config['cadastro_liberado'] ?? false) ? 'checked' : '' }}>

                        <label class="form-check-label fw-bold"
                               for="cadastro_liberado">
                            Liberar cadastro por link
                        </label>
                    </div>

                    <p class="config-hint">
                        Quando ativado, o sistema disponibiliza um link exclusivo.
                        Quando desativado, o link deixa de funcionar imediatamente.
                    </p>

                    @if (!empty($config['cadastro_liberado']) && !empty($config['cadastro_token']))
                        <div class="mt-4" id="linkCadastroArea">
                            <label class="form-label">
                                Link exclusivo para novos usuários
                            </label>

                            <div class="config-link mb-3" id="linkCadastro">
                                {{ url('/register/'.$config['cadastro_token']) }}
                            </div>

                            <button type="button" class="btn btn-outline-info"
                                    onclick="copiarLinkCadastro()">
                                <i class="fas fa-copy me-2"></i>
                                Copiar link
                            </button>

                            <span id="copiadoMensagem"
                                  class="text-success ms-2"
                                  style="display:none">
                                Link copiado!
                            </span>
                        </div>
                    @else
                        <div class="alert alert-secondary mt-3 mb-0">
                            <i class="fas fa-lock me-2"></i>
                            Cadastro por link desativado.
                        </div>
                    @endif

                    <hr class="border-secondary my-4">

                    <h6 class="text-info">Regras de segurança</h6>

                    <ul class="config-hint">
                        <li>Somente o administrador altera esta configuração.</li>
                        <li>O link exclusivo pode ser revogado desativando o cadastro.</li>
                        <li>Novas contas são criadas sem privilégios administrativos.</li>
                        <li>O administrador permanece conectado após o cadastro de outra pessoa.</li>
                    </ul>
                </div>

                <div class="config-card">
                    <h5><i class="fas fa-shield-alt me-2"></i>Proteção do sistema</h5>

                    <p class="config-hint mb-0">
                        As configurações administrativas são protegidas pelo controle
                        de acesso do usuário administrador.
                    </p>
                </div>

            </div>

        </div>

        <div class="config-save mt-4 text-end">
            <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-save me-2"></i>
                Salvar configurações
            </button>
        </div>

    </form>

</div>

<script>
    function copiarLinkCadastro() {
        const link = document.getElementById('linkCadastro').innerText.trim();

        navigator.clipboard.writeText(link).then(function () {
            const mensagem = document.getElementById('copiadoMensagem');
            mensagem.style.display = 'inline';
            setTimeout(() => mensagem.style.display = 'none', 2500);
        }).catch(function () {
            alert('Não foi possível copiar automaticamente. Selecione e copie o link.');
        });
    }
</script>

@endsection
