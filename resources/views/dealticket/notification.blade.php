<x-mail::message>
    # Deal Ticket Notification

    Hello,

    Your deal ticket (ID: {{ $dealTicket->id }}) has been approved.

    Transport Transaction Details:
    - Amount (in MQ): {{ $transaction->a_mq }}
    - Description:

    <x-mail::button :url="'https://silvergro.co.za/'">
        Log in
    </x-mail::button>

    Kind Regards,<br>
    {{ config('app.name') }}
</x-mail::message>
