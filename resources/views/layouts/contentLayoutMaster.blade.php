@isset($pageConfigs)
{!! \App\Admin\Helpers\Layout::updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
@php
   $configData = \App\Admin\Helpers\Layout::applClasses();
@endphp

<html lang="@if(session()->has('locale')){{session()->get('locale')}}@else{{$configData['defaultLanguage']}}@endif"
    data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{sc_config('ADMIN_TITLE')}} | {{ $title??'' }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/logo.png">

    {{-- Include core + vendor Styles --}}
    @include('panels/styles')
    @stack('styles')
    {{-- livewire style--}}
    @livewireStyles
</head>



@isset($configData["mainLayoutType"])
@extends((( $configData["mainLayoutType"] === 'horizontal') ? 'layouts.horizontalLayoutMaster' : 'layouts.verticalLayoutMaster' ))
@endisset