<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Parcela</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .box { border: 1px solid #000; padding: 15px; margin: 20px auto; width: 80%; }
    </style>
</head>
<body>
    <h2>Aviso </h2>
    <p>Prezado {{ $cliente->nome }},</p>
    <p>Sua parcela de número <strong>{{ $parcela->numero }}</strong> vence em <strong>{{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}</strong>.</p>
    
    <div class="box">
        <h3>Valor: R$ {{ number_format($parcela->valor, 2, ',', '.') }}</h3>
        <p>Chave Pix: <strong>000.000.000-00</strong></p>
        <p>Ou acesse seu boleto através do link: <a href="#">Clique aqui</a></p>
    </div>

    <p style="font-size: 14px; color: #aaa; text-align: center;">Atenciosamente,<br>Equipe AcervoTech</p></body>
</html>
