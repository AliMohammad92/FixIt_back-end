<x-mail::message>
    # Your OTP Code

    Hello,

    Your one-time password (OTP) is:

    ## {{ $otp }} ##

    Please use this code to verify your account.
    This code will expire shortly.

    Thanks.
    {{ config('app.name') }}
</x-mail::message>