@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => '#'])
    W8 - Resumo do billing {{$bill['bill_name']}}
@endcomponent
@endslot

# Gráfico último período ({{substr($history[0]['bill_datefrom'],0,7)}})
<img src="{{$remotePath}}" alt="Gráfico" />
<br />
<a href="{{$remotePath}}">link imagem</a>

- Contratado: {{str_pad(LibreNMS\Util\Number::formatSi($history[0]['bill_allowed'],2,3,'bps'), 10, ' ', STR_PAD_LEFT)}}
- Excedente: {{str_pad(LibreNMS\Util\Number::formatSi($history[0]['bill_overuse'],2,3,'bps'), 10, ' ', STR_PAD_LEFT)}}
- Aferido {{str_pad(LibreNMS\Util\Number::formatSi($history[0]['rate_95th'],2,3,'bps'), 10, ' ', STR_PAD_LEFT)}}
@if (preg_match('/cdr/', $bill['bill_type']))
- Método: Percentil {{$percentil}}%
@endif

# Histórico

```
Referência | Tipo | Contratado | Aferido | Excedente

@php
//array_shift($history);

@endphp
@foreach($history as $h)
@php
$metodo = $h['percentil'];
if ($metodo == null) $metodo = '95%';
if (!str_ends_with($metodo, '%')){
    $metodo .= '%';
}
@endphp
@if ($h['bill_overuse'] > 0)
{{substr($h['bill_datefrom'],0,7)}} | {{$metodo}} | {{str_pad(LibreNMS\Util\Number::formatSi($h['bill_allowed'],2,3,'bps'), 10, ' ', STR_PAD_LEFT)}} | {{str_pad(LibreNMS\Util\Number::formatSi($h['rate_95th'],2,3,'bps'), 10,' ', STR_PAD_LEFT)}} | {{str_pad(LibreNMS\Util\Number::formatSi($h['bill_overuse'],2,3,'bps'), 15,' ', STR_PAD_LEFT)}}
@else
{{substr($h['bill_datefrom'],0,7)}} | {{$metodo}} | {{str_pad(LibreNMS\Util\Number::formatSi($h['bill_allowed'],2,3,'bps'), 10, ' ', STR_PAD_LEFT)}} | {{str_pad(LibreNMS\Util\Number::formatSi($h['rate_95th'],2,3,'bps'), 10,' ', STR_PAD_LEFT)}} | {{str_pad('-', 15,' ', STR_PAD_LEFT)}}
@endif
@endforeach
````

---

@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{ 'W8 Telecom'}}. @lang('All rights reserved.')
    @endcomponent
@endslot

@endcomponent
