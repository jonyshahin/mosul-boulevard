@component('mail::message')
# {{ $title }}

{{ __('inspection_requests.greeting', ['name' => $name]) }}

@foreach($lines as $line)
{{ $line }}

@endforeach

@isset($actionUrl)
@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
{{ __('inspection_requests.view_request') }}
@endcomponent
@endisset

{{ __('inspection_requests.footer') }}

{{ __('inspection_requests.brand') }}
@endcomponent
