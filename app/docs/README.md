# Implementation Details

## Implement a simple RESTful API managing products
- Basic installation information in [BASE README](../../README.md)

### Freature Requirements/ Functionality
1. Abrufen aller Produkte aus der Tabelle products (Aufgabe 1).
2. Abrufen eines bestimmten Produkts aus der products-Tabelle.
3. Hinzufügen eines neuen Produkts in der Tabelle products.
4. Hinzufügen von mehreren Produkten zur Tabelle products.
5. Aktualisieren eines konkreten Produkts in der Tabelle products.
6. Löschen eines Produkts aus der Tabelle products anhand eines eindeutigen Schlüssels.

### Implementation Notes
- Use Symfony PHP Framework
- API needs to be RESTfull

### Endpoints
|   | URL                                     | METHOD | ACTION                 | Request Body                   | Response Body | Note                                     |
|---|-----------------------------------------|--------|------------------------|--------------------------------|---------------|------------------------------------------|
| 1 | /api/products                           | GET    | List All Products      | none                           | json Data     | ----                                     |
| 2 | /api/products                           | POST   | Add Product / Products | json Data Product or Product[] | json Data     | used for adding one or multiple products |
| 3 | /api/products/{product_id}              | GET    | Show One Product       | none                           | json Data     | ----                                     |
| 4 | /api/products/{product_id}              | DELETE | Delete Product         | none                           | json Data     | ----                                     |
| 5 | /api/products/{product_id}              | PUT    | Update Product         | json Data Product              | json Data     | ----                                     |

## Application Storage
The API app uses MariaDB 10.6 as the storage engine via synfony's doctrine which holds the tables
The reset the data for testing purposes just run 

``docker compose down -v`` and ``docker compose up -d``

### The ``Product`` table holds fields for

| name        | type          | default              | Note           |
|-------------|---------------|----------------------|----------------|
| id          | integer       | not null primary key |                |
| title       | varchar(255)  | not null             | max 255 chars  |
| description | varchar(255)  | not null             | max 255 chars  |
| category    | varchar(255)  | not null             | max 255 chars  |
| state       | boolean       | not null             | max 255 chars  |

