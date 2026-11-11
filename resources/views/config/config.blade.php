@extends('layouts.app')

@section('content')

<form action="{{ route('configuracoes.salvar') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <ul class="nav nav-tabs bg-dark rounded p-2" id="configTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="empresa-tab" data-bs-toggle="tab" data-bs-target="#empresa" type="button" role="tab">Empresa</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="financeiro-tab" data-bs-toggle="tab" data-bs-target="#financeiro" type="button" role="tab">Financeiro</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="notificacoes-tab" data-bs-toggle="tab" data-bs-target="#notificacoes" type="button" role="tab">Notificações</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="clientes-tab" data-bs-toggle="tab" data-bs-target="#clientes" type="button" role="tab">Clientes</button>
        </li>
    </ul>
    
    <div class="tab-content"> 
  <!-- Empresa -->
  <div class="tab-pane fade show active" id="empresa" role="tabpanel">
    <h5>Informações da Empresa</h5>
    <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" value="{{ old('nome', $config['nome'] ?? '') }}">
    </div>
    <div class="mb-3">
        <label for="logo" class="form-label">Logotipo</label>
        <input type="file" class="form-control" id="logo" name="logo">
        
        @if (!empty($config['logo_path']))
            <img src="{{ asset('storage/' . $config['logo_path']) }}" alt="Logo atual" class="img-thumbnail mt-2" style="max-height: 100px;">
        @endif
    </div>
    <div class="mb-3">
        <label for="telefone" class="form-label">Telefone / WhatsApp</label>
        <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $config['telefone'] ?? '') }}">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $config['email'] ?? '') }}">
    </div>
</div>

<!-- Financeiro -->
<div class="tab-pane fade" id="financeiro" role="tabpanel">
    <h5>Configurações Financeiras</h5>
    <div class="mb-3">
        <label for="juros_padrao" class="form-label">Juros Padrão (%)</label>
        <input type="number" name="juros_padrao" class="form-control" value="{{ old('juros_padrao', $config['juros_padrao'] ?? '') }}">
    </div>
    <div class="mb-3">
        <label for="intervalo" class="form-label">Intervalo entre Parcelas (dias)</label>
        <input type="number" name="intervalo" class="form-control" value="{{ old('intervalo', $config['intervalo'] ?? '') }}">
    </div>
    <div class="mb-3">
        <label for="pix" class="form-label">Chave Pix</label>
        <input type="text" name="pix" class="form-control" value="{{ old('pix', $config['pix'] ?? '') }}">
    </div>
</div>

<!-- Notificações -->
<div class="tab-pane fade" id="notificacoes" role="tabpanel">
    <h5>Notificações</h5>
    <div class="form-check form-switch mb-3">
        <input type="checkbox" name="notificacao_email" class="form-check-input" value="1" {{ old('notificacao_email', $config['notificacao_email'] ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="notificacao_email">Enviar lembretes por e-mail</label>
    </div>
    <div class="form-check form-switch mb-3">
        <input type="checkbox" name="notificacao_whatsapp" class="form-check-input" value="1" {{ old('notificacao_whatsapp', $config['notificacao_whatsapp'] ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="notificacao_whatsapp">Enviar por WhatsApp</label>
    </div>
    <div class="mb-3">
        <label for="dias_aviso" class="form-label">Dias antes do vencimento</label>
        <input type="number" class="form-control" id="dias_aviso" name="dias_aviso" value="3">
    </div>
</div>

<!-- Clientes (apenas link) -->
<div class="tab-pane fade" id="clientes" role="tabpanel">
    <h5>Gerenciamento de Clientes</h5>
    <a href="{{ route('clientes.index') }}" class="btn btn-primary">Gerenciar Clientes</a>
</div>


    <div class="text-center mt-4">
        <button type="submit" class="btn btn-success">Salvar</button>
    </div>
</form>

@endsection
