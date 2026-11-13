<?php

namespace App\Console\Commands;

use App\Mail\AvisoVencimento;
use App\Models\Parcela;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EnviarAvisosVencimento extends Command
{
    protected $signature = 'notificacoes:enviar';

    protected $description = 'Envia avisos automáticos de parcelas próximas do vencimento';

    public function handle(WhatsAppService $whatsapp): int
    {
        $config = [];

        if (Storage::disk('local')->exists('configuracoes.json')) {
            $config = json_decode(
                Storage::disk('local')->get('configuracoes.json'),
                true
            ) ?? [];
        }

        $dias = max(0, min(365, (int) ($config['dias_aviso'] ?? 3)));

        $emailAtivo = (bool) ($config['notificacao_email'] ?? false);
        $whatsappAtivo = (bool) ($config['notificacao_whatsapp'] ?? false);

        if (!$emailAtivo && !$whatsappAtivo) {
            $this->info('Notificações desativadas nas configurações.');
            return self::SUCCESS;
        }

        $dataAlvo = Carbon::today()->addDays($dias);

        $this->info(
            'Verificando parcelas com vencimento em ' .
            $dataAlvo->format('d/m/Y')
        );

        $parcelas = Parcela::with([
            'contrato.cliente',
            'pagamentos',
        ])
            ->whereDate('data_vencimento', $dataAlvo->toDateString())
            ->where('status', false)
            ->get();

        $enviadas = 0;

        foreach ($parcelas as $parcela) {
            $cliente = $parcela->contrato?->cliente;

            if (!$cliente) {
                continue;
            }

            $totalPago = (float) $parcela->pagamentos->sum('valor');

            $saldo = round((float) $parcela->valor - $totalPago, 2);

            if ($saldo <= 0.01) {
                continue;
            }

            if ($emailAtivo && $cliente->email) {
                if ($this->reservarEnvio($parcela, 'email', $dataAlvo)) {
                    try {
                        $this->enviarEmail($parcela, $cliente, $saldo);

                        $this->concluirEnvio(
                            $parcela,
                            'email',
                            $dataAlvo
                        );

                        $enviadas++;
                    } catch (Throwable $e) {
                        $this->registrarErro(
                            $parcela,
                            'email',
                            $dataAlvo,
                            $e
                        );
                    }
                }
            }

            if (
                $whatsappAtivo &&
                $cliente->fone &&
                $whatsapp->estaConfigurado()
            ) {
                if ($this->reservarEnvio($parcela, 'whatsapp', $dataAlvo)) {
                    try {
                        $mensagem = "Olá {$cliente->nome}! "
                            . "Lembramos que sua parcela nº {$parcela->numero} "
                            . "vence em "
                            . $parcela->data_vencimento->format('d/m/Y')
                            . ". Saldo em aberto: R$ "
                            . number_format($saldo, 2, ',', '.')
                            . ".";

                        $whatsapp->enviarMensagem(
                            $cliente->fone,
                            $mensagem
                        );

                        $this->concluirEnvio(
                            $parcela,
                            'whatsapp',
                            $dataAlvo
                        );

                        $enviadas++;
                    } catch (Throwable $e) {
                        $this->registrarErro(
                            $parcela,
                            'whatsapp',
                            $dataAlvo,
                            $e
                        );
                    }
                }
            }
        }

        $this->info("Notificações enviadas: {$enviadas}");

        return self::SUCCESS;
    }

    private function enviarEmail($parcela, $cliente, float $saldo): void
    {
        if (config('mail.default') === 'log') {
            throw new \RuntimeException(
                'O e-mail está configurado em modo log. Configure um SMTP real.'
            );
        }

        $diretorio = storage_path('app/public');

        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $pdfPath = $diretorio . "/aviso_{$parcela->id}.pdf";

        $parcelaPdf = clone $parcela;
        $parcelaPdf->valor = $saldo;

        try {
            $pdf = Pdf::loadView('pdf.boleto_pix', [
                'parcela' => $parcelaPdf,
                'cliente' => $cliente,
            ]);

            $pdf->save($pdfPath);

            Mail::to($cliente->email)->send(
                new AvisoVencimento(
                    $parcela,
                    $cliente,
                    $saldo,
                    $pdfPath
                )
            );
        } finally {
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }
    }

    private function reservarEnvio($parcela, string $canal, Carbon $data): bool
    {
        $registro = DB::table('notificacoes_enviadas')
            ->where('parcela_id', $parcela->id)
            ->where('canal', $canal)
            ->where('data_vencimento', $data->toDateString())
            ->first();

        if ($registro && $registro->status === 'enviado') {
            return false;
        }

        if (
            $registro &&
            $registro->status === 'pendente' &&
            Carbon::parse($registro->updated_at)->greaterThan(
                now()->subMinutes(30)
            )
        ) {
            return false;
        }

        DB::table('notificacoes_enviadas')->updateOrInsert(
            [
                'parcela_id' => $parcela->id,
                'canal' => $canal,
                'data_vencimento' => $data->toDateString(),
            ],
            [
                'status' => 'pendente',
                'erro' => null,
                'enviado_em' => null,
                'updated_at' => now(),
                'created_at' => $registro?->created_at ?? now(),
            ]
        );

        return true;
    }

    private function concluirEnvio($parcela, string $canal, Carbon $data): void
    {
        DB::table('notificacoes_enviadas')
            ->where('parcela_id', $parcela->id)
            ->where('canal', $canal)
            ->where('data_vencimento', $data->toDateString())
            ->update([
                'status' => 'enviado',
                'enviado_em' => now(),
                'erro' => null,
                'updated_at' => now(),
            ]);
    }

    private function registrarErro(
        $parcela,
        string $canal,
        Carbon $data,
        Throwable $e
    ): void {
        DB::table('notificacoes_enviadas')
            ->where('parcela_id', $parcela->id)
            ->where('canal', $canal)
            ->where('data_vencimento', $data->toDateString())
            ->update([
                'status' => 'erro',
                'erro' => substr($e->getMessage(), 0, 5000),
                'updated_at' => now(),
            ]);

        Log::error('Falha ao enviar notificação de parcela', [
            'parcela_id' => $parcela->id,
            'canal' => $canal,
            'erro' => $e->getMessage(),
        ]);
    }
}
