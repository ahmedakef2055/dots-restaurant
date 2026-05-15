@extends('errors::minimal')

@section('title', (app()->getLocale() ?? 'en') === 'ar' ? 'خطأ في الخادم' : 'Server Error')
@section('code', '500')
@section('message', (app()->getLocale() ?? 'en') === 'ar' ? 'خطأ داخلي في الخادم' : 'Server Error')
@section('description', (app()->getLocale() ?? 'en') === 'ar' ? 'عفواً، حدث خطأ غير متوقع في الخادم. يرجى المحاولة مرة أخرى لاحقاً.' : 'Sorry, an unexpected server error occurred. Please try again later.')
