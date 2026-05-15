@extends('errors::minimal')

@section('title', (app()->getLocale() ?? 'en') === 'ar' ? 'تم القفل' : 'Locked')
@section('code', '423')
@section('message', (app()->getLocale() ?? 'en') === 'ar' ? 'تم قفل المورد' : 'Locked')
@section('description', (app()->getLocale() ?? 'en') === 'ar' ? 'المورد الذي تحاول الوصول إليه مقفل حالياً ولا يمكن تعديله أو قراءته.' : 'The resource that is being accessed is locked.')
