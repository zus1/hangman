<x-mail::message>
    Hello {{$nickname}}, you can reset your password by clicking on button below.

    <x-mail::button :url="$url">
        Reset password
    </x-mail::button>
</x-mail::message>
