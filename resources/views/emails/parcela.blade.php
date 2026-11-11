<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Aviso de Parcela</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">

        <h2 style="color: #333;">Olá, {{ $cliente->nome }}!</h2>

        <p style="font-size: 16px; color: #555;">
            Sua <strong>parcela de número {{ $parcela->numero }} Valor {{$parcela->valor}}</strong> está prestes a vencer em
            <strong>{{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}</strong>.
        </p>

        <p style="font-size: 16px; color: #555;">
            Segue em anexo o boleto/Pix para pagamento.
        </p>

        <p style="font-size: 16px; color: #555;">
            Se você já efetuou o pagamento, por favor, desconsidere este aviso.
        </p>

        <p style="font-size: 14px; color: #aaa; text-align: center;">Atenciosamente,<br>Equipe AcervoTech</p>

    </div>
</body>
</html>
