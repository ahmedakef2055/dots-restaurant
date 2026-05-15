@extends('errors::minimal')

@section('title', (app()->getLocale() ?? 'en') === 'ar' ? 'انتهت صلاحية الصفحة' : 'Page Expired')
@section('code', '419')
@section('message', (app()->getLocale() ?? 'en') === 'ar' ? 'انتهت صلاحية الجلسة' : 'Page Expired')
@section('description', (app()->getLocale() ?? 'en') === 'ar' ? 'عفواً، لقد انتهت صلاحية الجلسة الخاصة بك. يرجى إعادة تحديث الصفحة والمحاولة من جديد.' : 'Sorry, your session has expired. Please refresh and try again.')
