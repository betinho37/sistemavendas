<?php

namespace App\Providers;

use App\Models\Auditoria;
use App\Models\Contrato;
use App\Models\Pagamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registrarAuditoria(Contrato::class, 'contratos');
        $this->registrarAuditoria(Pagamento::class, 'pagamentos');
    }

    private function registrarAuditoria(string $model, string $modulo): void
    {
        $model::created(function ($registro) use ($modulo) {
            $this->gravarAuditoria(
                'criacao',
                $modulo,
                $registro,
                null,
                $registro->getAttributes()
            );
        });

        $model::updated(function ($registro) use ($modulo) {
            $alteracoes = $registro->getChanges();

            unset($alteracoes['updated_at']);

            if (empty($alteracoes)) {
                return;
            }

            $anteriores = $registro->getPrevious();

            $dadosAnteriores = [];
            $dadosNovos = [];

            foreach ($alteracoes as $campo => $valor) {
                $dadosAnteriores[$campo] = $anteriores[$campo] ?? null;
                $dadosNovos[$campo] = $valor;
            }

            $this->gravarAuditoria(
                'alteracao',
                $modulo,
                $registro,
                $dadosAnteriores,
                $dadosNovos
            );
        });

        $model::deleted(function ($registro) use ($modulo) {
            $this->gravarAuditoria(
                'exclusao',
                $modulo,
                $registro,
                $registro->getAttributes(),
                null
            );
        });
    }

    private function gravarAuditoria(
        string $acao,
        string $modulo,
        $registro,
        ?array $anteriores,
        ?array $novos
    ): void {
        $usuario = Auth::user();

        Auditoria::create([
            'user_id' => Auth::id() ?? ($registro->user_id ?? null),
            'usuario_nome' => $usuario?->name ?? 'Usuário não identificado',
            'acao' => $acao,
            'modulo' => $modulo,
            'registro_id' => $registro->getKey(),
            'descricao' => ucfirst($acao) . ' de registro em ' . $modulo
                . ' #' . $registro->getKey(),
            'dados_anteriores' => $anteriores,
            'dados_novos' => $novos,
            'ip' => Request::ip(),
        ]);
    }
}
