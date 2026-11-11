@extends('layouts.app')

@section('title', 'Parcelas a Vencer')

@section('content')
<div class="container">
    <h2 class="text-light">Parcelas Vencendo em até 5 Dias</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($parcelas->isEmpty())
    
        <div class="alert alert-warning text-center">Nenhuma parcela vencendo nos próximos 5 dias.</div>
    @else
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parcelas as $parcela)
                    <tr>
                        <td>{{ $parcela->contrato->cliente->nome }}</td>
                        <td>{{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}</td>
                        <td>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                        <td>
                        <a href="{{ route('parcelas.gerarPdf', $parcela->id) }}" 
                        class="btn btn-primary btn-sm">
                            📄 PDF
                        </a>

                        <a href="{{ route('parcelas.enviarAviso', $parcela->id) }}" 
                        class="btn btn-warning btn-sm">
                            📩 E-mail
                        </a>

                        <a href="{{ route('parcelas.enviarAvisoPdf', $parcela->id) }}" 
                        class="btn btn-info btn-sm">
                            📎 E-mail + PDF
                        </a>

                        <a href="{{ route('parcelas.enviarWhatsApp', $parcela->id) }}" 
                        class="btn btn-success btn-sm">
                            📲 WhatsApp
                        </a>
                    </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
