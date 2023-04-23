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
    </style>
</head>

<body>
    <h1>Lista de productos</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Categoría</th>
                <th>Imágenes</th>
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->categoryName }}</td>
                    <td>No Salen Aun</td>
                    <td><a href="http://localhost:3000/productDetail/{{ $product->id }}">Ver detalles</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>


</html>