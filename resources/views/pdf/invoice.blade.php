<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $order->reference }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2d241e; font-size: 14px; }
        .header { width: 100%; margin-bottom: 40px; border-bottom: 2px solid #ebdaca; padding-bottom: 20px; }
        .logo-text { font-size: 28px; font-weight: bold; color: #c08257; }
        .details { width: 100%; margin-bottom: 40px; border-collapse: collapse; }
        .details td { width: 50%; vertical-align: top; }
        .invoice-title { font-size: 20px; font-weight: bold; margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .table th { background-color: #fef6ea; text-align: left; padding: 12px; border-bottom: 2px solid #ebdaca; }
        .table td { padding: 12px; border-bottom: 1px solid #ebdaca; }
        .totals { width: 100%; border-collapse: collapse; }
        .totals td { text-align: right; padding: 8px; }
        .totals .bold { font-weight: bold; font-size: 16px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #ebdaca; padding-top: 10px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <div class="logo-text">L'Art de la Tresse</div>
                <p>123 Rue de la Coiffure<br>75000 Paris<br>SIRET : 123 456 789 00012</p>
            </td>
            <td style="text-align: right;">
                <h1 class="invoice-title">FACTURE</h1>
                <p><strong>N° :</strong> {{ $order->invoice_number ?? $order->reference }}<br>
                <strong>Date :</strong> {{ $order->created_at->format('d/m/Y') }}</p>
            </td>
        </tr>
    </table>

    <table class="details">
        <tr>
            <td>
                <strong>Facturé à :</strong><br>
                {{ $order->user->name }}<br>
                {{ $order->user->email }}<br>
                {{ $order->user->phone ?? 'N/A' }}
            </td>
            <td style="text-align: right;">
                <strong>Détails du Rendez-vous :</strong><br>
                Date : {{ \Carbon\Carbon::parse($order->availability->date)->format('d/m/Y') }}<br>
                Heure : {{ \Carbon\Carbon::parse($order->availability->start_time)->format('H:i') }}
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Prestation</th>
                <th>Durée</th>
                <th style="text-align: right;">Montant Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->service->name }}</td>
                <td>{{ $order->service->duration_minutes }} min</td>
                <td style="text-align: right;">{{ number_format($order->total_price, 2, ',', ' ') }} €</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td style="width: 70%;"></td>
            <td>Total Prestation :</td>
            <td>{{ number_format($order->total_price, 2, ',', ' ') }} €</td>
        </tr>
        <tr>
            <td></td>
            <td><strong>Montant Réglé ({{ $order->deposit_paid == $order->total_price ? 'Totalité' : 'Acompte' }}) :</strong></td>
            <td class="bold">{{ number_format($order->deposit_paid, 2, ',', ' ') }} €</td>
        </tr>
        <tr>
            <td></td>
            <td><strong>Reste à payer le jour J :</strong></td>
            <td class="bold" style="color: #c08257;">{{ number_format($order->total_price - $order->deposit_paid, 2, ',', ' ') }} €</td>
        </tr>
    </table>

    <div class="footer">
        Merci de votre confiance. L'Art de la Tresse.
    </div>
</body>
</html>