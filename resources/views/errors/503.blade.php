@extends('errors::minimal')

@section('title', (app()->getLocale() ?? 'en') === 'ar' ? 'الخدمة غير متوفرة' : 'Service Unavailable')
@section('code', '503')
@section('message', (app()->getLocale() ?? 'en') === 'ar' ? 'الخدمة غير متوفرة' : 'Service Unavailable')
@section('description', (app()->getLocale() ?? 'en') === 'ar' ? 'نقوم حالياً ببعض أعمال الصيانة لتحديث النظام. سنعود للعمل في أقرب وقت ممكن.' : 'We are currently performing some maintenance on the system. We will be back shortly.')
