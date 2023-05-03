<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Productos</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }
        img{
            height: 5em;
            width: 5em;
        }
    </style>
</head>

<body>
    <h1>Lista de productos</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th style="width: 5em">Precio</th>
                <th>Categoría</th>
                <th>Imágenes</th>
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->status }}</td>
                    <td>L. {{ $product->price }}</td>
                    <td>{{ $product->categoryName }}</td>
                    @if (is_array($product->photos))
                    @foreach ($product->photos as $imageArray)
                        <td>
                            <img src="data:image/{{ $imageArray['type'] }};base64,{{ $imageArray['base64Image'] }}" alt="{{ $imageArray['name'] }}">
                        </td>
                    @endforeach
                    @endif
                    <td><a href="http://localhost:3000/productDetail/{{ $product->id }}">Ver detalles</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>


