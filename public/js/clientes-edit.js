document.addEventListener("DOMContentLoaded", function () {
    console.log("Script carregado");
    const fotoInput = document.getElementById('foto-input'); // Usando ID para maior especificidade
    const preview = document.getElementById('foto-preview');
    const alertBox = document.querySelector(".alert");

    if (!fotoInput || !preview) {
        console.log("Elementos não encontrados:", { fotoInput, preview });
        return;
    }

    // Preview de imagem inicial
    if (preview.src && preview.style.display !== 'none') {
        preview.style.display = 'block';
    }

    // Preview de imagem ao mudar arquivo
    fotoInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            alert('Por favor, selecione um arquivo de imagem válido.');
        }
    });

    // Remover alerta automaticamente
    if (alertBox) {
        setTimeout(function () {
            alertBox.style.transition = "opacity 0.5s";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 500);
        }, 3000);
    }
});