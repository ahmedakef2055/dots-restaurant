@extends('errors::minimal')

@section('title', (app()->getLocale() ?? 'en') === 'ar' ? 'غير مصرح' : 'Forbidden')
@section('code', '403')
@section('message', (app()->getLocale() ?? 'en') === 'ar' ? 'غير مصرح' : 'Forbidden')
@section('description', (app()->getLocale() ?? 'en') === 'ar' ? 'عفواً، لا تملك الصلاحيات الكافية للوصول إلى هذه الصفحة.' : 'Sorry, you do not have permission to access this page.')
