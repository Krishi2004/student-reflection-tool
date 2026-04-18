<x-mail::message>


A student has submitted a reflection and is waiting for your verification. Please click the link below to review. 



<x-mail::button :url="$verification_url" color="success">
    Verify Reflection
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
