<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Invito accesso</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.4;">
    <h2>Invito accesso – Pro Loco Venticanese</h2>

    <p>
        Ciao {{ $member->name }},
    </p>

    <p>
        per completare l’attivazione del tuo account e scegliere la password, clicca qui:
    </p>

    <p>
        <a href="{{ $inviteUrl }}">{{ $inviteUrl }}</a>
    </p>

    <p style="color:#555">
        Il link è valido per 7 giorni e può essere utilizzato una sola volta.
    </p>

    <p>
        Dopo l’accesso potrai usare sia la PWA che il portale:
        <a href="{{ url('/') }}">{{ url('/') }}</a>
    </p>
</body>
</html>


