@extends('errors::minimal')

@section('title', (app()->getLocale() ?? 'en') === 'ar' ? 'طلبات كثيرة جداً' : 'Too Many Requests')
@section('code', '429')
@section('message', (app()->getLocale() ?? 'en') === 'ar' ? 'طلبات كثيرة جداً' : 'Too Many Requests')
@section('description', (app()->getLocale() ?? 'en') === 'ar' ? 'لقد تجاوزت الحد المسموح به من الطلبات. يرجى الانتظار قليلاً ثم المحاولة مرة أخرى.' : 'You have made too many requests to the server. Please slow down and try again later.')
