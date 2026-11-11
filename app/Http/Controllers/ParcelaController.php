<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Parcela;
use Illuminate\Support\Facades\Http;
use App\Mail\AvisoVencimento;
use App\Mail\EnviarParcelaMail;

class ParcelaController extends Controller
{
    // ✅ ATUALIZA STATUS (USADO NO SHOW DO CONTRATO)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $parcela = Parcela::findOrFail($id);
        $parcela->status = $request->status;
        $parcela->save();

        return redirect()->back()->with('success', 'Status da parcela atualizado!');
    }

    // ✅ MARCAR COMO PAGA (ATALHO, SE QUISER USAR)
    public function marcarComoPaga($id)
    {
        $parcela = Parcela::findOrFail($id);
        $parcela->status = true;
        $parcela->save();

        return redirect()->back()->with('success', 'Parcela marcada como paga!');
    }

    // 📧 ENVIO DE AVISO COM PDF
    public function enviarAvisoParcela($id)
    {
        $parcela = Parcela::with('contrato.cliente')->findOrFail($id);
        $cliente = $parcela->contrato->cliente;

        if (!$cliente->email) {
            return back()->with('error', 'Cliente não possui e-mail cadastrado.');
        }

        $pdf = Pdf::loadView('pdf.boleto_pix', compact('parcela', 'cliente'));
        $pdfPath = storage_path("app/public/boleto_pix_{$parcela->id}.pdf");
        $pdf->save($pdfPath);

        Mail::to($cliente->email)
            ->send(new EnviarParcelaMail($cliente, $parcela, $pdfPath));

        return back()->with('success', 'Aviso enviado com sucesso!');
    }

    // ⏰ PARCELAS A VENCER
    public function vencimentos()
    {
        $hoje = Carbon::today();
        $limite = $hoje->copy()->addDays(5);

        $parcelas = Parcela::with('contrato.cliente')
            ->whereBetween('data_vencimento', [$hoje, $limite])
            ->where('status', false)
            ->get();

        return view('parcelas.vencimentos', compact('parcelas'));
    }

    // 📄 BOLETO / PIX
    public function gerarBoletoPix($id)
    {
        $parcela = Parcela::with('contrato.cliente')->findOrFail($id);
        $pdf = Pdf::loadView('parcelas.pdf', compact('parcela'));

        return $pdf->stream("boleto_pix_{$parcela->id}.pdf");
    }

    // 📧 AVISO SIMPLES
    public function enviarAviso($id)
    {
        $parcela = Parcela::with('contrato.cliente')->findOrFail($id);

        Mail::to($parcela->contrato->cliente->email)
            ->send(new AvisoVencimento($parcela));

        return back()->with('success', 'Aviso enviado por e-mail!');
    }

    // 📲 WHATSAPP
    public function enviarWhatsApp($id)
    {
        $parcela = Parcela::with('contrato.cliente')->findOrFail($id);
        $cliente = $parcela->contrato->cliente;

        if (!$cliente->fone) {
            return back()->with('error', 'Cliente sem telefone cadastrado.');
        }

        $mensagem = "Olá {$cliente->nome}, sua parcela de R$ "
            . number_format($parcela->valor, 2, ',', '.')
            . " vence em "
            . Carbon::parse($parcela->data_vencimento)->format('d/m/Y');

        return redirect("https://wa.me/55{$cliente->fone}?text=" . urlencode($mensagem));
    }
}
