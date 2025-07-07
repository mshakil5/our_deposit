@component('mail::message')

<h3>Dear {{$array['user']->name}},</h3>

<p> Welcome to <b>PMK</b> group. You have deposited TK.{{$array['tamnt']}}. Your previous total deposit is TK.{{$array['user']->total_amount}}.</p>

<p><b>PMK</b> group's admin will confim your transaction as soon as possible.</p>

Thanks,<br>
Muhammad Ullah Shakil <br>

@endcomponent