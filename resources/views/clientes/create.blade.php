@extends('layouts.app')

@section('title', 'Cadastrar Cliente')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4 rounded" style="background: #1e1e1e; border: 1px solid #333; width: 95%; max-width: 1100px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-light">Cadastrar Cliente</h4>
            <button class="btn-close btn-light" onclick="window.history.back();"></button>
        </div>

        <form action="{{ route('clientes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">

                <!-- COLUNA ESQUERDA -->
                <div class="col-md-8">

                    <div class="mb-3">
                        <label class="form-label text-light">Nome Completo:</label>
                        <input type="text" name="nome" class="form-control bg-dark text-light border-0" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Data de Nascimento:</label>
                            <input type="date" name="data_nascimento" class="form-control bg-dark text-light border-0" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Gênero:</label>
                            <select name="genero" class="form-control bg-dark text-light border-0">
                                <option value="">Selecione</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Endereço:</label>
                        <input type="text" name="endereco" class="form-control bg-dark text-light border-0" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Telefone:</label>
                            <input type="text" name="fone" class="form-control bg-dark text-light border-0 phone-mask" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">CPF:</label>
                            <input type="text" name="cpf" class="form-control bg-dark text-light border-0 cpf-mask" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">E-mail:</label>
                        <input type="email" name="email" class="form-control bg-dark text-light border-0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Observações:</label>
                        <textarea name="observacoes" class="form-control bg-dark text-light border-0" rows="3"></textarea>
                    </div>

                </div>

                <!-- COLUNA DIREITA -->
                <div class="col-md-4 text-center">
                    <label class="form-label text-light d-block">Foto do Cliente:</label>

                    <img id="foto-preview" src="https://via.placeholder.com/140x140?text=Foto" width="140" class="rounded mb-2 border">

                    <input type="file" name="foto" id="foto-input" class="form-control bg-dark text-light border-0 mt-2">
                </div>

            </div>

            <div class="d-grid mt-3 mb-4">
                <button type="submit" class="btn btn-primary" style="background: #00AEEF; border: none;">
                    Salvar Cliente
                </button>
            </div>

            <h5 class="text-light mb-3 mt-4">Documentos do Cliente</h5>

            <div id="upload-area" class="p-4 mb-3"
                style="background: rgba(255,255,255,0.02); border: 2px dashed rgba(255,255,255,0.06); border-radius: 10px; text-align: center;">

                <p class="text-light mb-2">Selecione os arquivos (suporta múltiplos)</p>

                <input type="file" name="documentos[]" id="documentos-input" multiple>
                <small class="form-text text-muted d-block mt-2">
                    Arquivos permitidos: PDF, JPG, PNG, JPEG, GIF. Máx 10MB por arquivo.
                </small>
            </div>

            <div id="preview-docs" class="text-light"></div>

        </form>
    </div>
</div>

<script>
// PREVIEW DA FOTO
document.getElementById('foto-input').addEventListener('change', function (event) {
    let reader = new FileReader();
    reader.onload = function () {
        document.getElementById('foto-preview').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
});

// PREVIEW DOS DOCUMENTOS
document.getElementById('documentos-input').addEventListener('change', function(e) {
    let list = document.getElementById('preview-docs');
    list.innerHTML = "";

    for (let file of e.target.files) {
        let item = document.createElement("div");
        item.textContent = "📄 " + file.name;
        list.appendChild(item);
    }
});


// ===== MÁSCARA CPF =====
document.querySelectorAll('.cpf-mask').forEach(input => {
    input.addEventListener('input', () => {
        let v = input.value.replace(/\D/g, '').slice(0, 11);

        if (v.length > 9)
            v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/, '$1.$2.$3-$4');
        else if (v.length > 6)
            v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, '$1.$2.$3');
        else if (v.length > 3)
            v = v.replace(/^(\d{3})(\d{0,3}).*/, '$1.$2');

        input.value = v;
    });
});


// ===== MÁSCARA TELEFONE (CELULAR E FIXO) =====
document.querySelectorAll('.phone-mask').forEach(input => {
    input.addEventListener('input', () => {
        let v = input.value.replace(/\D/g, '').slice(0, 11);

        if (v.length > 10)
            v = v.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
        else if (v.length > 6)
            v = v.replace(/^(\d{2})(\d{4,5})(\d{0,4}).*/, '($1) $2-$3');
        else if (v.length > 2)
            v = v.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
        else
            v = v.replace(/^(\d*)/, '($1');

        input.value = v;
    });
});



</script>

@endsection
