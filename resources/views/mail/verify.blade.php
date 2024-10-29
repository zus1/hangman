<x-mail::message>
    Hello {{$nickname}}, please verify you email by clicking on button below.

    <x-mail::button :url="$url">
        Verify
    </x-mail::button>
</x-mail::message>
