
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ConfiguracoesController;
use App\Http\Controllers\ClienteDocumentoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalculadoraController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\FluxoCaixaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
// FLUXO DE CAIXA
Route::get('/fluxo-caixa', [FluxoCaixaController::class, 'index'])
    ->name('fluxo-caixa.index');

Route::post('/fluxo-caixa', [FluxoCaixaController::class, 'store'])
    ->name('fluxo-caixa.store');

Route::delete('/fluxo-caixa/{id}', [FluxoCaixaController::class, 'destroy'])
    ->name('fluxo-caixa.destroy');
    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/dados', [DashboardController::class, 'dados'])
        ->name('dashboard.dados');

    Route::get('/dashboard/cliente/{id}', [DashboardController::class, 'dadosCliente'])
        ->name('dashboard.cliente');

    Route::get('/dashboard/pdf', [DashboardController::class, 'exportarPdf'])
        ->name('dashboard.pdf');

    Route::post('/dashboard/pagar-parcela/{parcela}', [DashboardController::class, 'pagarParcela'])
        ->name('dashboard.pagar-parcela');

    // CALCULADORA
    Route::get('/calculadora', [CalculadoraController::class, 'index'])
        ->name('calculadora.index');

    // CLIENTES
    Route::resource('clientes', ClienteController::class);

    // DOCUMENTOS
    Route::post('/clientes/{clienteId}/documentos', [ClienteDocumentoController::class, 'store'])
        ->name('clientes.documentos.store');

    Route::post('/clientes/{clienteId}/documentos/{id}/rename', [ClienteDocumentoController::class, 'rename'])
        ->name('clientes.documentos.rename');

    Route::delete('/clientes/documentos/{documento}', [ClienteDocumentoController::class, 'destroy'])
        ->name('clientes.documentos.destroy');

    // CONTRATOS
    Route::resource('contratos', ContratoController::class);

    Route::get('/contratos/{id}/gerar-pdf', [ContratoController::class, 'gerarPdf'])
        ->name('contratos.gerarPdf');

    // PARCELAS
    Route::post('/parcelas/{parcela}/pagar', [DashboardController::class, 'pagarParcela'])
        ->name('parcelas.pagar');

    Route::patch('/parcelas/{id}/pagar', [ParcelaController::class, 'marcarComoPaga'])
        ->name('parcelas.marcarComoPaga');

    Route::post('/parcelas/{id}/update', [ContratoController::class, 'updateParcela'])
        ->name('parcelas.update');

    Route::get('/parcelas/vencimentos', [ParcelaController::class, 'vencimentos'])
        ->name('parcelas.vencimentos');

    Route::get('/parcelas/{id}/pdf', [ParcelaController::class, 'gerarBoletoPix'])
        ->name('parcelas.gerarPdf');

    Route::get('/parcelas/{id}/enviar-aviso', [ParcelaController::class, 'enviarAviso'])
        ->name('parcelas.enviarAviso');

    Route::get('/parcelas/{id}/enviar-aviso-pdf', [ParcelaController::class, 'enviarAvisoParcela'])
        ->name('parcelas.enviarAvisoPdf');

    Route::get('/parcelas/{id}/whatsapp', [ParcelaController::class, 'enviarWhatsApp'])
        ->name('parcelas.enviarWhatsApp');

    Route::post('/parcelas/{id}/status', [ParcelaController::class, 'updateStatus'])
        ->name('parcelas.updateStatus');

    Route::put('/parcelas/{id}/status', [ParcelaController::class, 'updateStatus'])
        ->name('parcelas.updateStatus.put');

    // PAGAMENTOS
    Route::post('/parcelas/{parcela}/pagamentos', [PagamentoController::class, 'store'])
        ->name('parcelas.pagamentos.store');

    Route::put('/pagamentos/{pagamento}', [PagamentoController::class, 'update'])
        ->name('pagamentos.update');

    Route::delete('/pagamentos/{pagamento}', [PagamentoController::class, 'destroy'])
        ->name('pagamentos.destroy');

    // PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // CONFIGURAÇÕES
    Route::get('/configuracoes', [ConfiguracoesController::class, 'index'])
        ->name('configuracoes.index');

    Route::post('/configuracoes/salvar', [ConfiguracoesController::class, 'salvar'])
        ->name('configuracoes.salvar');
});

require __DIR__ . '/auth.php';
