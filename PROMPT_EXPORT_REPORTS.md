# 📋 Prompt para Generar Reportes con el Endpoint POST /api/reports/export

## Descripción General
Este endpoint permite generar y descargar reportes en múltiples formatos (PDF, CSV, Excel, HTML) enviando datos personalizados en formato JSON. Es ideal para crear reportes dinámicos con datos que no provienen de una consulta a la base de datos.

---

## 🔧 Especificaciones Técnicas del Endpoint

### URL del Endpoint
```
POST /api/reports/export
```

### Autenticación Requerida
- **Middleware**: JWT Authentication (`jwt.auth`)
- **Middleware adicional**: `SetDbSessionUser` (requiere usuario autenticado)

### Headers Obligatorios
```
Content-Type: application/json
Authorization: Bearer {TU_TOKEN_JWT}
```

---

## 📤 Estructura del Request JSON

### Body del Request
```json
{
  "title": "Nombre del Reporte",
  "headings": ["Encabezado 1", "Encabezado 2", "Encabezado 3"],
  "rows": [
    ["valor1", "valor2", "valor3"],
    ["valor4", "valor5", "valor6"]
  ],
  "format": "pdf"
}
```

### Campos Requeridos

| Campo | Tipo | Descripción | Ejemplo |
|-------|------|-------------|---------|
| `title` | string | Título del reporte que aparecerá en el documento | `"Reporte de Ventas Q4 2025"` |
| `headings` | array | Array de strings con los nombres de las columnas | `["ID", "Producto", "Cantidad", "Total"]` |
| `rows` | array | Array de arrays donde cada array es una fila de datos | `[["1", "Laptop", "5", "$6000"], ["2", "Mouse", "50", "$1250"]]` |
| `format` | string | Formato de salida del reporte | `"pdf"`, `"csv"`, `"excel"`, `"html"` |

---

## ✅ Validaciones Automáticas

El endpoint validará automáticamente que:

1. ✓ El campo `title` es un string no vacío
2. ✓ El campo `headings` es un array de strings
3. ✓ El campo `rows` es un array de arrays (cada fila debe tener el mismo número de columnas)
4. ✓ El número de columnas en cada fila coincide con el número de encabezados
5. ✓ El formato solicitado es uno de los permitidos: `pdf`, `csv`, `excel`, `html`
6. ✓ El usuario está autenticado con un JWT válido

---

## 📊 Ejemplos de Uso Completos

### Ejemplo 1: Reporte de Ventas en PDF

```json
{
  "title": "Reporte de Ventas - Noviembre 2025",
  "headings": ["ID Venta", "Producto", "Cantidad", "Precio Unitario", "Total", "Fecha"],
  "rows": [
    ["001", "Laptop Dell XPS", "2", "$1200", "$2400", "2025-11-20"],
    ["002", "Mouse Logitech", "15", "$25", "$375", "2025-11-21"],
    ["003", "Teclado Mecánico", "8", "$85", "$680", "2025-11-22"],
    ["004", "Monitor LG 27", "3", "$350", "$1050", "2025-11-23"],
    ["005", "Cable HDMI", "20", "$5", "$100", "2025-11-24"]
  ],
  "format": "pdf"
}
```

**Resultado**: Se descargará un archivo `reporte_ventas_*.pdf` con formato profesional y paginado.

---

### Ejemplo 2: Reporte de Usuarios en CSV

```json
{
  "title": "Listado de Usuarios Activos",
  "headings": ["ID", "Nombre Completo", "Email", "Departamento", "Teléfono", "Estado"],
  "rows": [
    ["1001", "Juan Pérez García", "juan.perez@empresa.com", "Ventas", "+34 612345678", "Activo"],
    ["1002", "María López Martínez", "maria.lopez@empresa.com", "Marketing", "+34 623456789", "Activo"],
    ["1003", "Carlos Rodríguez Sánchez", "carlos.rodriguez@empresa.com", "IT", "+34 634567890", "Activo"],
    ["1004", "Ana García Fernández", "ana.garcia@empresa.com", "RRHH", "+34 645678901", "Inactivo"],
    ["1005", "Pedro Martínez López", "pedro.martinez@empresa.com", "Contabilidad", "+34 656789012", "Activo"]
  ],
  "format": "csv"
}
```

**Resultado**: Se descargará un archivo `reporte_usuarios_*.csv` con datos separados por comas.

---

### Ejemplo 3: Reporte de Inventario en Excel

```json
{
  "title": "Inventario de Almacén Central",
  "headings": ["SKU", "Descripción", "Stock Actual", "Stock Mínimo", "Precio", "Última Actualización"],
  "rows": [
    ["SKU-001", "Componente A", "150", "50", "$25.50", "2025-11-26"],
    ["SKU-002", "Componente B", "45", "50", "$30.00", "2025-11-25"],
    ["SKU-003", "Componente C", "320", "100", "$15.75", "2025-11-26"],
    ["SKU-004", "Componente D", "80", "75", "$45.00", "2025-11-24"],
    ["SKU-005", "Componente E", "200", "100", "$12.25", "2025-11-26"]
  ],
  "format": "excel"
}
```

**Resultado**: Se descargará un archivo `reporte_inventario_*.xlsx` con estilos y formato Excel.

---

### Ejemplo 4: Reporte de Rendimiento en HTML

