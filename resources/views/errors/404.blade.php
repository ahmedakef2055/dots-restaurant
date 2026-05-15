@extends('errors::minimal')

@section('title', (app()->getLocale() ?? 'en') === 'ar' ? 'الصفحة غير موجودة' : 'Not Found')
@section('code', '404')
@section('message', (app()->getLocale() ?? 'en') === 'ar' ? 'الصفحة غير موجودة' : 'Not Found')
@section('description', (app()->getLocale() ?? 'en') === 'ar' ? 'عفواً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها.' : 'Sorry, the page you are looking for could not be found.')
