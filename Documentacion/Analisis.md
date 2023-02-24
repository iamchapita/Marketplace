# Análisis de Base de Datos

## Entidades
- Usuario
- Producto
- Direcciones

## Tablas y Atributos
- Usuario:
    - Nombre Completo
    - Correo Electrónico
    - DNI
    - Teléfono
    - Dirección
    - Contraseña
    - Fecha Nacimiento
    - Rol
    - Valoracion

- Producto:
    - Nombre
    - Categoría
    - Precio 
    - Descripción
    - Estado
    - Fotografías
    - FK Usuario (Vendedor)

- Dirección:
    - Departamento
    - Municipio
    - Descripción

- ProductoVendedor
    - FK Usuario
    - FK Producto

- ListaFavorito
    - FK Producto
    - FK Usuario
