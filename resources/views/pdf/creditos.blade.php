<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Créditos Pendientes</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1{
            text-align:center;
            margin-bottom:5px;
        }

        .fecha{
            text-align:right;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        th{
            background:#1e3a8a;
            color:white;
            padding:10px;
            border:1px solid #ddd;
        }

        td{
            padding:8px;
            border:1px solid #ddd;
        }

        .total{
            margin-top:20px;
            text-align:right;
            font-size:16px;
            font-weight:bold;
        }
    </style>
</head>
<body>

    <h1>Reporte de Créditos Pendientes</h1>

    <div class="fecha">
        Fecha: {{ now()->format('d/m/Y H:i') }}
    </div>

    @if($cliente)
        <p><strong>Cliente filtrado:</strong> {{ $cliente }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Fecha Venta</th>
                <th>Monto</th>
            </tr>
        </thead>

        <tbody>
            @foreach($creditos as $credito)
                <tr>
                    <td>{{ $credito->cliente->nombre ?? 'N/A' }}</td>
                    <td>{{ $credito->venta->fecha ?? 'N/A' }}</td>
                    <td>Q {{ number_format($credito->monto, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Adeudado:
        Q {{ number_format($total, 2) }}
    </div>

</body>
</html>