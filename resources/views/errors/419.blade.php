@extends('errors.layout')
@section('code', '419')
@section('title', __('Session expired'))
@section('message', __('For your security, this page expired. Please sign in again.'))
@section('primary-action')
    <a href="{{ route('auth.login') }}" class="btn btn-login w-100">@lang('Sign in')</a>
@endsection