```json
{
  "title": "Reporte de Rendimiento - Equipo de Ventas",
  "headings": ["Vendedor", "Objetivos ($)", "Logrado ($)", "% Cumplimiento", "Comisión ($)"],
  "rows": [
    ["Juan Pérez", "50000", "55000", "110%", "5500"],
    ["María López", "50000", "48000", "96%", "4800"],
    ["Carlos Rodríguez", "50000", "52500", "105%", "5250"],
    ["Ana García", "50000", "60000", "120%", "6000"],
    ["Pedro Martínez", "50000", "45000", "90%", "4500"]
  ],
  "format": "html"
}
```

**Resultado**: Se descargará un archivo `reporte_rendimiento_*.html` que se puede abrir en el navegador.

---

## 🔑 Usando el Token JWT

### Obtener el Token
Primero debes autenticarte en el endpoint de login:

```bash
curl -X POST "https://apitoliboy.lat/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "tu@email.com", "password": "tucontraseña"}'
```

Respuesta:
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### Usar el Token en el Endpoint
```bash
curl -X POST "https://apitoliboy.lat/api/reports/export" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..." \
  -d '{
    "title": "Mi Reporte",
    "headings": ["Col1", "Col2"],
    "rows": [["val1", "val2"]],
    "format": "pdf"
  }' \
  -o reporte.pdf
```

---

## 🌐 Usando desde JavaScript/Fetch

```javascript
// Suponiendo que ya tienes el token guardado
const token = localStorage.getItem('auth_token');

const reportData = {
  title: "Reporte de Ejemplo",
  headings: ["Nombre", "Email", "Estado"],
  rows: [
    ["Juan", "juan@test.com", "Activo"],
    ["María", "maria@test.com", "Activo"],
    ["Carlos", "carlos@test.com", "Inactivo"]
  ],
  format: "pdf"
};

fetch('https://apitoliboy.lat/api/reports/export', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  },
  body: JSON.stringify(reportData)
})
.then(response => response.blob())
.then(blob => {
  // Descargar el archivo
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'reporte.pdf';
  a.click();
  URL.revokeObjectURL(url);
})
.catch(error => console.error('Error:', error));
```

---

## 📋 Usando desde Python

```python
import requests
import json

# Token JWT
token = "eyJ0eXAiOiJKV1QiLCJhbGc..."

# Datos del reporte
report_data = {
    "title": "Reporte Mensual",
    "headings": ["Producto", "Cantidad", "Precio"],
    "rows": [
        ["Producto A", "100", "$50"],
        ["Producto B", "200", "$75"],
        ["Producto C", "150", "$60"]
    ],
    "format": "csv"
}

# Headers
headers = {
    "Content-Type": "application/json",
    "Authorization": f"Bearer {token}"
}

# Realizar la solicitud
response = requests.post(
    'https://apitoliboy.lat/api/reports/export',
    headers=headers,
    json=report_data
)

# Descargar el archivo
if response.status_code == 200:
    with open('reporte.csv', 'wb') as f:
        f.write(response.content)
    print("Reporte descargado exitosamente")
else:
    print(f"Error: {response.status_code}")
    print(response.json())
```

---

## ⚠️ Manejo de Errores

### Errores Comunes

**Error 422 - Validación fallida:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "headings": ["El campo headings debe ser un array."],
    "rows": ["El campo rows debe ser un array de arrays."]
  }
}
```

**Error 401 - No autenticado:**
```json
{
  "message": "Unauthorized"
}
```

**Error 400 - Formato inválido:**
```json
{
  "message": "Invalid format. Allowed formats: pdf, csv, excel, html"
}
```

---

## 🎯 Tips Importantes

1. **Seguridad**: Siempre valida los datos en el servidor antes de enviarlos
2. **Tamaño**: No envíes más de 10,000 filas por reporte (limita según tu servidor)
3. **Caracteres especiales**: Los datos pueden contener cualquier carácter UTF-8
4. **JSON válido**: Asegúrate de que el JSON esté correctamente formateado
5. **Headers coincidentes**: El número de columnas debe coincidir en todas las filas
6. **Formato**: Usa minúsculas para el campo format: `"pdf"`, no `"PDF"`

---

## 🔄 Flujo Completo de Uso

```
1. Usuario se autentica → Recibe JWT token
                    ↓
2. Prepara datos del reporte en JSON
                    ↓
3. Envía POST /api/reports/export con token
                    ↓
4. Backend valida datos
                    ↓
5. Backend genera archivo en formato solicitado
                    ↓
6. Devuelve archivo como blob/binary
                    ↓
7. Cliente descarga el archivo
```

---

## 📞 Resumen Rápido

```
Endpoint: POST /api/reports/export
Auth: JWT Bearer Token
Content-Type: application/json

Body:
{
  "title": "string (requerido)",
  "headings": ["array de strings (requerido)"],
  "rows": [["array de arrays (requerido)"]],
  "format": "pdf|csv|excel|html (requerido)"
}

Response: Binary file (blob) para descargar
```

---

## 💡 Casos de Uso Típicos

- ✅ Generar reportes de ventas mensuales
- ✅ Exportar listados de usuarios
- ✅ Crear reportes de análisis personalizados
- ✅ Generar facturas en PDF
- ✅ Exportar datos de auditoría
- ✅ Crear reportes de inventario
- ✅ Generar certificados/documentos
- ✅ Exportar resultados de búsquedas personalizadas
