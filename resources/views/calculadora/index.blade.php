@extends('layouts.app')

@section('title','Calculadora Financeira')

@section('content')
<div class="container-fluid mt-4">
    <h4 class="mb-4">
        <i class="fas fa-calculator"></i> Calculadora Financeira
    </h4>

    @include('calculadora._calculadora')
</div>
@endsection
