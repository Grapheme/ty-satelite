<?
/**
 * TITLE: Главная страница
 * AVAILABLE_ONLY_IN_ADVANCED_MODE
 */
?>
@extends(Helper::layout())


@section('style')
@stop


@section('content')

    {{ $page->block('slider') }}

@stop


@section('scripts')
@stop